<?php

namespace Sweepo\CoreBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * This listener is used to set Local on session and request
 * It's called all time
 */
class LocaleListener
{
    /**
     * @var Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @param Session                  $session
     * @param Request                  $request
     * @param SecurityContextInterface $security
     */
    public function __construct(Session $session, Request $request, SecurityContextInterface $security)
    {
        $this->session = $session;
        $this->request = $request;
        $this->security = $security;
    }

    /**
     * @param  GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (null === $this->session->get('_locale')) {

            if (null !== $this->security->getToken() && $this->security->isGranted('ROLE_USER')) {
                $this->setSession($this->security->getToken()->getUser()->getLocal());
                $this->setRequest($this->security->getToken()->getUser()->getLocal());

                return;
            }

            $this->setSession($this->request->getLocale());

            return;
        }

        if ($this->session->get('_locale')) {
            $this->request->setLocale($this->session->get('_locale'));

            return;
        }
    }

    /**
     * Set the _locale in session
     * @param string $locale
     */
    private function setSession($locale)
    {
        $this->session->set('_locale', $locale);
    }

    /**
     * Set the _locale in request
     * @param string $locale
     */
    private function setRequest($locale)
    {
        $this->request->setLocale($locale);
    }
}