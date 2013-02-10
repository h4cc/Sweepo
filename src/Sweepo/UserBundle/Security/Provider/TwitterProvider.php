<?php

namespace Sweepo\UserBundle\Security\Provider;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Sweepo\UserBundle\Security\Authentication\Token\TwitterUserToken;

class TwitterProvider implements AuthenticationProviderInterface
{
    private $em;

    public function __construct(EntityManager $em, $session)
    {
        $this->em = $em;
    }

    public function authenticate(TokenInterface $twitterUserToken)
    {
        $user = $this->em->getRepository('SweepoUserBundle:User')->findOneBy(['token' => $twitterUserToken->getTwitterToken(), 'token_secret' => $twitterUserToken->getTwitterTokenSecret()]);

        if (null !== $user) {
            $authenticatedToken = new TwitterUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            $authenticatedToken->setLocale($user->getLocal());

            return $authenticatedToken;
        }

        return false;
    }

    protected function validateDigest($digest, $nonce, $created, $secret)
    {

    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof TwitterUserToken;
    }
}