<?php

namespace Sweepo\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class CoreController extends Controller
{
    /**
     * @Route("/test", name="test")
     * @Template()
     * @Secure("ROLE_USER")
     */
    public function testAction()
    {
        // $mandrill = $this->get('sweepo.mandrill');
        // $mandrill->setHtml('<p>html</p>');
        // $mandrill->setText('text');
        // $mandrill->setSubject('Subject');
        // $mandrill->setTo('r.gazelot@gmail.com');
        // $mandrill->setFrom('daily@sweepo.fr', 'Sweepo');

        // $mandrill->send();



        return [];
    }
}
