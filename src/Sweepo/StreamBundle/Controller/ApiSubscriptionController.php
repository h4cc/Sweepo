<?php

namespace Sweepo\StreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @Route("/api/subscriptions")
 */
class ApiSubscriptionController extends Controller
{
    /**
     * @Route("/add", name="subscriptions_add")
     * @Secure("ROLE_USER")
     */
    public function addAction()
    {
        die(var_dump('ok'));

        return array();
    }
}
