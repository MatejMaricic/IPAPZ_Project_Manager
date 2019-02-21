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
use App\Entity\Task;
use App\Form\RegistrationFormType;
use App\Form\TaskFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProjectController extends AbstractController
{

    private function addDeveloper(Project $project, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, $form)
    {
        /**@var User $user */
        $user = $form->getData();
        $file = $request->files->get('registration_form')['avatar'];

        if (isset($file)) {
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename

            );
            $user->setAvatar($filename);
        }

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        $user->setRoles(array('ROLE_USER'));
        $user->addProject($project);
        $entityManager->persist($user);
        $entityManager->flush();
    }


    private function addTask(Request $request, EntityManagerInterface $entityManager, Project $project, $taskForm)
    {

        /**@var Task $task */
        $task = $taskForm->getData();
        $files = $request->files->get('task_form')['images'];

        if (isset($files)) {

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
        $form = $this->createForm(RegistrationFormType::class);
        $taskForm = $this->createForm(TaskFormType::class, $data = null, array("project_id" => $project->getId()));

        $form->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $form->isSubmitted() && $form->isValid()) {
            $this->addDeveloper($project, $request, $entityManager, $passwordEncoder, $form);
            return $this->redirect($request->getUri());
        } else {
            $taskForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
                $this->addTask($request, $entityManager, $project, $taskForm);
                return $this->redirect($request->getUri());
            }
            return $this->render('project/project.html.twig', [
                'form' => $form->createView(),
                'taskForm' => $taskForm->createView(),
                'user' => $this->getUser(),
                'project' => $project
            ]);
        }


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

}


