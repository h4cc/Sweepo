<?php

namespace Sweepo\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->redirect($this->generateUrl('stream'));
    }

    /**
     * @Route("/login-check", name="login_check")
     * @Template()
     */
    public function loginCheckAction()
    {
        return $this->redirect($this->generateUrl('stream'));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {}
}
