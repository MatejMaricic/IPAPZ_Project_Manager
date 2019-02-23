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
use App\Entity\User;
use App\Entity\Task;
use App\Entity\ProjectStatus;
use App\Form\AssignDevFormType;
use App\Form\CommentFormType;
use App\Form\TaskFormType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
        $entityManager->persist($task);
        $entityManager->flush();

    }

    private function assignDev(Project $project, Request $request, EntityManagerInterface $entityManager, $devForm)
    {
        $user = $devForm->getData();
        foreach ($user as $singleuser){
            foreach ($singleuser as $item) {
                $project->addUser($item);
            }
        }
        $entityManager->persist($project);
        $entityManager->flush();
    }

    public function addComment(Task $task,  Request $request, EntityManagerInterface $entityManager,$commentForm)
    {
        /**@var Comments $comments*/
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
     * @return Response
     */
    public function projectHandler(Project $project, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {

        $devForm = $this->createForm(AssignDevFormType::class);
        $taskForm = $this->createForm(TaskFormType::class, $data = null, array("project_id" => $project->getId()));
        $taskForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
            $this->addTask($request, $entityManager, $project, $taskForm);
            return $this->redirect($request->getUri());
        }else {
            $devForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $devForm->isSubmitted() && $devForm->isValid()){
                $this->assignDev($project,$request,$entityManager,$devForm);
                return $this->redirect($request->getUri());
            }
        }
        return $this->render('project/project.html.twig', [
            'taskForm' => $taskForm->createView(),
            'user' => $this->getUser(),
            'project' => $project,
            'devForm' => $devForm->createView()
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

        $newStatus = $project_status_repository->find( $statusId);

        $oldStatusID = $task->getStatus();
        $task->setStatus( $newStatus );
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
     * @param Task $task
     * @return Response
     */
    public function taskView(Task $task,  Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository)
    {

        $id= $task->getProject()->getId();
        $project = $projectRepository->find($id);

        $commentForm = $this->createForm(CommentFormType::class);
        $devForm = $this->createForm(AssignDevFormType::class, $task);

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()){
            $this->addComment($task,$request,$entityManager,$commentForm);
            return $this->redirect($request->getUri());
        }

        return $this->render('project/task.html.twig', [
            'user' => $this->getUser(),
            'task' => $task,
            'commentForm' => $commentForm->createView(),
            'project' => $project,
            'devForm' => $devForm->createView()
        ]);
    }

}

