<?php

namespace Sweepo\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sweepo\UserBundle\Entity\User;
use Sweepo\UserBundle\Form\UserType;
use Sweepo\UserBundle\Security\Authentication\Token\TwitterUserToken;

class SiteController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('stream'));
        }

        return [];
    }
}
