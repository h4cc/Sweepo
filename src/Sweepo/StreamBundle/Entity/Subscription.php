<?php

namespace Sweepo\StreamBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sweepo\StreamBundle\Entity\SubscriptionRepository")
 */
class Subscription
{
    const TYPE_KEYWORD = 'keyword';
    const TYPE_USER = 'user';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Sweepo\UserBundle\Entity\User", inversedBy="subscriptions", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="subscription", type="string", length=255)
     */
    private $subscription;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="Sweepo\StreamBundle\Entity\Tweet", mappedBy="subscription", cascade={"remove"})
     */
    private $tweets;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->tweets = new ArrayCollection();
    }

    public function toArray($short = true)
    {
        $array = [
            'id'           => $this->id,
            'subscription' => $this->subscription,
            'created_at'   => $this->created_at,
            'type'         => $this->type,
            'user'         => $this->user->toArray(),
        ];

        if (!$short) {
            $array = array_merge($array, [

            ]);
        }

        return $array;
    }

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
     * Set type
     *
     * @param string $subscription
     * @return Subscription
     */
    public function setType($subscription)
    {
        if('@' === substr($subscription, 0, 1)) {
            $this->type = self::TYPE_USER;

            return $this;
        }

        $this->type = self::TYPE_KEYWORD;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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

    /**
     * Set user
     *
     * @param \Sweepo\UserBundle\Entity\User $user
     * @return Subscription
     */
    public function setUser(\Sweepo\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Sweepo\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add tweets
     *
     * @param \Sweepo\StreamBundle\Entity\Tweet $tweets
     * @return User
     */
    public function addTweet(\Sweepo\StreamBundle\Entity\Tweet $tweets)
    {
        $tweets->setUser($this);
        $this->tweets[] = $tweets;

        return $this;
    }

    /**
     * Remove tweets
     *
     * @param \Sweepo\StreamBundle\Entity\Tweet $tweets
     */
    public function removeTweet(\Sweepo\StreamBundle\Entity\Tweet $tweets)
    {
        $this->tweets->removeElement($tweets);
    }

    /**
     * Get tweets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTweets()
    {
        return $this->tweets;
    }
}