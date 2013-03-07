<?php

namespace Sweepo\StreamBundle\Service;

use Symfony\Component\Translation\TranslatorInterface;

use Sweepo\StreamBundle\Entity\Tweet;
use Sweepo\StreamBundle\Entity\Subscription;
use Sweepo\UserBundle\Entity\User;

class AnalyseTweet
{
    private $translator;
    private $tweetsSaved;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
            $text = strtolower($tweet->text);

            foreach ($subscriptions as $subscription) {

                // If the subscription is an @screen_name format
                if ($subscription->getType() === Subscription::TYPE_USER) {

                    if (strtolower($subscription->getSubscription()) === '@' . strtolower($tweet->user->screen_name)) {

                        if (false === $this->alreadyAdded($tweet->id_str, $arrayTweetId)) {
                            $this->addTweet($tweet, $subscription);
                        }
                    }

                // Else we search just the keyword in text
                } else {
                    if (preg_match('/' . strtolower($subscription->getSubscription()) . '/', $text)) {

                        if (false === $this->alreadyAdded($tweet->id_str, $arrayTweetId)) {
                            $this->addTweet($tweet, $subscription);
                        }
                    }
                }
            }
        }

        return $this->tweetsSaved;
    }

    /**
     * Add a new Tweet
     * @param JsonObject $tweet Tweet must to be added
     * @param Subscription $subscription The correspondant subscription
     */
    private function addTweet($tweet, Subscription $subscription)
    {
        $this->tweetsSaved[] = $this->createTweet($tweet, $subscription);
    }

    /**
     * Create a Tweet entity from a Twitter Tweet
     * @param  JsonObject   $rawTweet
     * @param  Subscription $subscription
     * @return Tweet
     */
    private function createTweet($rawTweet, Subscription $subscription)
    {
        $rawTweet = $this->textHandler($rawTweet, $subscription);

        $tweet = new Tweet();

        $tweet->setSubscription($subscription);
        $tweet->setTweetId($rawTweet->id);
        $tweet->setTweetCreatedAt(new \DateTime($rawTweet->created_at));
        $tweet->setInReplyToScreenName($rawTweet->in_reply_to_screen_name); // TODO
        $tweet->setCreatedAt(new \DateTime());
        $tweet->setIsRetweeted(false);
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

    /**
     * Test to check if a tweet has already been added
     * @param  int   $id The tweet id
     * @param  array $arrayTweetId
     * @return boolean
     */
    private function alreadyAdded($id, $arrayTweetId)
    {
        foreach ($arrayTweetId as $tweet_id) {
            if ($id === $tweet_id['tweet_id']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle the text field of a Tweet. Add hightlight for keyword of remove the RT mention for retweeted tweets
     * @param  JsonObject $tweet
     * @param  Subscription $subscription
     * @return JsonObject
     */
    private function textHandler($tweet, Subscription $subscription)
    {
        // Here we remoive the RT mention
        if (true === $this->isRetweeted($tweet)) {
            $tweet->text = substr_replace($tweet->text, '', 0, strpos($tweet->text, ':') + 2);
            $tweet->is_retweeted = true;
        }

        // hightlight the keyword
        if ($subscription->getType() === Subscription::TYPE_KEYWORD) {
            $tweet->text = $this->placeKeywordHightlight($tweet->text, $subscription->getSubscription());
        }

        // Add link
        $tweet->text = $this->searchLinks($tweet->text);

        // Add hashtag
        $tweet->text = $this->searchHashtag($tweet->text);

        // Add mentions
        $tweet->text = $this->searchMentions($tweet->text);

        return $tweet;
    }

    /**
     * Is retweeted ?
     * @param  JsonObject $tweet
     * @return boolean
     */
    private function isRetweeted($tweet)
    {
        if (isset($tweet->retweeted_status)) {
            return true;
        }

        return false;
    }

    // ==== Text Handler ==== //

    private function placeKeywordHightlight($text, $keyword)
    {
        preg_match_all('/' . $keyword . '/', $text, $matches);

        foreach ($matches[0] as $match) {
            $text = substr_replace($text, '<span class="hightlight" data-toggle="tooltip" data-title="' . $this->translator->trans('subscription') . ' : ' . $keyword . '">' . $match . '</span>', strrpos($text, $match), strlen($match));
        }

        return $text;
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
        preg_match_all('/@[a-zA-Z0-9_]+/', $text, $matches);

        foreach ($matches[0] as $mention) {
            $text = substr_replace($text, '<a href="http://twitter.com/' . $mention . '" class="mention">' . $mention . '</a>', strrpos($text, $mention), strlen($mention));
        }

        return $text;
    }

    private function searchLinks($text)
    {
        preg_match_all('/http(s)*:\/\/[.\/a-zA-Z0-9]+/', $text, $matches);

        foreach ($matches[0] as $link) {
            $text = substr_replace($text, '<a href="' . $link . '" class="link">' . $link . '</a>', strrpos($text, $link), strlen($link));
        }

        return $text;
    }
}