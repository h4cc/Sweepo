<?php

namespace Sweepo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Sweepo\UserBundle\Form\UserType;
use Sweepo\UserBundle\Entity\User;
use Sweepo\UserBundle\Security\Authentication\Token\TwitterUserToken;

class UserController extends Controller
{
    /**
     * @Route("/user/create", name="create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
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

    /**
     * @Route("/user/edit", name="user_edit")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new UserType(), $user);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('stream'));
            }
        }

        return [
            'form'            => $form->createView(),
            'profile_picture' => $user->getProfileImageUrl(),
            'name'            => $user->getName()
        ];
    }

    /**
     * @Route("/user/delete", name="user_delete")
     * @Secure(roles="ROLE_USER")
     */
    public function deleteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($this->getUser());
        $em->flush();

        return $this->redirect($this->generateUrl('logout'));
    }
}
