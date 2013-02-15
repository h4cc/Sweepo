<?php

namespace Sweepo\StreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sweepo\CoreBundle\ErrorCode\ErrorCode;
use Sweepo\StreamBundle\Entity\Subscription;

/**
 * @Route("/api/subscriptions")
 */
class ApiSubscriptionController extends Controller
{
    /**
     * @Route("/get/{id}", name="api_subscriptions_get")
     * @Method({"GET", "OPTIONS"})
     */
    public function getSubscription(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $subscription = $em->getRepository('SweepoStreamBundle:Subscription')->find($id)) {
            return $this->get('sweepo.api.response')->errorResponse('Subscription not found', ErrorCode::SUBSCRIPTION_NOT_FOUND, 404);
        }

        return $this->get('sweepo.api.response')->successResponse($subscription->toArray());
    }

    /**
     * @Route("/add", name="api_subscriptions_add")
     * @Method({"POST", "OPTIONS"})
     */
    public function addSubscription(Request $request)
    {
        $subscription = $request->request->get('subscription', null);

        if (null === $subscription) {
            return $this->get('sweepo.api.response')->errorResponse('Subscription is a missing mandatory parameter', ErrorCode::INVALID_PARAMETER, 400);
        }

        $newSubscription = (new Subscription())->setSubscription($subscription);

        $em = $this->getDoctrine()->getManager();
        $em->persist($newSubscription);
        $em->flush();

        return $this->get('sweepo.api.response')->successResponse($newSubscription->toArray());
    }
}
