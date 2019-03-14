<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 16.02.19.
 * Time: 15:44
 */

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Project;
use App\Entity\Subscriptions;
use App\Entity\Task;
use App\Form\AssignDevFormType;
use App\Form\CommentFormType;
use App\Form\DiscussionFormType;
use App\Form\ProjectFormType;
use App\Form\ProjectStatusFormType;
use App\Form\TaskFormType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectStatusRepository;
use App\Repository\SubscriptionsRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProjectController extends AbstractController
{


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

    /**
     * @Route("/project/{id}/complete", name="task_completed", methods={"POST", "GET"})
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function taskCompleted(Task $task, EntityManagerInterface $entityManager)
    {
        $id = $task->getProject()->getId();
        $task->setCompleted(true);

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('completed_tasks', array('id' => $id));

    }

    /**
     * @Route("/project/{id}/reopen", name="task_reopen", methods={"POST", "GET"})
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function taskReopen(Task $task, EntityManagerInterface $entityManager)
    {

        $projectId = $task->getProject()->getId();
        $task->setCompleted(false);

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('project_view', array('id' => $projectId));

    }

    /**
     * @Route("/subscribe_to_task/{id}", name="subscribe_to_task")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SubscriptionsRepository $subscriptionsRepository
     * @param Task $task
     * @return Response
     */
    public function subscribeToTask(Task $task, EntityManagerInterface $entityManager, Request $request, SubscriptionsRepository $subscriptionsRepository)
    {
        $subscription = new Subscriptions();
        $email = $this->getUser()->getEmail();
        $projectId = $task->getProject()->getId();

        try {
            $subscription->setUserEmail($email);
            $subscription->setTaskId($task->getId());
            $entityManager->persist($subscription);
            $entityManager->flush();
        } catch (ConstraintViolationException $constraintViolationException) {

        }


        return $this->redirectToRoute('project_view', array('id' => $projectId));
    }


    /**
     * @Route("/completed_tasks/{id}", name="completed_tasks")
     * @param Project $project
     * @return Response
     */
    public function completedTaskView(Project $project)
    {
        return $this->render('project/completed_tasks.html.twig', [
            'user' => $this->getUser(),
            'project' => $project

        ]);
    }


    private function assignDevToTask(Task $task, Request $request, EntityManagerInterface $entityManager, $devForm, SubscriptionsRepository $subscriptionsRepository)
    {


        $user = $devForm->getData();
        foreach ($user as $singleuser) {
            foreach ($singleuser as $item) {
                $task->addUser($item);
                $subs = $subscriptionsRepository->checkSubscriber($task->getId(), $item->getEmail());

                if (!isset($subs[0])) {

                    $subscription = new Subscriptions();
                    $subscription->setUserEmail($item->getEmail());
                    $subscription->setTaskId($task->getId());
                    $entityManager->persist($subscription);
                    $entityManager->flush();

                }


            }
        }
        $entityManager->persist($task);
        $entityManager->flush();
    }


    private function newStatus(Request $request, EntityManagerInterface $entityManager, Project $project, $statusForm)
    {
        $status = $statusForm->getData();
        $project->addProjectStatus($status);
        $entityManager->persist($project);
        $entityManager->flush();

    }

    private function assignDev(Project $project, Request $request, EntityManagerInterface $entityManager, $devForm)
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

    public function addComment(Task $task, Request $request, EntityManagerInterface $entityManager, $commentForm)
    {
        /**@var Comments $comments */
        $comments = $commentForm->getData();

        $files = $request->files->get('comment_form')['images'];

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
            $comments->setImages($images);

        }
        $comments->setUser($this->getUser());
        $comments->setTask($task);
        $entityManager->persist($comments);
        $entityManager->flush($comments);

    }


    /**
     * @Route("/project/{id}", name="project_view")
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @return Response
     */
    public function projectHandler(Project $project, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
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
                $this->assignDev($project, $request, $entityManager, $devForm);
                return $this->redirect($request->getUri());
            } else {
                $statusForm->handleRequest($request);
                if ($this->isGranted('ROLE_MANAGER') && $statusForm->isSubmitted() && $statusForm->isValid()) {
                    $this->newStatus($request, $entityManager, $project, $statusForm);
                    return $this->redirect($request->getUri());

                } else {
                    $discussionForm->handleRequest($request);
                    if ($this->isGranted('ROLE_MANAGER') && $discussionForm->isSubmitted() && $discussionForm->isValid()) {
                        $this->newDiscussion($request, $entityManager, $project, $discussionForm);
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

    /**
     * @Route("/project/{id}/delete", name="task_delete", methods={"POST", "GET"})
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteTask(Task $task, EntityManagerInterface $entityManager)
    {

        $taskId = $task->getId();

        if (!$task) {
            return new JsonResponse([
                'msg' => 'Unable to delete'
            ]);
        }

        $entityManager->remove($task);
        $entityManager->flush();


        return new JsonResponse([
            'deletedTask' => $taskId
        ]);
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
     * @Route("/task/{id}", name="task_view")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @param SubscriptionsRepository $subscriptionsRepository
     * @param Task $task
     * @return Response
     */
    public function taskView(Task $task, Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository, SubscriptionsRepository $subscriptionsRepository)
    {

        $id = $task->getProject()->getId();
        $project = $projectRepository->find($id);
        $subs = $subscriptionsRepository->findByTask($task->getId());

        $commentForm = $this->createForm(CommentFormType::class);
        $devForm = $this->createForm(AssignDevFormType::class, $data = null, array("id" => $this->getUser()->getId()));

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->addComment($task, $request, $entityManager, $commentForm);
            return $this->redirect($request->getUri());
        } else {
            $devForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $devForm->isSubmitted() && $devForm->isValid()) {
                $this->assignDevToTask($task, $request, $entityManager, $devForm, $subscriptionsRepository);
                return $this->redirect($request->getUri());
            }

            return $this->render('project/task.html.twig', [
                'user' => $this->getUser(),
                'task' => $task,
                'commentForm' => $commentForm->createView(),
                'project' => $project,
                'devForm' => $devForm->createView(),
                'subs' => $subs
            ]);
        }

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
            $this->editProject($request, $entityManager, $project, $projectForm);
            return $this->redirectToRoute('index_page');
        }


        return $this->render('project/project_edit.html.twig', [
            'user' => $this->getUser(),
            'projectForm' => $projectForm->createView()
        ]);
    }

    private function editProject(Request $request, EntityManagerInterface $entityManager, Project $project, $projectForm)
    {
        $project = $projectForm->getData();
        $entityManager->persist($project);
        $entityManager->flush();
    }

    /**
     * @Route("/my_tasks/{id}", name="my_tasks")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Project $project
     * @return Response
     */
    public function myTasks(Project $project)
    {
        return $this->render('project/my_tasks.html.twig', [
            'user' => $this->getUser(),
            'project' => $project

        ]);
    }

    /**
     * @Route("/developer_tasks/{id}/{dev_id}", name="developer_tasks", methods={"POST", "GET"})
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function developerTasks(Project $project, EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository)
    {

        $devId = $request->get('dev_id');
        $user = $userRepository->find($devId);

        return $this->render('project/developer_tasks.html.twig', [
            'user' => $user,
            'project' => $project

        ]);

    }


    private function newDiscussion(Request $request, EntityManagerInterface $entityManager, Project $project, $discussionForm)
    {
        $discussion = $discussionForm->getData();
        $discussion->setProject($project);
        $discussion->setCreatedBy($this->getUser()->getFullName());

        $entityManager->persist($discussion);
        $entityManager->flush();

    }

    /**
     * @Route("/project_discussions/{id}", name="project_discussions", methods={"POST", "GET"})
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function showDiscussions(Project $project)
    {
        return $this->render('project/project_discussions.html.twig', [
            'project' => $project,
            'user' => $this->getUser()

        ]);
    }

}

