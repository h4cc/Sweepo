<?php

namespace Sweepo\CoreBundle\Service;

class Twitter
{
    private $consumerKey;
    private $consumerSecret;
    private $session;
    private $request;

    public function __construct($consumerKey, $consumerSecret, $session)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->session = $session;
    }

    public function init()
    {
        $connection = new \TwitterOAuth($this->consumerKey, $this->consumerSecret);
        $request_token = $connection->getRequestToken('http://sweepo.dev/app_dev.php/login-check');

        $this->session->set('oauth_token', $request_token['oauth_token']);
        $this->session->set('oauth_token_secret', $request_token['oauth_token_secret']);

        return $connection->getAuthorizeURL($request_token['oauth_token']);
    }

    public function getAccessToken($oauthVerifier)
    {
        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new \TwitterOAuth($this->consumerKey, $this->consumerSecret, $this->session->get('oauth_token'), $this->session->get('oauth_token_secret'));

        /* Request access tokens from twitter */
        return $connection->getAccessToken($oauthVerifier);
        // $connection = new \TwitterOAuth($this->consumerKey, $this->consumerSecret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    }

    public function get($url, $parameters = [], $oauthToken, $oauthTokenSecret)
    {
        $connection = $this->getConnection($oauthToken, $oauthTokenSecret);
        return $connection->get($url);
    }

    private function getConnection($oauthToken, $oauthTokenSecret)
    {
        return new \TwitterOAuth($this->consumerKey, $this->consumerSecret, $oauthToken, $oauthTokenSecret);
    }
}