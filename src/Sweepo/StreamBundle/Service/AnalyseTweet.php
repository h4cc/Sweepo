<?php

namespace Sweepo\StreamBundle\Service;

use Sweepo\StreamBundle\Entity\Tweet;
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

    public function createTweet($rawTweet)
    {
        $rawTweet = $this->textHandler($rawTweet);

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
        if (true === $this->isRetweeted($rawTweet)) {
            $tweet->setIsRetweeted(true);
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

    private function textHandler($tweet)
    {
        if (true === $this->isRetweeted($tweet)) {
            $tweet->text = substr_replace($tweet->text, '', 0, strpos($tweet->text, ':') + 2);
        }

        $tweet->text = $this->searchLinks($tweet->text);
        $tweet->text = $this->searchHashtag($tweet->text);
        $tweet->text = $this->searchMentions($tweet->text);

        return $tweet;
    }

    private function isRetweeted($tweet)
    {
        if (isset($tweet->retweeted_status) && 'RT' === $str = substr($tweet->text, 0, 2)) {
            return true;
        }

        return false;
    }

    private function searchHashtag($text)
    {
        preg_match_all('/#[a-zA-Z0-9]+/', $text, $matches);

        foreach ($matches[0] as $hashtag) {
            $text = substr_replace($text, '<span class="hashtag">' . $hashtag . '</span>', strrpos($text, $hashtag), strlen($hashtag));
        }

        return $text;
    }

    private function searchMentions($text)
    {
        preg_match_all('/@[a-zA-Z0-9]+/', $text, $matches);

        foreach ($matches[0] as $mention) {
            $text = substr_replace($text, '<a href="http://twitter.com/' . $mention . '" class="mention">' . $mention . '</a>', strrpos($text, $mention), strlen($mention));
        }

        return $text;
    }

    private function searchLinks($text)
    {
        preg_match_all('/http:\/\/[.\/a-zA-Z0-9]+/', $text, $matches);

        foreach ($matches[0] as $link) {
            $text = substr_replace($text, '<a href="' . $link . '" class="link">' . $link . '</a>', strrpos($text, $link), strlen($link));
        }

        return $text;
    }
}