<?php

namespace Sweepo\StreamBundle\Tests\Service;

use \stdClass;

use Symfony\Component\Translation\TranslatorInterface;

use Sweepo\StreamBundle\Entity\Subscription;
use Sweepo\StreamBundle\Service\CreateTweet;
use Sweepo\StreamBundle\Entity\Tweet;

class CreateTweetTest extends \PHPUnit_Framework_TestCase
{
    private $translator;

    public function setUp()
    {
        $this->translator = $this->getMockTranslator();
    }

    public function testCreateTweet()
    {
        $rawTweet = json_decode($this->getTweet());

        $createTweet = new CreateTweet($this->translator);
        $tweet = $createTweet->createTweet($rawTweet, (new Subscription()));

        $this->assertEquals(331841255455748096, $tweet->getTweetId());
        $this->assertEquals('Opportunité de poste dev UX à Montpellier ! via <a href="http://twitter.com/@foo" class="mention">@foo</a> RT, share, tatouez vous le ! <a href="http://t.co/2O8dBawmjC" class="link">http://t.co/2O8dBawmjC</a> <span class="hashtag">#jobs</span> <span class="hashtag">#cdi</span> <span class="hashtag">#ux</span> <span class="hashtag">#js</span> <span class="hashtag">#montpellier</span>', $tweet->getText());
        $this->assertEquals(83561264, $tweet->getOwnerId());
        $this->assertEquals('Nicolas Chenet', $tweet->getOwnerName());
        $this->assertEquals('nicolaschenet', $tweet->getOwnerScreenName());
    }

    private function getTweet()
    {
        return '{
            "created_at":"Tue May 07 18:41:35 +0000 2013",
            "id":331841255455748096,
            "id_str":"331841255455748096",
            "text":"Opportunit\u00e9 de poste dev UX \u00e0 Montpellier ! via @foo RT, share, tatouez vous le ! http:\/\/t.co\/2O8dBawmjC #jobs #cdi #ux #js #montpellier",
            "source":"web",
            "truncated":false,
            "in_reply_to_status_id":null,
            "in_reply_to_status_id_str":null,
            "in_reply_to_user_id":null,
            "in_reply_to_user_id_str":null,
            "in_reply_to_screen_name":null,
            "user":{
                "id":83561264,
                "id_str":"83561264",
                "name":"Nicolas Chenet",
                "screen_name":"nicolaschenet",
                "location":"Paris, FR",
                "url":"http:\/\/goo.gl\/uaHiB",
                "description":"UX Lead Engineer @ Talkspirit | Front-end coder, and User experience architect  | would like to marry node.js, backbone.js (Yeah that\'s polygamy so what ?)",
                "protected":false,
                "followers_count":289,
                "friends_count":528,
                "listed_count":8,
                "created_at":"Mon Oct 19 09:27:48 +0000 2009",
                "favourites_count":31,
                "utc_offset":3600,
                "time_zone":"Paris",
                "geo_enabled":true,
                "verified":false,
                "statuses_count":1767,
                "lang":"fr",
                "contributors_enabled":false,
                "is_translator":false,
                "profile_background_color":"000000",
                "profile_background_image_url":"http:\/\/a0.twimg.com\/profile_background_images\/632121965\/tq8gl1nisrjys5fxthtd.jpeg",
                "profile_background_image_url_https":"https:\/\/si0.twimg.com\/profile_background_images\/632121965\/tq8gl1nisrjys5fxthtd.jpeg",
                "profile_background_tile":false,
                "profile_image_url":"http:\/\/a0.twimg.com\/profile_images\/2564040084\/bzpnlol1jmlu5kpqdi60_normal.jpeg",
                "profile_image_url_https":"https:\/\/si0.twimg.com\/profile_images\/2564040084\/bzpnlol1jmlu5kpqdi60_normal.jpeg",
                "profile_banner_url":"https:\/\/si0.twimg.com\/profile_banners\/83561264\/1348552020",
                "profile_link_color":"0084B4",
                "profile_sidebar_border_color":"E6E6E6",
                "profile_sidebar_fill_color":"F5F5F5",
                "profile_text_color":"000000",
                "profile_use_background_image":true,
                "default_profile":false,
                "default_profile_image":false,
                "following":true,
                "follow_request_sent":null,
                "notifications":null
            },
            "geo":null,
            "coordinates":null,
            "place":null,
            "contributors":null,
            "retweet_count":3,
            "favorite_count":0,
            "favorited":false,
            "retweeted":false,
            "possibly_sensitive":false,
            "lang":"fr"
        }';
    }

    private function getMockTranslator()
    {
        return $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
                     ->disableOriginalConstructor()
                     ->getMock();
    }
}