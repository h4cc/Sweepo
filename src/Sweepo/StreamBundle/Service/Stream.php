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

    public function getStream(User $user, $sinceId = null)
    {
        return $this->em->getRepository('SweepoStreamBundle:Tweet')->getStream($user, $sinceId);
    }

    public function fetchTweetsFromTwitter(User $user)
    {
        $id = $this->em->getRepository('SweepoStreamBundle:Tweet')->getLastId($user);

        $tweetsRetrieved = $this->twitter->get('statuses/home_timeline', null !== $id ? ['since_id' => $id, 'count' => 200] : ['count' => 200], $user->getToken(), $user->getTokenSecret());
        $tweetsRetrieved = array_reverse($tweetsRetrieved);

        $subscriptions = $this->em->getRepository('SweepoStreamBundle:Subscription')->findByKeywords($user);

        foreach ($subscriptions as $subscription) {
            $arraySubscriptions[] = $subscription['subscription'];
        }

        $tweetsAnalysed = $this->analyse->analyseCollection($tweetsRetrieved, $arraySubscriptions);

        if (empty($tweetsAnalysed)) {
            return [];
        }

        foreach ($tweetsAnalysed as $tweet) {
            $newTweet = $this->analyse->createTweet($tweet);

            $user->addTweet($newTweet);
            $this->em->persist($user);

            $tweetCollection[] = $newTweet;
        }

        $this->em->flush();

        return $tweetCollection;
    }
}