<?php

namespace Sweepo\UserBundle\Security\Firewall;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;

class LogoutHandler implements LogoutSuccessHandlerInterface
{
    private $router;
    private $security;

    public function __construct(Router $router, SecurityContext $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function onLogoutSuccess(Request $request)
    {
        $this->security->setToken(null);
        $request->getSession()->invalidate();
        return new RedirectResponse($request->server->get('HTTP_REFERER', $this->router->generate('index')));
    }
}