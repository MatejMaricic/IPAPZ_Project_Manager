<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 15.02.19.
 * Time: 18:06
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\ProjectStatus;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class IndexController extends AbstractController
{


    /**
     * @param Request $request
     * @Route("/", name="index_page")
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @return Response
     */
  public function newProject(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository)
  {



      $form = $this->createForm(ProjectFormType::class);
      $form->handleRequest($request);
      if ($this->isGranted('ROLE_MANAGER') && $form->isSubmitted() && $form->isValid()) {
          /** @var Project $project */
          $project = $form->getData();
          $project->addUser($this->getUser());
          $project->addProjectStatus($project->getName());
          $entityManager->persist($project);
          $entityManager->flush();

          $entityManager->flush();
          $this->addFlash('success', 'New project created!');
          return $this->redirectToRoute('index_page');
      }


      return $this->render('index.html.twig' , [
          'form' => $form->createView(),
          'projects' => $projectRepository->findAll(),
          'user' => $this->getUser()

      ]);
  }




}
