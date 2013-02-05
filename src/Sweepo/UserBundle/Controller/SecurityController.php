<?php

namespace Sweepo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sweepo\UserBundle\Entity\User;
use Sweepo\UserBundle\Form\UserType;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request) {}

    /**
     * @Route("/login-check", name="login_check")
     * @Template()
     */
    public function loginCheckAction(Request $request)
    {
        return $this->redirect($this->generateUrl('stream'));
    }

    /**
     * @Route("/create", name="create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        if (null === $request->query->get('oauth_token') || null === $request->query->get('oauth_token_secret')) {
            // TODO
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
            $user->setLang($informations->lang);
        }

        $form = $this->createForm(new UserType(), $user);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('index'));
            }
        }

        return [
            'form'            => $form->createView(),
            'profile_picture' => $user->getProfileImageUrl(),
        ];
    }
}
