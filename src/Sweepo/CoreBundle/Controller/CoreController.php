<?php

namespace Sweepo\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CoreController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function homeAction()
    {
        return array();
    }
}
