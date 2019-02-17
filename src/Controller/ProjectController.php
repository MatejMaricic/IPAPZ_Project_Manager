<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 16.02.19.
 * Time: 15:44
 */

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/project/", name="project_index")
     */
  public function newProject(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository)
  {



      $form = $this->createForm(ProjectFormType::class);
      $form->handleRequest($request);
      if ($this->isGranted('ROLE_MANAGER') && $form->isSubmitted() && $form->isValid()) {
          /** @var Project $project */
          $project = $form->getData();
          $project->addUser($this->getUser());
          $entityManager->persist($project);
          $entityManager->flush();
          $this->addFlash('success', 'New project created!');
          return $this->redirectToRoute('index_page');
      }


      return $this->render('project/project.html.twig' , [
          'form' => $form->createView(),

      ]);
  }


}