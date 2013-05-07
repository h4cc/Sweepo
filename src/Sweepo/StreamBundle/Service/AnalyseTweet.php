<?php

namespace Sweepo\StreamBundle\Service;

use Sweepo\StreamBundle\Service\CreateTweet;
use Sweepo\StreamBundle\Entity\Subscription;

class AnalyseTweet
{
    private $tweetsSaved;

    public function __construct(CreateTweet $createTweet)
    {
        $this->createTweet = $createTweet;
        $this->tweetsSaved = [];
    }

    /**
     * Analayse a collection of tweets. For each tweets, we check if there is a subscription for it
     * @param  array $tweets        Tweet retrieve from Twitter
     * @param  array $subscriptions The User subscriptions
     * @param  array $arrayTweetId  This is the array of the actuals tweets id. This is use to detect duplicate tweets
     * @return array                The new tweets fetched by the analyse
     */
    public function analyseCollection($tweets, $subscriptions, $arrayTweetId)
    {
        foreach ($tweets as $tweet) {
            $subscription = $this->isSubscribed($subscriptions, $tweet);

            if(!$subscription) {
                continue;
            }

            if (false === $this->isAlreadyAdded($tweet->id_str, $arrayTweetId)) {
                $this->addTweet($tweet, $subscription);
            }
        }

        return $this->tweetsSaved;
    }

    public function isSubscribed($subscriptions, $tweet)
    {
        foreach ($subscriptions as $subscription) {

            switch ($subscription->getType()) {
                case Subscription::TYPE_USER:

                    if (strtolower($subscription->getSubscription()) === '@' . strtolower($tweet->user->screen_name)) {
                        return $subscription;
                    }

                    break;

                case Subscription::TYPE_KEYWORD:
                    $text = strtolower($tweet->text);

                    if (preg_match('/' . strtolower($subscription->getSubscription()) . '/', $text)) {
                        return $subscription;
                    }

                    break;
            }

            return false;
        }
    }

    /**
     * Add a new Tweet
     * @param JsonObject $tweet Tweet must to be added
     * @param Subscription $subscription The correspondant subscription
     */
    private function addTweet($tweet, Subscription $subscription)
    {
        $this->tweetsSaved[] = $this->createTweet->createTweet($tweet, $subscription);
    }

    /**
     * Test to check if a tweet has already been added
     * @param  int   $id The tweet id
     * @param  array $arrayTweetId
     * @return boolean
     */
    private function isAlreadyAdded($id, $arrayTweetId)
    {
        foreach ($arrayTweetId as $tweet_id) {
            if ($id === $tweet_id['tweet_id']) {
                return true;
            }
        }

        return false;
    }
}