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
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return Response
     */
    public function projectHandler(Project $project, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $devs = $userRepository->devsOnProject($project->getId());

        $devForm = $this->createForm(AssignDevFormType::class, $data = null, array("id" => $this->getUser()->getId()));
        $taskForm = $this->createForm(TaskFormType::class, $data = null, array("project_id" => $project->getId()));
        $statusForm = $this->createForm(ProjectStatusFormType::class);
        $discussionForm = $this->createForm(DiscussionFormType::class);

        $taskForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
            $this->addTask($request, $entityManager, $project, $taskForm);
            return $this->redirect($request->getUri());
        } else {
            $devForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $devForm->isSubmitted() && $devForm->isValid()) {
                $this->assignDev($project, $entityManager, $devForm);
                return $this->redirect($request->getUri());
            } else {
                $statusForm->handleRequest($request);
                if ($this->isGranted('ROLE_MANAGER') && $statusForm->isSubmitted() && $statusForm->isValid()) {
                    $this->newStatus($entityManager, $project, $statusForm);
                    return $this->redirect($request->getUri());

                } else {
                    $discussionForm->handleRequest($request);
                    if ($this->isGranted('ROLE_MANAGER') && $discussionForm->isSubmitted() && $discussionForm->isValid()) {
                        $this->newDiscussion( $entityManager, $project, $discussionForm);
                        return $this->redirect($request->getUri());

                    }

                }
            }
        }
        return $this->render('project/project.html.twig', [
            'taskForm' => $taskForm->createView(),
            'user' => $this->getUser(),
            'project' => $project,
            'devForm' => $devForm->createView(),
            'statusForm' => $statusForm->createView(),
            'discussionForm' => $discussionForm->createView(),
            'devs' => $devs
        ]);

    }

    private function editProject(EntityManagerInterface $entityManager, Project $project, $projectForm)
    {
        $project = $projectForm->getData();
        $entityManager->persist($project);
        $entityManager->flush();
    }


    private function addTask(Request $request, EntityManagerInterface $entityManager, Project $project, $taskForm)
    {

        /**@var Task $task */
        $task = $taskForm->getData();
        $files = $request->files->get('task_form')['images'];

        if (!empty($files)) {

            foreach ($files as $file) {
                $uploads_directory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploads_directory,
                    $filename

                );
                $images[] = $filename;
            }
            $task->setImages($images);

        }

        $task->setProject($project);
        $task->setCompleted(false);
        $entityManager->persist($task);
        $entityManager->flush();

    }


    private function newStatus(EntityManagerInterface $entityManager, Project $project, $statusForm)
    {
        $status = $statusForm->getData();
        $project->addProjectStatus($status);
        $entityManager->persist($project);
        $entityManager->flush();

    }

    private function assignDev(Project $project, EntityManagerInterface $entityManager, $devForm)
    {
        $user = $devForm->getData();
        foreach ($user as $singleuser) {
            foreach ($singleuser as $item) {
                $project->addUser($item);
            }
        }
        $entityManager->persist($project);
        $entityManager->flush();
    }

    private function newDiscussion(EntityManagerInterface $entityManager, Project $project, $discussionForm)
    {
        $discussion = $discussionForm->getData();
        $discussion->setProject($project);
        $discussion->setCreatedBy($this->getUser()->getFullName());

        $entityManager->persist($discussion);
        $entityManager->flush();

    }


    /**
     * @Route("/status_change/{id}/{status_id}", name="status_change", methods={"POST", "GET"})
     * @param Task $task
     * @param ProjectStatusRepository $project_status_repository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return JsonResponse
     */
    public function statusChange(Task $task, ProjectStatusRepository $project_status_repository, EntityManagerInterface $entityManager, Request $request)
    {

        $statusId = $request->get('status_id');

        $newStatus = $project_status_repository->find($statusId);

        $oldStatusID = $task->getStatus();
        $task->setStatus($newStatus);
        $entityManager->persist($task);
        $entityManager->flush();


        if (!$newStatus->getId()) {
            return new JsonResponse([
                'msg' => 'Unable to delete'
            ]);
        }

        return new JsonResponse([
            'newStatusID' => $newStatus->getId(),
            'oldStatusID' => $oldStatusID->getId(),
            'taskID' => $task->getId()
        ]);
    }



    /**
     * @Route("/project_edit/{id}", name="project_edit")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Project $project
     * @return Response
     */
    public function projectEditor(Request $request, EntityManagerInterface $entityManager, Project $project)
    {
        $projectForm = $this->createForm(ProjectFormType::class, $project);
        $projectForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $projectForm->isSubmitted() && $projectForm->isValid()) {
            $this->editProject($entityManager, $project, $projectForm);
            return $this->redirectToRoute('index_page');
        }


        return $this->render('project/project_edit.html.twig', [
            'user' => $this->getUser(),
            'projectForm' => $projectForm->createView()
        ]);
    }


    /**
     * @Route("/{id}/complete", name="project_complete", methods={"POST", "GET"})
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function completeProject(Project $project, EntityManagerInterface $entityManager)
    {
        $project->setCompleted(true);
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute( 'index_page' );
    }

    /**
     * @Route("/{id}/reopen", name="project_reopen", methods={"POST", "GET"})
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function projectReopen(Project $project, EntityManagerInterface $entityManager)
    {

        $project->setCompleted(false);

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('index_page');

    }

    /**
     * @Route("/completed_projects/{id}", name="completed_projects")
     * @param Project $project
     * @return Response
     */

    public function completedProjectsView(Project $project)
    {
        return $this->render('project/completed_projects.html.twig', [
            'user' => $this->getUser(),
            'project' => $project

        ]);
    }

    /**
     * @Route("/single_project/{id}", name="single_project")
     * @param Project $project
     * @return Response
     */
    public function completedSingleProjectView(Project $project)
    {
        return $this->render('project/single_project.html.twig', [
            'user' => $this->getUser(),
            'project' => $project

        ]);
    }

    /**
     * @Route("/{id}/delete", name="project_delete", methods={"POST", "GET"})
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteProject(Project $project, EntityManagerInterface $entityManager)
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

