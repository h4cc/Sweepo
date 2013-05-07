<?php

namespace Sweepo\StreamBundle\Tests\Service;

use \stdClass;

use Symfony\Component\Translation\TranslatorInterface;

use Sweepo\StreamBundle\Entity\Subscription;
use Sweepo\StreamBundle\Service\AnalyseTweet;
use Sweepo\StreamBundle\Service\CreateTweet;

class AnalyseTweetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerIsSubscribed
     */
    public function testIsSubscribed($subscriptions, $tweet, $exepected)
    {
        $createTweet = $this->getMockBuilder('Sweepo\StreamBundle\Service\CreateTweet')
                     ->disableOriginalConstructor()
                     ->getMock();

        $analyse = new AnalyseTweet($createTweet);
        $answer = $analyse->isSubscribed($subscriptions, $tweet);

        $this->assertInstanceOf('Sweepo\StreamBundle\Entity\Subscription', $answer);
        $this->assertEquals($subscriptions[0]->getSubscription(), $answer->getSubscription());
    }

    public function providerIsSubscribed()
    {
        return [
            // Test that we find the user type subscription 'foo'
            [[$this->addSubscription('@foo', '@foo')], $this->addTweetFromTwitter('foo', 'bar'), true],
            // Test we find keyword subscription
            [[$this->addSubscription('bar', 'bar')], $this->addTweetFromTwitter('baz', 'The Bar is better than Foo'), true],
        ];
    }

    /**
     * @dataProvider providerIsNotSubscribed
     */
    public function testIsNotSubscribed($subscriptions, $tweet)
    {
        $createTweet = $this->getMockBuilder('Sweepo\StreamBundle\Service\CreateTweet')
                     ->disableOriginalConstructor()
                     ->getMock();

        $analyse = new AnalyseTweet($createTweet);
        $answer = $analyse->isSubscribed($subscriptions, $tweet);

        $this->assertFalse($answer);
    }

    public function providerIsNotSubscribed()
    {
        return [
            // Test that we don't find subscription
            [[$this->addSubscription('@foo', '@foo')], $this->addTweetFromTwitter('bar', 'bar'), false],
            // Test we don't find keyword subscription
            [[$this->addSubscription('bar', 'bar')], $this->addTweetFromTwitter('foo', 'The Foo is better than Baz'), false],
        ];
    }

    private function addSubscription($subscription, $type)
    {
        return (new Subscription())
            ->setSubscription($subscription)
            ->setType($type);
    }

    private function addTweetFromTwitter($screen_name, $text)
    {
        $user = new stdClass();
        $user->screen_name = $screen_name;

        $tweet = new stdClass();
        $tweet->text = $text;
        $tweet->user = $user;

        return $tweet;
    }
}