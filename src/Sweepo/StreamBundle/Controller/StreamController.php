<?php

namespace Sweepo\StreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Sweepo\StreamBundle\Entity\Tweet;
use Sweepo\StreamBundle\Entity\Subscription;

class StreamController extends Controller
{
    /**
     * @Route("/stream", name="stream")
     * @Template()
     * @Secure("ROLE_USER")
     */
    public function streamAction()
    {
        $em = $this->getDoctrine()->getManager();
        $subscriptions = $em->getRepository('SweepoStreamBundle:Subscription')->findBy(['user' => $this->getUser()]);
        $subscriptionsTypes = Subscription::getTypes();
        $countSubscriptionsType = [];

        foreach ($subscriptionsTypes as $type) {
            $countSubscriptionsType[$type] = 0;
        }

        foreach ($subscriptions as $subscription) {
            $countSubscriptionsType[$subscription->getType()]++;
        }

        return [
            'count_subscription' => $countSubscriptionsType,
            'user'               => $this->getUser(),
        ];
    }
}
