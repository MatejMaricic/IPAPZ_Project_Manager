<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/19/19
 * Time: 1:20 PM
 */

namespace App\Controller;


use App\Entity\Collaboration;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

;

class CollabController extends AbstractController
{
    /**
     * @Route("/index/{id}/request_collab", name="request_collab")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param User $user
     * @throws
     * @return Response $reponse
     */
    public function requestCollab(EntityManagerInterface $entityManager, Request $request, User $user, ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->findByUser($user->getId());

        $collaboration = new Collaboration();

        $collaboration->setUser($user);
        foreach ($projects as $project){
            $collaboration->addProject($project);
        }

        $collaboration->setPending(true);
        $collaboration->setSubscribed(false);
        $collaboration->setApproved(false);
        $collaboration->setCreatedAt(new \DateTime('now'));
        $collaboration->setSubscribedUntil(new \DateTime('now + 1 month'));


        $entityManager->persist($collaboration);
        $entityManager->flush();

        return $this->redirectToRoute('index_page');
    }

    /**
     * @Route("index/{id}/approve_collab", name="approve_collab")
     * @param EntityManagerInterface $entityManager
     * @param Collaboration $collaboration
     * @throws
     * @return Response
     */
    public function approveCollab(EntityManagerInterface $entityManager, Collaboration $collaboration)
    {
        $collaboration->setPending(false);
        $collaboration->setCreatedAt(new \DateTime('now'));
        $collaboration->setSubscribedUntil(new \DateTime('now + 1 month'));
        $collaboration->setSubscribed(true);
        $collaboration->setApproved(true);

        $entityManager->persist($collaboration);
        $entityManager->flush();

        return $this->redirectToRoute('index_page');
    }
}