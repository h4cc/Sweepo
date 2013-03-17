<?php

namespace Sweepo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Sweepo\UserBundle\Form\UserType;

class UserController extends Controller
{
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
