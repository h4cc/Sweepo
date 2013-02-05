<?php

namespace Sweepo\UserBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class TwitterUserToken extends AbstractToken
{
    protected $twitterToken;
    protected $twitterTokenSecret;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return '';
    }

    public function setTwitterToken($twitterToken)
    {
        $this->twitterToken = $twitterToken;
    }

    public function getTwitterToken()
    {
        return $this->twitterToken;
    }

    public function setTwitterTokenSecret($twitterTokenSecret)
    {
        $this->twitterTokenSecret = $twitterTokenSecret;
    }

    public function getTwitterTokenSecret()
    {
        return $this->twitterTokenSecret;
    }
}