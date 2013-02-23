<?php

namespace Sweepo\CoreBundle\Service;

use Sweepo\UserBundle\Entity\User;

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

    private function addTweet($tweet)
    {
        $this->tweetsSaved[] = $tweet;
    }


}