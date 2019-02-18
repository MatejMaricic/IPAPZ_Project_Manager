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
use App\Entity\ProjectStatus;
use App\Form\ProjectStatusFormType;
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
     * @Route("/project/{id}", name="project_view")
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    public function showProject(Project $project, Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository)
    {

        $form = $this->createForm(ProjectStatusFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $form->isSubmitted() && $form->isValid()){
        /**@var ProjectStatus $projectStatus */
        $projectStatus = $form->getData();
        $project->addProjectStatus($projectStatus);
        $entityManager->persist($projectStatus);
        $entityManager->flush();
        }
        return $this->render('project/project.html.twig', [
            'project' => $project,
            'form' => $form->createView()

        ]);
    }

}