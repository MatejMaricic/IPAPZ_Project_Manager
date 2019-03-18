<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/18/19
 * Time: 12:20 PM
 */

namespace App\Controller;

use App\Repository\SubscriptionsRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EmailController extends AbstractController
{

    /**
     * @Route("/email", name="email")
     * @param EntityManagerInterface $entityManager
     * @param \Swift_Mailer $mailer
     * @param TaskRepository $taskRepository
     * @param SubscriptionsRepository $subscriptionsRepository
     * @return Response
     */
    public function mail(\Swift_Mailer $mailer, TaskRepository $taskRepository, SubscriptionsRepository $subscriptionsRepository, EntityManagerInterface $entityManager)
    {
        $tasks = $taskRepository->findAll();

        foreach ($tasks as $task)
        {
            if ($task->getUpdated() == true){

                $subscribers = $subscriptionsRepository->findByTask($task->getId());

                foreach ($subscribers as $subscriber){
                    $message = (new \Swift_Message('Hello Email'))
                        ->setFrom('send@example.com')
                        ->setTo($subscriber->getUserEmail())
                        ->setBody(
                            $this->renderView('test.html.twig', [
                                'task'=> $task
                            ])
                        );
                    $mailer->send($message);
                    $task->setUpdated(false);
                    $entityManager->persist($task);
                    $entityManager->flush();


                }

            }

        }

        return $this->redirectToRoute( 'index_page' );
    }

}