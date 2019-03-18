<?php

namespace App\Command;


use App\Controller\EmailController;
use App\Repository\SubscriptionsRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



class CronOnSubscribers extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:start-cron';

    private $emailController;
    private $mailer;
    private $taskRepository;
    private $subscriptionRepository;
    private $entityManager;

    public function __construct(EmailController $emailController, \Swift_Mailer $mailer, TaskRepository $taskRepository, SubscriptionsRepository $subscriptionsRepository, EntityManagerInterface $entityManager)
    {
        $this->emailController = $emailController;
        $this->mailer = $mailer;
        $this->taskRepository = $taskRepository;
        $this->subscriptionRepository = $subscriptionsRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Starts A CronJob')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to send mails to subscribers...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $test = $this->emailController->mail($this->mailer,$this->taskRepository, $this->subscriptionRepository, $this->entityManager);


        $output->writeln('Email Sent!');
    }
}