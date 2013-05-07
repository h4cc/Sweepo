<?php

namespace Sweepo\StreamBundle\Tests\Service;

use \stdClass;

use Symfony\Component\Translation\TranslatorInterface;

use Sweepo\StreamBundle\Entity\Subscription;
use Sweepo\StreamBundle\Service\AnalyseTweet;

class AnalyseTweetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testIsSubscribed($subscriptions, $tweet, $exepected)
    {
        // $translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
        //              ->disableOriginalConstructor()
        //              ->getMock();

        $analyse = new AnalyseTweet();
        $answer = $analyse->isSubscribed($subscriptions, $tweet);

        $this->assertEquals($exepected, $answer);
    }

    public function provider()
    {
        return [
            // Test that we find the user type subscription 'foo'
            [[$this->addSubscription('@foo', '@foo')], $this->addTweetFromTwitter('foo', 'bar'), true],
            // Test that we don't find subscription
            [[$this->addSubscription('@foo', '@foo')], $this->addTweetFromTwitter('bar', 'bar'), false],
            // Test we find keyword subscription
            [[$this->addSubscription('bar', 'bar')], $this->addTweetFromTwitter('baz', 'The Bar is better than Foo'), true],
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