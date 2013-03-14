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
        if (null !== $this->get('security.context')->getToken()) {
            return $this->redirect($this->generateUrl('stream'));
        }

        return [];
    }

    /**
     * @Route("/create", name="create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        if (null !== $this->get('security.context')->getToken()) {
            return $this->redirect($this->generateUrl('stream'));
        }

        if (null === $request->query->get('oauth_token') || null === $request->query->get('oauth_token_secret')) {
            $this->get('session')->getFlashBag()->add('error', 'error');

            return $this->redirect($this->generateUrl('index'));
        }

        $user = new User();

        if ($request->getMethod() === 'GET') {
            $informations = $this->get('sweepo.twitter')->get('account/verify_credentials', [], $request->query->get('oauth_token'), $request->query->get('oauth_token_secret'));
            $user->setToken($request->query->get('oauth_token'));
            $user->setTokenSecret($request->query->get('oauth_token_secret'));
            $user->setName($informations->name);
            $user->setScreenName($informations->screen_name);
            $user->setTwitterId($informations->id);
            $user->setProfileImageUrl($informations->profile_image_url);
            $user->setLocal($informations->lang);
        }

        $form = $this->createForm(new UserType(), $user);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $twitterUserToken = new TwitterUserToken($user->getRoles());
                $twitterUserToken->setUser($user);
                $twitterUserToken->setLocale($user->getLocal());
                $this->get('security.context')->setToken($twitterUserToken);
                $this->get('session')->set('_locale', $user->getLocal());

                return $this->redirect($this->generateUrl('stream'));
            }
        }

        return [
            'form'            => $form->createView(),
            'profile_picture' => $user->getProfileImageUrl(),
            'name'            => $user->getName(),
        ];
    }
}
