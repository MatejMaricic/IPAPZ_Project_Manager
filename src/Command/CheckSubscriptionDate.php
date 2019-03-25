<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/21/19
 * Time: 7:51 AM
 */

namespace App\Command;

use App\Controller\CollabController;
use App\Repository\CollaborationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckSubscriptionDate extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:check-subscriptions';

    private $collabController;
    private $collaborationRepository;
    private $entityManager;


    public function __construct(
        CollabController $collabController,
        CollaborationRepository $collaborationRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->collabController = $collabController;
        $this->collaborationRepository = $collaborationRepository;
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
            ->setHelp('This command checks subscription date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->collabController->checkSubscriptionDate($this->collaborationRepository, $this->entityManager);


        $output->writeln('Date Checked!');
    }
}
