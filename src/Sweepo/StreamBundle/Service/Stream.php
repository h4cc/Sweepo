<?php

namespace Sweepo\StreamBundle\Service;

use Doctrine\ORM\EntityManager;

use Sweepo\UserBundle\Entity\User;
use Sweepo\CoreBundle\Service\Twitter;
use Sweepo\CoreBundle\Service\AnalyseTweet;
use Sweepo\StreamBundle\Entity\Tweet;

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
     * @var Sweepo\CoreBundle\Service\AnalyseTweet
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

        $tweetsRetrieved = $this->twitter->get('statuses/home_timeline', null !== $id ? ['since_id' => $id] : [], $user->getToken(), $user->getTokenSecret());
        $subscriptions = $this->em->getRepository('SweepoStreamBundle:Subscription')->findByKeywords($user);

        foreach ($subscriptions as $subscription) {
            $arraySubscriptions[] = $subscription['subscription'];
        }

        $tweetsAnalysed = $this->analyse->analyseCollection($tweetsRetrieved, $arraySubscriptions);

        if (empty($tweetsAnalysed)) {
            return [];
        }

        foreach ($tweetsAnalysed as $tweet) {
            $newTweet = new Tweet();
            $newTweet->setTweetId($tweet->id)
                ->setText($tweet->text)
                ->setTweetCreatedAt(new \DateTime($tweet->created_at))
                ->setInReplyToScreenName($tweet->in_reply_to_screen_name)
                ->setOwnerId($tweet->user->id)
                ->setOwnerName($tweet->user->name)
                ->setOwnerScreenName($tweet->user->screen_name)
                ->setOwnerProfileImageUrl($tweet->user->profile_image_url)
                ->setIsRetweeted($tweet->retweeted)
                ->setRawUserScreenName(null)
                ->setCreatedAt(new \DateTime());

            $user->addTweet($newTweet);
            $this->em->persist($user);

            $tweetCollection[] = $newTweet;
        }

        $this->em->flush();

        return $tweetCollection;
    }
}