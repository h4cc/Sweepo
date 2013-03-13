<?php

namespace Sweepo\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CrontabCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron')
            ->setDescription('Crontab command for send tweets')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('SweepoUserBundle:User')->getUsersForSending();

        foreach ($users as $user) {

            // Update the stream
            $this->getContainer()->get('sweepo.stream')->fetchTweetsFromTwitter($user);
            $dateTimeToday = new \DateTime(date('Y-m-d'));

            $tweets = $em->getRepository('SweepoStreamBundle:Tweet')->getStream($user, $dateTimeToday);

            if (!empty($tweets)) {
                $this->getContainer()->get('sweepo.mailer')->send($user, 'daily', ['tweets' => $tweets, 'user' => $user]);
            }
        }
    }
}