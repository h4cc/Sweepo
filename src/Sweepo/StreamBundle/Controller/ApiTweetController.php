<?php

namespace Sweepo\StreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sweepo\CoreBundle\ErrorCode\ErrorCode;
use Sweepo\StreamBundle\Entity\Tweet;

/**
 * @Route("/api/tweets")
 */
class ApiTweetController extends Controller
{
    /**
     * @Route("", name="api_tweets", options={"expose"=true})
     * @Method({"GET", "POST", "OPTIONS"})
     */
    public function tweets(Request $request)
    {
        switch ($request->getMethod()) {
            case 'GET':
                $tweets = $this->get('sweepo.stream')->getStream($this->getUser());

                if (empty($tweets)) {
                    return $this->get('sweepo.api.response')->errorResponse('Tweets not found', ErrorCode::TWEETS_NOT_FOUND, 404);
                }

                array_walk($tweets, function (Tweet &$tweet) {
                    $tweet = $tweet->toArray(false);
                });

                return $this->get('sweepo.api.response')->successResponse($tweets);
            break;

            case 'POST':

            break;
        }
    }

    /**
     * @Route("/refresh", name="api_tweets_refresh", options={"expose"=true})
     * @Method({"GET", "OPTIONS"})
     */
    public function refreshTweetsFromTwitter(Request $request)
    {
        $tweets = $this->get('sweepo.stream')->fetchTweetsFromTwitter($this->getUser());

        if (empty($tweets)) {
            return $this->get('sweepo.api.response')->errorResponse('Tweets not found', ErrorCode::TWEETS_NOT_FOUND, 404);
        }

        array_walk($tweets, function (Tweet &$tweet) {
            $tweet = $tweet->toArray(false);
        });

        return $this->get('sweepo.api.response')->successResponse($tweets);
    }
}
