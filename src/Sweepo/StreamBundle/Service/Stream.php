<?php

namespace Sweepo\StreamBundle\Service;

use Doctrine\ORM\EntityManager;

use Sweepo\UserBundle\Entity\User;
use Sweepo\CoreBundle\Service\Twitter;
use Sweepo\StreamBundle\Service\AnalyseTweet;

class Stream
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Sweepo\CoreBundle\Service\Twitter
     */
    private $twitter;

    /**
     * @var Sweepo\StreamBundle\Service\AnalyseTweet
     */
    private $analyse;

    public function __construct(EntityManager $em, Twitter $twitter, AnalyseTweet $analyse)
    {
        $this->em = $em;
        $this->twitter = $twitter;
        $this->analyse = $analyse;
    }

    public function getStream(User $user)
    {
        $this->fetchTweetsFromTwitter($user);

        return $this->em->getRepository('SweepoStreamBundle:Tweet')->getStream($user);
    }

    public function fetchTweetsFromTwitter(User $user)
    {
        $subscriptions = $this->em->getRepository('SweepoStreamBundle:Subscription')->findBy(['user' => $user]);
        $parameters = ['count' => 200];

        // If we have added a new subscriptions or we have 0 subscriptions
        if (count($subscriptions) === $user->getNbSubscriptions()) {
            $id = $this->em->getRepository('SweepoStreamBundle:Tweet')->getLastId($user);

            if (null !== $id) {
                $parameters['since_id'] = $id;
            }
        }

        $user->setNbSubscriptions(count($subscriptions));
        $tweetsRetrieved = $this->twitter->get('statuses/home_timeline', $parameters, $user->getToken(), $user->getTokenSecret());
        $tweetsRetrieved = array_reverse($tweetsRetrieved);

        // Get all the tweet_id to check double
        $arrayTweetsId = $this->em->getRepository('SweepoStreamBundle:Tweet')->getTweetId($user);
        $tweetsAnalysed = $this->analyse->analyseCollection($tweetsRetrieved, $subscriptions, $arrayTweetsId);

        if (empty($tweetsAnalysed)) {
            $this->em->persist($user);
            $this->em->flush();

            return [];
        }

        foreach ($tweetsAnalysed as $tweet) {
            $user->addTweet($tweet);
            $this->em->persist($user);

            $tweetCollection[] = $tweet;
        }

        $this->em->flush();

        return $tweetCollection;
    }
}