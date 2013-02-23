<?php

namespace Sweepo\StreamBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Sweepo\UserBundle\Entity\User;

/**
 * TweetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TweetRepository extends EntityRepository
{
    public function getLastId(User $user)
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('t.id')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
