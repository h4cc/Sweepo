<?php

namespace Sweepo\CoreBundle\Service\MailerTransport;

use Sweepo\CoreBundle\Service\MailerTransport\Mandrill;

class Mailer
{
    private $twig;
    private $mandrill;

    public function __construct(\Twig_Environment $twig, Mandrill $mandrill)
    {
        $this->twig = $twig;
        $this->mandrill = $mandrill;
    }

    public function send($users, $template)
    {
        if (is_array($users)) {
            foreach ($users as $user) {
                $template = $this->loadTemplate($template, $user->getLocal());
            }
        } else {
            $template = $this->loadTemplate($template, $users->getLocal());
            $this->sendEmail($users->getEmail(), $template);
        }
    }

    private function loadTemplate($template, $language)
    {
        try {
            return $this->twig->render("SweepoCoreBundle:Emails_{$language}:{$template}.html.twig");
        } catch (\Exception $e) {}

        return $this->twig->render("BalloonCoreBundle:Emails_en:{$template}.html.twig");
    }

    private function sendEmail($email, $html)
    {
        $this->mandrill->setHtml($html);
        $this->mandrill->setSubject('Subject');
        $this->mandrill->setTo($email);
        $this->mandrill->setFrom('daily@sweepo.fr');

        $this->mandrill->send();
    }
}