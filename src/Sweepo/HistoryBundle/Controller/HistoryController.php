<?php

namespace Sweepo\HistoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class HistoryController extends Controller
{
    /**
     * @Route("/history")
     * @Template()
     * @Secure("ROLE_USER")
     */
    public function indexAction()
    {
        return array();
    }
}
