<?php

namespace Sweepo\HistoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HistoryController extends Controller
{
    /**
     * @Route("/history")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
