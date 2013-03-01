<?php

namespace Sweepo\StreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Sweepo\StreamBundle\Entity\Tweet;

class StreamController extends Controller
{
    /**
     * @Route("/stream", name="stream")
     * @Template()
     * @Secure("ROLE_USER")
     */
    public function streamAction()
    {
        $this->get('sweepo.stream')->fetchTweetsFromTwitter($this->getUser());

        return [
            'user' => $this->getUser(),
        ];
    }
}
