<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 16.02.19.
 * Time: 15:44
 */

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Discussion;
use App\Entity\HoursOnTask;
use App\Entity\Project;
use App\Entity\Subscriptions;
use App\Entity\Task;
use App\Entity\User;
use App\Form\AddHoursFormType;
use App\Form\AssignDevFormType;
use App\Form\CommentFormType;
use App\Form\DiscussionFormType;
use App\Form\ProjectFormType;
use App\Form\ProjectStatusFormType;
use App\Form\TaskConvertFormType;
use App\Form\TaskFormType;
use App\Repository\CommentsRepository;
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
        $task->setUpdated(true);
        $comments->setUser($this->getUser());
        $comments->setTask($task);
        $entityManager->persist($comments);
        $entityManager->persist($task);
        $entityManager->flush();

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
     * @Route("/project_discussion/{id}/delete", name="discussion_delete", methods={"POST", "GET"})
     * @param Discussion $discussion
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteDiscussion(Discussion $discussion, EntityManagerInterface $entityManager)
    {

        $discussionId = $discussion->getId();

        if (!$discussion) {
            return new JsonResponse([
                'msg' => 'Unable to delete'
            ]);
        }

        $entityManager->remove($discussion);
        $entityManager->flush();


        return new JsonResponse([
            'deletedDiscussion' => $discussionId
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
        $addHoursForm = $this->createForm(AddHoursFormType::class);

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->addComment($task, $request, $entityManager, $commentForm);
            return $this->redirect($request->getUri());
        } else {
            $devForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $devForm->isSubmitted() && $devForm->isValid()) {
                $this->assignDevToTask($task, $request, $entityManager, $devForm, $subscriptionsRepository);
                return $this->redirect($request->getUri());
            } else {
                $addHoursForm->handleRequest($request);
                if ($this->isGranted('ROLE_USER') && $addHoursForm->isSubmitted() && $addHoursForm->isValid()) {
                    $this->addHoursToTask($task, $entityManager, $addHoursForm);
                    return $this->redirect($request->getUri());
                }

                return $this->render('project/task.html.twig', [
                    'user' => $this->getUser(),
                    'task' => $task,
                    'commentForm' => $commentForm->createView(),
                    'project' => $project,
                    'devForm' => $devForm->createView(),
                    'subs' => $subs,
                    'addHoursForm' => $addHoursForm->createView()
                ]);
            }

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

    private function addHoursToTask(Task $task, EntityManagerInterface $entityManager, $addHoursForm)
    {
        $hoursOnTask = $addHoursForm->getData();
        $hoursOnTask->setTask($task);
        $hoursOnTask->setUser($this->getUser());
        $hoursOnTask->setProject($task->getProject());

        $task->setTotalHours($task->getTotalHours() + $hoursOnTask->getHours());

        $entityManager->persist($hoursOnTask);
        $entityManager->flush();
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
     * @Route("/project_tasks/{id}", name="project_tasks", methods={"POST", "GET"})
     * @param Project $project
     * @param UserRepository $userRepository
     * @return Response
     */
    public function showTasks(Project $project, UserRepository $userRepository)
    {
        $devs = $userRepository->devsOnProject($project->getId());
        return $this->render('project/project_tasks.html.twig', [
            'project' => $project,
            'user' => $this->getUser(),
            'devs' => $devs

        ]);
    }

    /**
     * @Route("/task_edit/{id}", name="task_edit", methods={"POST", "GET"})
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function taskEditor (Task $task, Request $request, EntityManagerInterface $entityManager)
    {
        $taskForm = $this->createForm(TaskFormType::class, $task, array('project_id'=>$task->getProject()->getId()));
        $taskForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
            $this->editTask($request, $entityManager, $task, $taskForm);
            return $this->redirectToRoute('index_page');
        }


        return $this->render('project/task_edit.html.twig', [
            'user' => $this->getUser(),
            'taskForm' => $taskForm->createView()
        ]);
    }

    private function editTask(Request $request, EntityManagerInterface $entityManager, Task $task, $taskForm)
    {
        $task = $taskForm->getData();
        $entityManager->persist($task);
        $entityManager->flush();
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
     * @param Project $project
     * @return Response
     */
    public function showDiscussions(Project $project)
    {

        return $this->render('project/project_discussions.html.twig', [
            'project' => $project,
            'user' => $this->getUser()

        ]);
    }
    /**
     * @Route("/single_discussion/{id}", name="discussion_view", methods={"POST", "GET"})
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param Discussion $discussion
     * @param SubscriptionsRepository $subscriptionsRepository
     * @param CommentsRepository $commentsRepository
     * @return Response
     */
    public function singleDiscussionView(Discussion $discussion, Request $request, EntityManagerInterface $entityManager, SubscriptionsRepository $subscriptionsRepository, CommentsRepository $commentsRepository)
    {
        $projectId =$discussion->getProject()->getId();

        $commentForm = $this->createForm(CommentFormType::class);
        $taskForm = $this->createForm(TaskConvertFormType::class,$data = null, array('project_id' => $projectId));

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->addDiscussionComment($discussion, $request, $entityManager, $commentForm);
            return $this->redirect($request->getUri());
        } else {
            $taskForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
                $this->convertToTask($discussion, $request, $entityManager, $taskForm, $subscriptionsRepository, $commentsRepository);
                return $this->redirectToRoute('index_page');
            }
        }

        return $this->render('project/single_discussion.html.twig', [
            'discussion' => $discussion,
            'user' => $this->getUser(),
            'commentForm' => $commentForm->createView(),
            'taskForm' => $taskForm->createView()
            ]);


    }

    private function addDiscussionComment(Discussion $discussion, Request $request, EntityManagerInterface $entityManager, $commentForm)
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
        $comments->setDiscussion($discussion);
        $entityManager->persist($comments);
        $entityManager->flush();
    }

    /**
     * @Route("/subscribe_to_discussion{id}", name="subscribe_to_discussion")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SubscriptionsRepository $subscriptionsRepository
     * @param Discussion $discussion
     * @return Response
     */
    public function subscribeToDiscussion(Discussion $discussion, EntityManagerInterface $entityManager, Request $request, SubscriptionsRepository $subscriptionsRepository)
    {
        $subscription = new Subscriptions();
        $email = $this->getUser()->getEmail();
        $projectId = $discussion->getProject()->getId();

        try {
            $subscription->setUserEmail($email);
            $subscription->setDiscussionId($discussion->getId());
            $entityManager->persist($subscription);
            $entityManager->flush();
        } catch (ConstraintViolationException $constraintViolationException) {

        }


        return $this->redirectToRoute('project_view', array('id' => $projectId));
    }

    private function convertToTask(Discussion $discussion, Request $request, EntityManagerInterface $entityManager, $taskForm, SubscriptionsRepository $subscriptionsRepository, CommentsRepository $commentsRepository)
    {
        $subs = $subscriptionsRepository->findByDiscussion($discussion->getId());
        $comments = $commentsRepository->findCommentsByDiscussion($discussion);

        $task = new Task();
        $task = $taskForm->getData();
        $task->setProject($discussion->getProject());
        $task->setContent($discussion->getContent());
        $task->setName($discussion->getName());
        $task->setCompleted(false);



        $entityManager->persist($task);
        $entityManager->flush();

        foreach ($subs as $sub)
        {
            $sub->setDiscussionId(null);
            $sub->setTaskId($task->getId());
            $entityManager->persist($sub);
            $entityManager->flush();
        }

        foreach ($comments as $comment)
        {
            $comment->setTask($task);
            $discussion->removeComment($comment);

            $entityManager->persist($comment);
            $entityManager->persist($discussion);
            $entityManager->flush();
        }
        $entityManager->remove($discussion);
        $entityManager->flush();
    }



}

