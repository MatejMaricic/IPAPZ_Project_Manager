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
     * @Route("index/{id}/subscribe_to_collab", name="subscribe_to_collab")
     * @param EntityManagerInterface $entityManager
     * @param Collaboration $collaboration
     * @throws
     * @return Response
     */
    public function subscribeToCollab(EntityManagerInterface $entityManager, Collaboration $collaboration)
    {

        $collaboration->setCreatedAt(new \DateTime('now'));
        $collaboration->setSubscribedUntil(new \DateTime('now + 1 month'));
        $collaboration->setSubscribed(true);

        $entityManager->persist($collaboration);
        $entityManager->flush();

        return $this->redirectToRoute('index_page');
    }
}