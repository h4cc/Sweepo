<?php

namespace Sweepo\ApiBundle\Authentication;

use Doctrine\ORM\EntityManager;

class ApiLogin
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function checkToken($token)
    {
        if (null === $user = $this->em->getRepository('SweepoUserBundle:User')->findOneBy(['api_key' => $token])) {
            return false;
        }

        return $user;
    }
}