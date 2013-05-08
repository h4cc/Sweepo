<?php

namespace Sweepo\StreamBundle\Service;

use Symfony\Component\Translation\TranslatorInterface;

use Sweepo\StreamBundle\Entity\Tweet;
use Sweepo\StreamBundle\Entity\Subscription;
use Sweepo\UserBundle\Entity\User;

class CreateTweet
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Create a Tweet entity from a Twitter Tweet
     * @param  JsonObject   $rawTweet
     * @param  Subscription $subscription
     * @return Tweet
     */
    public function createTweet($rawTweet, Subscription $subscription)
    {
        $rawTweet = $this->textHandler($rawTweet, $subscription);

        $tweet = (new Tweet())
            ->setSubscription($subscription)
            ->setTweetId($rawTweet->id)
            ->setTweetCreatedAt($this->getCreatedAtDateTimeFormat($rawTweet->created_at))
            ->setInReplyToScreenName($rawTweet->in_reply_to_screen_name) // TODO
            ->setCreatedAt(new \DateTime())
            ->setIsRetweeted(false)
            ->setText($rawTweet->text)
            ->setOwnerId($rawTweet->user->id)
            ->setOwnerName($rawTweet->user->name)
            ->setOwnerScreenName($rawTweet->user->screen_name)
            ->setOwnerProfileImageUrl($rawTweet->user->profile_image_url);

        // If is a retweeted tweet
        if (true === $this->isRetweeted($rawTweet)) {
            $tweet->setIsRetweeted(true)
                ->setOwnerId($rawTweet->retweeted_status->user->id)
                ->setOwnerName($rawTweet->retweeted_status->user->name)
                ->setOwnerScreenName($rawTweet->retweeted_status->user->screen_name)
                ->setOwnerProfileImageUrl($rawTweet->retweeted_status->user->profile_image_url)
                ->setRawUserScreenName($rawTweet->user->screen_name)
                ->setRawUserName($rawTweet->user->name);
        }

        return $tweet;
    }

    /**
     * Create correct DateTime format
     * @param  string $tweetDateTime the Tweet datetime from Twitter
     * @return DateTime
     */
    private function getCreatedAtDateTimeFormat($tweetDateTime)
    {
        $datetime = new \DateTime($tweetDateTime);
        $datetime->add(new \DateInterval('PT2H'));

        return $datetime;
    }

    /**
     * Handle the text field of a Tweet. Add hightlight for keyword of remove the RT mention for retweeted tweets
     * @param  JsonObject $tweet
     * @param  Subscription $subscription
     * @return JsonObject
     */
    private function textHandler($tweet, Subscription $subscription)
    {
        // Here we remove the RT mention
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