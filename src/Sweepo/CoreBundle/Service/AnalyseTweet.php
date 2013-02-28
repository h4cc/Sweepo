<?php

namespace Sweepo\CoreBundle\Service;

use Sweepo\UserBundle\Entity\User;
use Sweepo\StreamBundle\Entity\Tweet;

class AnalyseTweet
{
    private $tweetsSaved;

    public function analyseCollection($tweets, $subscriptions)
    {
        foreach ($tweets as $tweet) {
            $text = strtolower($tweet->text);

            array_walk($subscriptions, function(&$subscription) {
                $subscription = strtolower($subscription);
            });

            foreach ($subscriptions as $subscription) {

                // If the subscription is an @screen_name format
                if (preg_match('/^\@/', $subscription)) {
                    if ($subscription === '@' . strtolower($tweet->user->screen_name)) {
                        $this->addTweet($tweet);
                    }
                // Else we search just the keyword in text
                } else {
                    if (preg_match('/' . $subscription . '/', $text)) {
                        $this->addTweet($tweet);
                    }
                }
            }
        }

        return $this->tweetsSaved;
    }

    public function createTweet($rawTweet)
    {
        $tweet = new Tweet();

        $tweet->setTweetId($rawTweet->id);
        $tweet->setTweetCreatedAt(new \DateTime($rawTweet->created_at));
        $tweet->setInReplyToScreenName($rawTweet->in_reply_to_screen_name); // TODO
        $tweet->setIsRetweeted(false);
        $tweet->setCreatedAt(new \DateTime());
        $tweet->setText($rawTweet->text);
        $tweet->setOwnerId($rawTweet->user->id);
        $tweet->setOwnerName($rawTweet->user->name);
        $tweet->setOwnerScreenName($rawTweet->user->screen_name);
        $tweet->setOwnerProfileImageUrl($rawTweet->user->profile_image_url);

        // If is a retweeted tweet
        if (isset($rawTweet->retweeted_status) && 'RT' === $str = substr($rawTweet->text, 0, 2)) {
            $tweet->setIsRetweeted(true);
            $tweet->setText(substr_replace($rawTweet->text, '', 0, strpos($rawTweet->text, ':') + 2));
            $tweet->setOwnerId($rawTweet->retweeted_status->user->id);
            $tweet->setOwnerName($rawTweet->retweeted_status->user->name);
            $tweet->setOwnerScreenName($rawTweet->retweeted_status->user->screen_name);
            $tweet->setOwnerProfileImageUrl($rawTweet->retweeted_status->user->profile_image_url);
            $tweet->setRawUserScreenName($rawTweet->user->screen_name);
            $tweet->setRawUserName($rawTweet->user->name);
        }

        return $tweet;
    }

    private function addTweet($tweet)
    {
        $this->tweetsSaved[] = $tweet;
    }
}