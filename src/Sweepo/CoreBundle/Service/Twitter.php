<?php

namespace Sweepo\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class Twitter
{
    /**
     * The consumer key of Twitter app
     * @var string
     */
    private $consumerKey;

    /**
     * the consumer secret key of Twitter app
     * @var string
     */
    private $consumerSecret;

    /**
     * Session service
     * @var Session
     */
    private $session;

    /**
     * Request service
     * @var Request
     */
    private $request;

    /**
     * @param string  $consumerKey
     * @param string  $consumerSecret
     * @param Session $session         Session service
     */
    public function __construct($consumerKey, $consumerSecret, Session $session)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->session = $session;
    }

    /**
     * Initialization used by the TwitterListener
     * @return  string  The authorized twitter url
     */
    public function init()
    {
        $connection = $this->getConnection();
        $request_token = $connection->getRequestToken('http://sweepo.dev/app_dev.php/login-check');

        $this->session->set('oauth_token', $request_token['oauth_token']);
        $this->session->set('oauth_token_secret', $request_token['oauth_token_secret']);

        return $connection->getAuthorizeURL($request_token['oauth_token']);
    }

    /**
     * Get access token
     * @param  string $oauthVerifier The oauthVerifier parameter returned from Twitter authorization
     * @return array                 The access token
     */
    public function getAccessToken($oauthVerifier)
    {
        $connection = $this->getConnection($this->session->get('oauth_token'), $this->session->get('oauth_token_secret'));
        return $connection->getAccessToken($oauthVerifier);
    }

    /**
     * Get request
     * @param  string      $url              The url of the API REST targeted (example : account/verify_credentials)
     * @param  array       $parameters       The parameters needed by the API
     * @param  string      $oauthToken       User's access token
     * @param  string      $oauthTokenSecret User's access token secret
     * @return std Object                    The return of the API
     */
    public function get($url, $parameters = [], $oauthToken, $oauthTokenSecret)
    {
        $connection = $this->getConnection($oauthToken, $oauthTokenSecret);
        return $connection->get($url);
    }

    /**
     * get Connection
     * @param  string $oauthToken
     * @param  string $oauthTokenSecret
     * @return array
     */
    private function getConnection($oauthToken = null, $oauthTokenSecret = null)
    {
        return new \TwitterOAuth($this->consumerKey, $this->consumerSecret, $oauthToken, $oauthTokenSecret);
    }
}