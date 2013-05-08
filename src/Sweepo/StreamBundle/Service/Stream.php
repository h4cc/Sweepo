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
        return $this->em->getRepository('SweepoStreamBundle:Tweet')->getStream($user);
    }

    public function fetchTweetsFromTwitter(User $user)
    {
        $subscriptions = $this->em->getRepository('SweepoStreamBundle:Subscription')->findBy(['user' => $user]);

        // By default, the count parameter is 200
        $parameters = ['count' => 200];

        // If we have NOT added a new subscriptions
        // We pass since_id parameter to the query to Twitter to get all tweets since our last loading
        // To improvment the speed of the Twitter query
        if (count($subscriptions) === $user->getNbSubscriptions()) {
            $id = $this->em->getRepository('SweepoStreamBundle:Tweet')->getLastId($user);

            if (null !== $id) {
                $parameters['since_id'] = $id;
            }
        }

        // Here we save the real number of subscriptions
        $user->setNbSubscriptions(count($subscriptions));

        // Twitter request
        $tweetsRetrieved = $this->twitter->get('statuses/home_timeline', $parameters, $user->getToken(), $user->getTokenSecret());
        $tweetsRetrieved = array_reverse($tweetsRetrieved);

        // Get all the tweet_id to check double
        $arrayTweetsId = $this->em->getRepository('SweepoStreamBundle:Tweet')->getTweetId($user);
        $tweetsAnalysed = $this->analyse->analyseTweets($tweetsRetrieved, $subscriptions, $arrayTweetsId);

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