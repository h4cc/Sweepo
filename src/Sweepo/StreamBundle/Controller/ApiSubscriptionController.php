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
     * @Route("", name="api_subscriptions", options={"expose"=true})
     * @Method({"GET", "POST", "OPTIONS"})
     */
    public function subscription(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($request->getMethod()) {
            case 'GET':
                $subscriptions = $em->getRepository('SweepoStreamBundle:Subscription')->findBy(['user' => $this->getUser()]);

                if (empty($subscriptions)) {
                    return $this->get('sweepo.api.response')->errorResponse('Subscription not found', ErrorCode::SUBSCRIPTION_NOT_FOUND, 404);
                }

                array_walk($subscriptions, function (Subscription &$subscription) {
                    $subscription = $subscription->toArray();
                });

                return $this->get('sweepo.api.response')->successResponse($subscriptions);
            break;

            case 'POST':
                $subscription = $request->request->get('subscription', null);

                if (null === $subscription) {
                    return $this->get('sweepo.api.response')->errorResponse('Subscription is a missing mandatory parameter', ErrorCode::INVALID_PARAMETER, 400);
                }

                $newSubscription = (new Subscription())
                    ->setSubscription($subscription)
                    ->setType($subscription);

                $user = $this->getUser();
                $user->addSubscription($newSubscription);
                $em->persist($user);
                $em->flush();

                return $this->get('sweepo.api.response')->successResponse($newSubscription->toArray());
            break;
        }
    }

    /**
     * @Route("/{id}", name="api_subscriptions_get", options={"expose"=true})
     * @Method({"GET", "DELETE", "OPTIONS"})
     */
    public function getSubscription(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        if (null === $subscription = $em->getRepository('SweepoStreamBundle:Subscription')->find($id)) {
            return $this->get('sweepo.api.response')->errorResponse('Subscription not found', ErrorCode::SUBSCRIPTION_NOT_FOUND, 404);
        }

        switch ($request->getMethod()) {
            case 'GET':
                return $this->get('sweepo.api.response')->successResponse($subscription->toArray());
            break;

            case 'DELETE':
                $em->remove($subscription);
                $em->flush();

                return $this->get('sweepo.api.response')->successResponse([]);
            break;
        }
    }
}
