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
use App\Form\ProjectStatusFormType;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;



class IndexController extends AbstractController
{


    /**
     * @param Request $request
     * @Route("/", name="index_page")
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @return Response
     */
  public function newProject(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository, UserRepository $userRepository)
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


      return $this->render('index.html.twig' , [
          'form' => $form->createView(),
          'projects' => $projectRepository->findAll(),
          'user' => $this->getUser(),
          'users' => $userRepository->findAllDevelopersArray()

      ]);
  }
    /**
     * @Route("/{id}/delete", name="project_delete", methods={"POST", "GET"})
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteProject(Project $project, EntityManagerInterface $entityManager )
    {

        $projectId = $project->getId();

        if (!$project) {
            return new JsonResponse([
                'msg' => 'Unable to delete'
            ]);
        }

        $entityManager->remove($project);
        $entityManager->flush();


        return new JsonResponse([
            'deletedProject' => $projectId
        ]);
    }



}
