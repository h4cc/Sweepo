<?php

namespace Sweepo\StreamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tweet
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sweepo\StreamBundle\Entity\TweetRepository")
 */
class Tweet
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
     * @ORM\ManyToOne(targetEntity="Sweepo\UserBundle\Entity\User", inversedBy="tweets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="tweet_id", type="integer")
     */
    private $tweet_id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tweet_created_at", type="datetime")
     */
    private $tweet_created_at;

    /**
     * @var string
     *
     * @ORM\Column(name="in_reply_to_screen_name", type="string", length=255, nullable=true)
     */
    private $in_reply_to_screen_name;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer")
     */
    private $owner_id;

    /**
     * @var string
     *
     * @ORM\Column(name="owner_name", type="string", length=255)
     */
    private $owner_name;

    /**
     * @var string
     *
     * @ORM\Column(name="owner_screen_name", type="string", length=255)
     */
    private $owner_screen_name;

    /**
     * @var string
     *
     * @ORM\Column(name="owner_profile_image_url", type="string", length=255)
     */
    private $owner_profile_image_url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_retweeted", type="boolean")
     */
    private $is_retweeted = false;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_user_screen_name", type="string", length=255, nullable=true)
     */
    private $raw_user_screen_name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
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
     * Set user
     *
     * @param string $user
     * @return Tweet
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set tweet_id
     *
     * @param integer $tweetId
     * @return Tweet
     */
    public function setTweetId($tweetId)
    {
        $this->tweet_id = $tweetId;

        return $this;
    }

    /**
     * Get tweet_id
     *
     * @return integer
     */
    public function getTweetId()
    {
        return $this->tweet_id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Tweet
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set tweet_created_at
     *
     * @param \DateTime $tweetCreatedAt
     * @return Tweet
     */
    public function setTweetCreatedAt($tweetCreatedAt)
    {
        $this->tweet_created_at = $tweetCreatedAt;

        return $this;
    }

    /**
     * Get tweet_created_at
     *
     * @return \DateTime
     */
    public function getTweetCreatedAt()
    {
        return $this->tweet_created_at;
    }

    /**
     * Set in_reply_to_screen_name
     *
     * @param string $inReplyToScreenName
     * @return Tweet
     */
    public function setInReplyToScreenName($inReplyToScreenName)
    {
        $this->in_reply_to_screen_name = $inReplyToScreenName;

        return $this;
    }

    /**
     * Get in_reply_to_screen_name
     *
     * @return string
     */
    public function getInReplyToScreenName()
    {
        return $this->in_reply_to_screen_name;
    }

    /**
     * Set owner_id
     *
     * @param integer $ownerId
     * @return Tweet
     */
    public function setOwnerId($ownerId)
    {
        $this->owner_id = $ownerId;

        return $this;
    }

    /**
     * Get owner_id
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * Set owner_name
     *
     * @param string $ownerName
     * @return Tweet
     */
    public function setOwnerName($ownerName)
    {
        $this->owner_name = $ownerName;

        return $this;
    }

    /**
     * Get owner_name
     *
     * @return string
     */
    public function getOwnerName()
    {
        return $this->owner_name;
    }

    /**
     * Set owner_screen_name
     *
     * @param string $ownerScreenName
     * @return Tweet
     */
    public function setOwnerScreenName($ownerScreenName)
    {
        $this->owner_screen_name = $ownerScreenName;

        return $this;
    }

    /**
     * Get owner_screen_name
     *
     * @return string
     */
    public function getOwnerScreenName()
    {
        return $this->owner_screen_name;
    }

    /**
     * Set owner_profile_image_url
     *
     * @param string $ownerProfileImageUrl
     * @return Tweet
     */
    public function setOwnerProfileImageUrl($ownerProfileImageUrl)
    {
        $this->owner_profile_image_url = $ownerProfileImageUrl;

        return $this;
    }

    /**
     * Get owner_profile_image_url
     *
     * @return string
     */
    public function getOwnerProfileImageUrl()
    {
        return $this->owner_profile_image_url;
    }

    /**
     * Set is_retweeted
     *
     * @param boolean $isRetweeted
     * @return Tweet
     */
    public function setIsRetweeted($isRetweeted)
    {
        $this->is_retweeted = $isRetweeted;

        return $this;
    }

    /**
     * Get is_retweeted
     *
     * @return boolean
     */
    public function getIsRetweeted()
    {
        return $this->is_retweeted;
    }

    /**
     * Set raw_user_screen_name
     *
     * @param string $rawUserScreenName
     * @return Tweet
     */
    public function setRawUserScreenName($rawUserScreenName)
    {
        $this->raw_user_screen_name = $rawUserScreenName;

        return $this;
    }

    /**
     * Get raw_user_screen_name
     *
     * @return string
     */
    public function getRawUserScreenName()
    {
        return $this->raw_user_screen_name;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Tweet
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
