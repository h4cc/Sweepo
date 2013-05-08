<?php

namespace Sweepo\StreamBundle\Tests\Service;

use \stdClass;

use Symfony\Component\Translation\TranslatorInterface;

use Sweepo\StreamBundle\Entity\Subscription;
use Sweepo\StreamBundle\Service\AnalyseTweet;
use Sweepo\StreamBundle\Service\CreateTweet;
use Sweepo\StreamBundle\Entity\Tweet;

class AnalyseTweetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getIsSubscribed
     */
    public function testIsSubscribed($subscriptions, $tweet)
    {
        $createTweet = $this->getMockBuilder('Sweepo\StreamBundle\Service\CreateTweet')
                     ->disableOriginalConstructor()
                     ->getMock();

        $analyse = new AnalyseTweet($createTweet);
        $answer = $analyse->isSubscribed($subscriptions, $tweet);

        $this->assertInstanceOf('Sweepo\StreamBundle\Entity\Subscription', $answer);
    }

    public function getIsSubscribed()
    {
        return [
            // Test that we find the user type subscription 'foo'
            [[$this->addSubscription('@foo', '@foo')], $this->addTweetFromTwitter('foo', 'bar')],
            // Test that we find the user type subscription 'foo'
            [[$this->addSubscription('@foo', '@foo'), $this->addSubscription('@bar', '@bar')], $this->addTweetFromTwitter('bar', 'bar')],
            // Test we find keyword subscription
            [[$this->addSubscription('bar', 'bar')], $this->addTweetFromTwitter('baz', 'The Bar is better than Foo')],
        ];
    }

    /**
     * @dataProvider getIsNotSubscribed
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

    public function getIsNotSubscribed()
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

    /**
     * Here we test we find all the Tweets by Pall Neave (@neave)
     * There are one tweet and one RT
     */
    public function testSearchNeaveTweets()
    {
        $createTweet = $this->getMockBuilder('Sweepo\StreamBundle\Service\CreateTweet')
                     ->disableOriginalConstructor()
                     ->getMock();

        $createTweet->expects($this->any())
                 ->method('createTweet')
                 ->will($this->returnValue('foo'));

        $arrayTweetId = [];
        $subscriptions = [];
        $subscriptionsContent = ['@foo', '@neave'];

        for ($i=0; $i < 2; $i++) {
            $subscriptions[$i] = $this->addSubscription($subscriptionsContent[$i], $subscriptionsContent[$i]);
        }

        $tweets = json_decode(file_get_contents(__DIR__ . '/tweets.json'));

        $analyse = new AnalyseTweet($createTweet);
        $answer = $analyse->analyseTweets($tweets, $subscriptions, $arrayTweetId);

        $this->assertCount(2, $answer);
    }

    /**
     * Here we test we find all the Tweets by Pall Neave (@neave),
     * but there are already added in the database previously.
     * There are one tweet and one RT
     */
    public function testSearchNeaveTweetButThereAreAlreadyAdded()
    {
        $createTweet = $this->getMockBuilder('Sweepo\StreamBundle\Service\CreateTweet')
                     ->disableOriginalConstructor()
                     ->getMock();

        $createTweet->expects($this->any())
                 ->method('createTweet')
                 ->will($this->returnValue('foo'));

        $arrayTweetId = [['tweet_id' => '332077626585935872'], ['tweet_id' => '332079281444372481']];
        $subscriptions = [];
        $subscriptionsContent = ['@foo', '@neave'];

        for ($i=0; $i < 2; $i++) {
            $subscriptions[$i] = $this->addSubscription($subscriptionsContent[$i], $subscriptionsContent[$i]);
        }

        $tweets = json_decode(file_get_contents(__DIR__ . '/tweets.json'));

        $analyse = new AnalyseTweet($createTweet);
        $answer = $analyse->analyseTweets($tweets, $subscriptions, $arrayTweetId);

        $this->assertEmpty($answer);
    }

    /**
     * Here we test if we find keyword subscription 'Montpellier' in an array of tweets
     * There is one tweet
     */
    public function testSearchMontpelierKeyword()
    {
        $tweet = (new Tweet())
            ->setTweetId(331841255455748096);

        $createTweet = $this->getMockBuilder('Sweepo\StreamBundle\Service\CreateTweet')
                     ->disableOriginalConstructor()
                     ->getMock();

        $createTweet->expects($this->any())
                 ->method('createTweet')
                 ->will($this->returnValue($tweet));

        $arrayTweetId = [];
        $subscriptions = [];
        $subscriptionsContent = ['@foo', 'Montpellier'];

        for ($i=0; $i < 2; $i++) {
            $subscriptions[$i] = $this->addSubscription($subscriptionsContent[$i], $subscriptionsContent[$i]);
        }

        $tweets = json_decode(file_get_contents(__DIR__ . '/tweets.json'));

        $analyse = new AnalyseTweet($createTweet);
        $answer = $analyse->analyseTweets($tweets, $subscriptions, $arrayTweetId);

        $this->assertCount(1, $answer);
        $this->assertEquals(331841255455748096, $answer[0]->getTweetId());
    }
}