<?php

namespace Sweepo\StreamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sweepo\StreamBundle\Entity\SubscriptionRepository")
 */
class Subscription
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="subscription", type="string", length=255)
     */
    private $subscription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subscription
     *
     * @param string $subscription
     * @return Subscription
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    
        return $this;
    }

    /**
     * Get subscription
     *
     * @return string 
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Subscription
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
