<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 16.02.19.
 * Time: 15:44
 */

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\AssignDevFormType;
use App\Form\DiscussionFormType;
use App\Form\ProjectFormType;
use App\Form\ProjectStatusFormType;
use App\Form\TaskFormType;
use App\Repository\ProjectStatusRepository;
use App\Repository\UserRepository;
use App\Services\Fetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{


    /**
     * @Route("/project/{id}", name="project_view")
     * @param                  Project $project
     * @param                  Request $request
     * @param                  EntityManagerInterface $entityManager
     * @param                  UserRepository $userRepository
     * @param                  Fetcher $fetcher
     * @return                 Response
     */
    public function projectHandler(
        Project $project,
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Fetcher $fetcher
    ) {
        $devs = $userRepository->devsOnProject($project->getId());

        $devForm = $this->createForm(AssignDevFormType::class, $data = null, array("id" => $this->getUser()->getId()));

        $devForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $devForm->isSubmitted() && $devForm->isValid()) {
            $this->assignDev($project, $entityManager, $devForm);
            return $this->redirect($request->getUri());
        }

        if ($fetcher->checkSubscription() == 0) {
            return $this->redirectToRoute('index_page');
        }

        return $this->render(
            'project/project.html.twig',
            [
                'user' => $this->getUser(),
                'project' => $project,
                'devForm' => $devForm->createView(),
                'devs' => $devs
            ]
        );
    }

    private function editProject(
        EntityManagerInterface $entityManager,
        $projectForm
    ) {
        $project = $projectForm->getData();
        $entityManager->persist($project);
        $entityManager->flush();
    }


    private function assignDev(
        Project $project,
        EntityManagerInterface $entityManager,
        $devForm
    ) {
        $user = $devForm->getData();
        foreach ($user as $singleuser) {
            foreach ($singleuser as $item) {
                $project->addUser($item);
            }
        }
        $entityManager->persist($project);
        $entityManager->flush();
    }


    /**
     * @Route("/status_change/{id}/{status_id}", name="status_change", methods={"POST", "GET"})
     * @param                                    Task $task
     * @param                                    ProjectStatusRepository $project_status_repository
     * @param                                    EntityManagerInterface $entityManager
     * @param                                    Request $request
     * @return                                   JsonResponse
     */
    public function statusChange(
        Task $task,
        ProjectStatusRepository $project_status_repository,
        EntityManagerInterface $entityManager,
        Request $request
    ) {

        $statusId = $request->get('status_id');

        $newStatus = $project_status_repository->find($statusId);

        $oldStatusID = $task->getStatus();
        $task->setStatus($newStatus);
        $entityManager->persist($task);
        $entityManager->flush();


        if (!$newStatus->getId()) {
            return new JsonResponse(
                [
                    'msg' => 'Unable to delete'
                ]
            );
        }

        return new JsonResponse(
            [
                'newStatusID' => $newStatus->getId(),
                'oldStatusID' => $oldStatusID->getId(),
                'taskID' => $task->getId()
            ]
        );
    }


    /**
     * @Route("/project_edit/{id}", name="project_edit")
     * @param                       Request $request
     * @param                       EntityManagerInterface $entityManager
     * @param                       Project $project
     * @param                       Fetcher $fetcher
     * @return                      Response
     */
    public function projectEditor(
        Request $request,
        EntityManagerInterface $entityManager,
        Project $project,
        Fetcher $fetcher
    ) {
        $projectForm = $this->createForm(ProjectFormType::class, $project);
        $projectForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $projectForm->isSubmitted() && $projectForm->isValid()) {
            $this->editProject($entityManager, $projectForm);
            return $this->redirectToRoute('index_page');
        }


        if ($fetcher->checkSubscription() == 0) {
            return $this->redirectToRoute('index_page');
        }

        return $this->render(
            'project/project_edit.html.twig',
            [
                'user' => $this->getUser(),
                'projectForm' => $projectForm->createView()
            ]
        );
    }


    /**
     * @Route("/{id}/complete", name="project_complete", methods={"POST", "GET"})
     * @param                   Project $project
     * @param                   EntityManagerInterface $entityManager
     * @return                  Response
     */
    public function completeProject(
        Project $project,
        EntityManagerInterface $entityManager
    ) {
        $project->setCompleted(true);
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('index_page');
    }

    /**
     * @Route("/{id}/reopen", name="project_reopen", methods={"POST", "GET"})
     * @param                 Project $project
     * @param                 EntityManagerInterface $entityManager
     * @return                Response
     */
    public function projectReopen(
        Project $project,
        EntityManagerInterface $entityManager
    ) {

        $project->setCompleted(false);

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('index_page');
    }

    /**
     * @Route("/completed_projects/", name="completed_projects")
     * @return                        Response
     */

    public function completedProjectsView()
    {
        return $this->render(
            'project/completed_projects.html.twig',
            [
                'user' => $this->getUser(),

            ]
        );
    }

    /**
     * @Route("/single_project/{id}", name="single_project")
     * @param                         Project $project
     * @return                        Response
     */
    public function completedSingleProjectView(Project $project)
    {
        return $this->render(
            'project/single_project.html.twig',
            [
                'user' => $this->getUser(),
                'project' => $project

            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="project_delete", methods={"POST", "GET"})
     * @param                 Project $project
     * @param                 EntityManagerInterface $entityManager
     * @return                JsonResponse
     */
    public function deleteProject(
        Project $project,
        EntityManagerInterface $entityManager
    ) {

        $projectId = $project->getId();

        if (!$projectId) {
            return new JsonResponse(
                [
                    'msg' => 'Unable to delete'
                ]
            );
        }
        $entityManager->remove($project);
        $entityManager->flush();

        return new JsonResponse(
            [
                'deletedProject' => $projectId
            ]
        );
    }
}
