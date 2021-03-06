<?php

namespace Sweepo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

use Sweepo\StreamBundle\Entity\Subscription;
use Sweepo\StreamBundle\Entity\Tweet;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sweepo\UserBundle\Entity\UserRepository")
 * @DoctrineAssert\UniqueEntity("email")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", unique=true, type="string", length=255)
     * @Assert\Email(message="Please use a valid email adress")
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(name="token_secret", type="string", length=255)
     */
    protected $token_secret;

    /**
     * @var string
     *
     * @ORM\Column(name="screen_name", type="string", length=255)
     */
    protected $screen_name;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="twitter_id", type="integer")
     */
    protected $twitter_id;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_image_url", type="string", length=255)
     */
    protected $profile_image_url;

    /**
     * @var string
     *
     * @ORM\Column(name="local", type="string", length=255)
     */
    protected $local;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=255)
     */
    protected $api_key;

    /**
     * @ORM\OneToMany(targetEntity="Sweepo\StreamBundle\Entity\Tweet", mappedBy="user", cascade={"all"})
     */
    protected $tweets;

    /**
     * @ORM\OneToMany(targetEntity="Sweepo\StreamBundle\Entity\Subscription", mappedBy="user", cascade={"all"})
     */
    protected $subscriptions;

    /**
     * @var string
     *
     * @ORM\Column(name="nb_subscriptions", type="integer", length=255)
     */
    protected $nb_subscriptions = 0;

    public function __toString()
    {
        return $this->screen_name;
    }

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->api_key = hash('md5', uniqid(true));
        $this->subscriptions = new ArrayCollection();
        $this->tweets = new ArrayCollection();
    }

    public function toArray($short = true)
    {
        $array = [
            'id'          => $this->id,
            'screen_name' => $this->screen_name,
            'name'        => $this->name,
        ];

        if (!$short) {
            $array = array_merge($array, [
                'email'             => $this->email,
                'token'             => $this->token,
                'token_secret'      => $this->token_secret,
                'local'             => $this->local,
                'created_at'        => $this->created_at,
                'api_key'           => $this->api_key,
                'twitter_id'        => $this->twitter_id,
                'profile_image_url' => $this->profile_image_url,
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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token_secret
     *
     * @param string $token_secret
     * @return User
     */
    public function setTokenSecret($token_secret)
    {
        $this->token_secret = $token_secret;

        return $this;
    }

    /**
     * Get token_secret
     *
     * @return string
     */
    public function getTokenSecret()
    {
        return $this->token_secret;
    }

    /**
     * Set screen_name
     *
     * @param string $screenName
     * @return User
     */
    public function setScreenName($screenName)
    {
        $this->screen_name = $screenName;

        return $this;
    }

    /**
     * Get screen_name
     *
     * @return string
     */
    public function getScreenName()
    {
        return $this->screen_name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set twitter_id
     *
     * @param integer $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitter_id = $twitterId;

        return $this;
    }

    /**
     * Get twitter_id
     *
     * @return integer
     */
    public function getTwitterId()
    {
        return $this->twitter_id;
    }

    /**
     * Set profile_image_url
     *
     * @param string $profileImageUrl
     * @return User
     */
    public function setProfileImageUrl($profileImageUrl)
    {
        $this->profile_image_url = $profileImageUrl;

        return $this;
    }

    /**
     * Get profile_image_url
     *
     * @return string
     */
    public function getProfileImageUrl()
    {
        return $this->profile_image_url;
    }

    /**
     * Set local
     *
     * @param string $local
     * @return User
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
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
     * Set api_key
     *
     * @param string $api_key
     * @return User
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }

    /**
     * Get api_key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Set nb_subscriptions
     *
     * @param integer $nb_subscriptions
     * @return User
     */
    public function setNbSubscriptions($nb_subscriptions)
    {
        $this->nb_subscriptions = $nb_subscriptions;

        return $this;
    }

    /**
     * Get nb_subscriptions
     *
     * @return integer
     */
    public function getNbSubscriptions()
    {
        return $this->nb_subscriptions;
    }

    /**
     * Get roles
     *
     * @return integer
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
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

    /**
     * Add subscriptions
     *
     * @param \Sweepo\StreamBundle\Entity\Subscription $subscriptions
     * @return User
     */
    public function addSubscription(\Sweepo\StreamBundle\Entity\Subscription $subscriptions)
    {
        $subscriptions->setUser($this);
        $this->subscriptions[] = $subscriptions;

        // Here we don't increase the nb_subscription of the user because we make it in StreamService to test
        // if there are new subscriptions since the last loading.

        return $this;
    }

    /**
     * Remove subscriptions
     *
     * @param \Sweepo\StreamBundle\Entity\Subscription $subscriptions
     */
    public function removeSubscription(\Sweepo\StreamBundle\Entity\Subscription $subscriptions)
    {
        $this->subscriptions->removeElement($subscriptions);
        $this->nb_subscriptions--;
    }

    /**
     * Get subscriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }
}