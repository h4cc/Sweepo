<?php

namespace Sweepo\CoreBundle\Service\MailerTransport;

use Symfony\Component\Translation\TranslatorInterface;

use Sweepo\CoreBundle\Service\MailerTransport\Mandrill;
use Sweepo\UserBundle\Entity\User;

class Mailer
{
    private $twig;
    private $mandrill;
    private $translator;

    public function __construct(\Twig_Environment $twig, Mandrill $mandrill, TranslatorInterface $translator)
    {
        $this->twig = $twig;
        $this->mandrill = $mandrill;
        $this->translator = $translator;
    }

    /**
     * @param  User|UserCollection $users One or plus User
     * @param  string $template   The template email needed
     * @param  array  $parameters Parameters used in the email
     */
    public function send($users, $template, $parameters = [])
    {
        if (is_array($users)) {
            foreach ($users as $user) {
                $template = $this->loadTemplate($template, $user->getLocal(), $parameters);
                $this->sendEmail($user, $template);
            }
        } else {
            $template = $this->loadTemplate($template, $users->getLocal(), $parameters);
            $this->sendEmail($users, $template);
        }
    }

    /**
     * Load template with twig and correct language
     * @param  string $template   The template email needed
     * @param  string $language   The language of the email
     * @param  array  $parameters Parameters used in the email
     * @return string The html content in string format
     */
    private function loadTemplate($template, $language, $parameters = [])
    {
        try {
            return $this->twig->render("SweepoCoreBundle:Emails_{$language}:{$template}.html.twig", $parameters);
        } catch (\Exception $e) {}

        return $this->twig->render("SweepoCoreBundle:Emails_en:{$template}.html.twig", $parameters);
    }

    /**
     * @param  User   $user The recipient
     * @param  string $html The mail content
     */
    private function sendEmail(User $user, $html)
    {
        $this->mandrill->setHtml($html);
        $this->mandrill->setSubject($this->translator->trans('daily_email_subject', [], 'messages', $user->getLocal()));
        $this->mandrill->setTo($user->getEmail());
        $this->mandrill->setFrom('daily@sweepo.fr', 'Sweepo');
        $response = $this->mandrill->send();
        $this->mandrill->clean();
    }
}