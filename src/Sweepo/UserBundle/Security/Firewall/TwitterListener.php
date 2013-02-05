<?php

namespace Sweepo\UserBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Sweepo\UserBundle\Security\Authentication\Token\TwitterUserToken;
use Sweepo\UserBundle\Security\Provider\TwitterProvider;
use Sweepo\CoreBundle\Service\Twitter;

class TwitterListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    private $twitter;
    private $session;
    private $container;
    private $twitterProvider;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, Twitter $twitter, $session, ContainerInterface $container, TwitterProvider $twitterProvider)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;

        $this->twitter = $twitter;
        $this->session = $session;
        $this->container = $container;
        $this->twitterProvider = $twitterProvider;
    }

    public function handle(GetResponseEvent $event)
    {
        if (null !== $this->securityContext->getToken() || ($event->getRequest()->attributes->get('_route') !== 'login' && $event->getRequest()->attributes->get('_route') !== 'login_check')) {
            return;
        }

        switch ($event->getRequest()->attributes->get('_route')) {
            case 'login':
                $event->setResponse(new RedirectResponse($this->twitter->init()));
            break;

            case 'login_check':
                $oauthVerifier = $this->container->get('request')->get('oauth_verifier');

                if (null === $oauthVerifier) {
                    // TODO
                }

                $accessToken = $this->twitter->getAccessToken($oauthVerifier);

                $twitterUserToken = new TwitterUserToken();
                $twitterUserToken->setTwitterToken($accessToken['oauth_token']);
                $twitterUserToken->setTwitterTokenSecret($accessToken['oauth_token_secret']);

                if (false !== $authToken = $this->twitterProvider->authenticate($twitterUserToken)) {
                    $this->securityContext->setToken($authToken);

                    return;
                }

                $event->setResponse(new RedirectResponse($this->container->get('router')->generate('create', ['oauth_token' => $accessToken['oauth_token'], 'oauth_token_secret' => $accessToken['oauth_token_secret']])));

            break;
        }
    }
}