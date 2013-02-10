<?php

namespace Sweepo\CoreBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class LocaleListener
{
    private $session;
    private $request;
    private $security;

    public function __construct($session, Request $request, SecurityContextInterface $security)
    {
        $this->session = $session;
        $this->request = $request;
        $this->security = $security;
    }

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

    private function setSession($locale)
    {
        $this->session->set('_locale', $locale);
    }

    private function setRequest($locale)
    {
        $this->request->setLocale($locale);
    }
}