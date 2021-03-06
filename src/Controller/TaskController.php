<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/18/19
 * Time: 11:56 AM
 */

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Subscriptions;
use App\Entity\Task;
use App\Form\AddHoursFormType;
use App\Form\AssignDevFormType;
use App\Form\CommentFormType;
use App\Form\ProjectStatusFormType;
use App\Form\TaskFormType;
use App\Repository\ProjectRepository;
use App\Repository\SubscriptionsRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\WebHookController;
use App\Services\Fetcher;

class TaskController extends AbstractController
{

    /**
     * @Symfony\Component\Routing\Annotation\Route("/task/{id}", name="task_view")
     * @param               Request $request
     * @param               EntityManagerInterface $entityManager
     * @param               ProjectRepository $projectRepository
     * @param               SubscriptionsRepository $subscriptionsRepository
     * @param               Task $task
     * @param               Fetcher $fetcher
     * @return              \Symfony\Component\HttpFoundation\Response
     */
    public function taskView(
        Task $task,
        Request $request,
        EntityManagerInterface $entityManager,
        ProjectRepository $projectRepository,
        SubscriptionsRepository $subscriptionsRepository,
        Fetcher $fetcher
    ) {
        $users = $task->getProject()->getUsers();

        if ($fetcher->checkUsers($users) !== true) {
            return $this->redirectToRoute('index_page');
        }

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
                $this->assignDevToTask($task, $entityManager, $devForm, $subscriptionsRepository);
                return $this->redirect($request->getUri());
            } else {
                $addHoursForm->handleRequest($request);
                if ($this->isGranted('ROLE_USER') && $addHoursForm->isSubmitted() && $addHoursForm->isValid()) {
                    $this->addHoursToTask($task, $entityManager, $addHoursForm);
                    return $this->redirect($request->getUri());
                }


                if ($fetcher->checkSubscription() == 0) {
                    return $this->redirectToRoute('index_page');
                }


                return $this->render(
                    'project/task.html.twig',
                    [
                        'user' => $this->getUser(),
                        'task' => $task,
                        'commentForm' => $commentForm->createView(),
                        'project' => $project,
                        'devForm' => $devForm->createView(),
                        'subs' => $subs,
                        'addHoursForm' => $addHoursForm->createView()
                    ]
                );
            }
        }
    }

    private function editTask(
        EntityManagerInterface $entityManager,
        $taskForm
    ) {
        try {
            $task = $taskForm->getData();
            $entityManager->persist($task);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->addFlash('warning', 'All Fields Are Required');
        }
    }

    private function addComment(
        Task $task,
        Request $request,
        EntityManagerInterface $entityManager,
        $commentForm
    ) {
        /**
         * @var \App\Entity\Comments $comments
         */
        $comments = $commentForm->getData();

        $files = $request->files->get('comment_form')['images'];

        if (!empty($files)) {
            foreach ($files as $file) {
                $uploadDirectory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploadDirectory,
                    $filename
                );
                $images[] = $filename;
            }

            $comments->setImages($images);
        }

        try {
            $task->setUpdated(true);
            $comments->setUser($this->getUser());
            $comments->setTask($task);
            $entityManager->persist($comments);
            $entityManager->persist($task);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->addFlash('warning', 'You have to add comment before posting');
        }
    }

    private function assignDevToTask(
        Task $task,
        EntityManagerInterface $entityManager,
        $devForm,
        SubscriptionsRepository $subscriptionsRepository
    ) {


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

    /**
     * @Symfony\Component\Routing\Annotation\Route(
     *     "/manager/task/{id}/remove_dev/{dev_id}",
     *     name="remove_dev",
     *     methods={"POST", "GET"}
     *     )
     * @param                           Task $task
     * @param                           Fetcher $fetcher
     * @param                           Request $request
     * @param                           UserRepository $userRepository
     * @param                           EntityManagerInterface $entityManager
     * @param                           SubscriptionsRepository $subscriptionsRepository
     * @return                          \Symfony\Component\HttpFoundation\Response
     */
    public function removeDevFromTask(
        Task $task,
        Fetcher $fetcher,
        EntityManagerInterface $entityManager,
        Request $request,
        UserRepository $userRepository,
        SubscriptionsRepository $subscriptionsRepository
    ) {
        $managerId = $task->getProject()->getAddedBy();


        if ($fetcher->checkManager($managerId) !== true) {
            return $this->redirectToRoute('index_page');
        }

        $devId = $request->get('dev_id');
        $dev = $userRepository->find($devId);

        $subscription = $subscriptionsRepository->checkSubscriber($task->getId(), $dev->getEmail());

        $task->removeUser($dev);
        $entityManager->persist($task);
        $entityManager->remove($subscription[0]);
        $entityManager->flush();

        return $this->redirectToRoute('task_view', array('id' => $task->getId()));
    }

    private function addHoursToTask(
        Task $task,
        EntityManagerInterface $entityManager,
        $addHoursForm
    ) {
        $hoursOnTask = $addHoursForm->getData();
        $hoursOnTask->setTask($task);
        $hoursOnTask->setUser($this->getUser());
        $hoursOnTask->setProject($task->getProject());

        $task->setTotalHours($task->getTotalHours() + $hoursOnTask->getHours());

        $entityManager->persist($hoursOnTask);
        $entityManager->flush();
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route(
     *     "/manager/project/{id}/complete",
     *     name="task_completed",
     *     methods={"POST", "GET"}
     *     )
     * @param                           Task $task
     * @param                           Fetcher $fetcher
     * @param                           WebHookController $webHookController
     * @param                           EntityManagerInterface $entityManager
     * @return                          \Symfony\Component\HttpFoundation\Response
     */
    public function taskCompleted(
        Task $task,
        EntityManagerInterface $entityManager,
        WebHookController $webHookController,
        Fetcher $fetcher
    ) {
        $managerId = $task->getProject()->getAddedBy();

        if ($fetcher->checkManager($managerId) !== true) {
            return $this->redirectToRoute('index_page');
        }

        $id = $task->getProject()->getId();
        $task->setCompleted(true);

        $entityManager->persist($task);
        $entityManager->flush();

        $name = $task->getName();
        $taskId = $task->getId();
        $name = str_replace(' ', '_', $name);
        $type = $task->getType();

        $webHookController->deleteBranch($taskId, $name, $type);

        return $this->redirectToRoute('completed_tasks', array('id' => $id));
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route
     * (
     *     "/manager/project/{id}/reopen",
     *     name="task_reopen",
     *      methods={"POST", "GET"}
     * )
     * @param                         Task $task
     * @param                         EntityManagerInterface $entityManager
     * @param                         WebHookController $webHookController
     * @param                         Fetcher $fetcher
     * @return                        \Symfony\Component\HttpFoundation\Response
     */

    public function taskReopen(
        Task $task,
        EntityManagerInterface $entityManager,
        WebHookController $webHookController,
        Fetcher $fetcher
    ) {

        $managerId = $task->getProject()->getAddedBy();

        if ($fetcher->checkManager($managerId) !== true) {
            return $this->redirectToRoute('index_page');
        }

        $projectId = $task->getProject()->getId();
        $task->setCompleted(false);

        $entityManager->persist($task);
        $entityManager->flush();

        $name = $task->getName();
        $id = $task->getId();
        $name = str_replace(' ', '_', $name);
        $type = $task->getType();

        $webHookController->createBranch($id, $name, $type);

        return $this->redirectToRoute('project_tasks', array('id' => $projectId));
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/subscribe_to_task/{id}", name="subscribe_to_task")
     * @param                            EntityManagerInterface $entityManager
     * @param                            Task $task
     * @param                            Fetcher $fetcher
     * @return                           \Symfony\Component\HttpFoundation\Response
     */
    public function subscribeToTask(
        Task $task,
        EntityManagerInterface $entityManager,
        Fetcher $fetcher
    ) {

        $users = $task->getProject()->getUsers();

        if ($fetcher->checkUsers($users) !== true) {
            return $this->redirectToRoute('index_page');
        }

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
     * @Symfony\Component\Routing\Annotation\Route("/manager/completed_tasks/{id}", name="completed_tasks")
     * @param                          Project $project
     * @param                          Fetcher $fetcher
     * @return                         \Symfony\Component\HttpFoundation\Response
     */
    public function completedTaskView(Project $project, Fetcher $fetcher)
    {
        $managerId = $project->getAddedBy();

        if ($fetcher->checkManager($managerId) !== true) {
            return $this->redirectToRoute('index_page');
        }

        return $this->render(
            'project/completed_tasks.html.twig',
            [
                'user' => $this->getUser(),
                'project' => $project

            ]
        );
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/project_tasks/{id}", name="project_tasks", methods={"POST", "GET"})
     * @param                        Project $project
     * @param                        UserRepository $userRepository
     * @param                        Request $request
     * @param                        Fetcher $fetcher
     * @param                        WebHookController $webHookController
     * @param                        EntityManagerInterface $entityManager
     * @return                       \Symfony\Component\HttpFoundation\Response
     */
    public function showTasks(
        Project $project,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        WebHookController $webHookController,
        Fetcher $fetcher
    ) {

        $users = $project->getUsers();

        if ($fetcher->checkUsers($users) !== true) {
            return $this->redirectToRoute('index_page');
        }

        $devs = $userRepository->devsOnProject($project->getId());
        $taskForm = $this->createForm(TaskFormType::class, $data = null, array("project_id" => $project->getId()));
        $statusForm = $this->createForm(ProjectStatusFormType::class);

        $taskForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
            $this->addTask($request, $entityManager, $project, $taskForm, $webHookController);
            return $this->redirect($request->getUri());
        } else {
            $statusForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $statusForm->isSubmitted() && $statusForm->isValid()) {
                $this->newStatus($entityManager, $project, $statusForm);
                return $this->redirect($request->getUri());
            }
        }

        return $this->render(
            'project/project_tasks.html.twig',
            [
                'project' => $project,
                'user' => $this->getUser(),
                'devs' => $devs,
                'taskForm' => $taskForm->createView(),
                'statusForm' => $statusForm->createView(),

            ]
        );
    }

    private function addTask(
        Request $request,
        EntityManagerInterface $entityManager,
        Project $project,
        $taskForm,
        WebHookController $webHookController
    ) {

        /**
         * @var Task $task
         */
        $task = $taskForm->getData();
        $files = $request->files->get('task_form')['images'];
        $name = $task->getName();
        $name = str_replace(' ', '_', $name);
        $type = $task->getType();

        if (!empty($files)) {
            foreach ($files as $file) {
                $uploadDirectory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploadDirectory,
                    $filename
                );
                $images[] = $filename;
            }

            $task->setImages($images);
        }

        try {
            $task->setProject($project);
            $task->setCompleted(false);
            $entityManager->persist($task);
            $entityManager->flush();
            $id = $task->getId();
            $webHookController->createBranch($id, $name, $type);
        } catch (\Exception $exception) {
            $this->addFlash('warning', 'All Fields Are Required');
        }
    }

    private function newStatus(
        EntityManagerInterface $entityManager,
        Project $project,
        $statusForm
    ) {
        $status = $statusForm->getData();
        $project->addProjectStatus($status);
        $entityManager->persist($project);
        $entityManager->flush();
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/manager/task_edit/{id}", name="task_edit", methods={"POST", "GET"})
     * @param                    Task $task
     * @param                    EntityManagerInterface $entityManager
     * @param                    Request $request
     * @param                    Fetcher $fetcher
     * @return                   \Symfony\Component\HttpFoundation\Response
     */
    public function taskEditor(
        Task $task,
        Request $request,
        EntityManagerInterface $entityManager,
        Fetcher $fetcher
    ) {
        $managerId = $task->getProject()->getAddedBy();

        if ($fetcher->checkManager($managerId) !== true) {
            return $this->redirectToRoute('index_page');
        }

        $taskForm = $this->createForm(TaskFormType::class, $task, array('project_id' => $task->getProject()->getId()));
        try {
            $taskForm->handleRequest($request);
            if ($taskForm->isSubmitted() && $taskForm->isValid()) {
                $this->editTask($entityManager, $taskForm);
                return $this->redirectToRoute('project_tasks', array('id' => $task->getProject()->getId()));
            }
        } catch (\Exception $exception) {
            $this->addFlash('warning', 'All Fields Are Required');
        }


        return $this->render(
            'project/task_edit.html.twig',
            [
                'user' => $this->getUser(),
                'taskForm' => $taskForm->createView()
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route(
     *     "/developer_tasks/{id}/{dev_id}",
     *     name="developer_tasks",
     *     methods={"POST", "GET"}
     *     )
     * @param                                   UserRepository $userRepository
     * @param                                   Project $project
     * @param                                   Request $request
     * @return                                  \Symfony\Component\HttpFoundation\Response
     */
    public function developerTasks(
        Project $project,
        Request $request,
        UserRepository $userRepository
    ) {

        $devId = $request->get('dev_id');
        $user = $userRepository->find($devId);

        return $this->render(
            'project/developer_tasks.html.twig',
            [
                'user' => $user,
                'project' => $project

            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/my_tasks/{id}", name="my_tasks")
     * @param                   Project $project
     * @return                  \Symfony\Component\HttpFoundation\Response
     */
    public function myTasks(Project $project)
    {
        return $this->render(
            'project/my_tasks.html.twig',
            [
                'user' => $this->getUser(),
                'project' => $project

            ]
        );
    }
}
