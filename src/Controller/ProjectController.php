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
use App\Form\ProjectFormType;
use App\Entity\ProjectStatus;
use App\Form\ProjectStatusFormType;
use App\Form\RegistrationFormType;
use App\Form\TaskFormType;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProjectController extends AbstractController
{

    /**
     * @Route("/project/{id}", name="project_view")
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */

    public function addDeveloper(Project $project, Request $request, EntityManagerInterface $entityManager,  UserPasswordEncoderInterface $passwordEncoder)
    {

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $form->isSubmitted() && $form->isValid()){
            /**@var User $user */

            $user = $form->getData();
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user ->setRoles(array('ROLE_USER'));
            $user ->addProject($project);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('project/project.html.twig', [

            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' =>$project

        ]);
    }

    /**
     * @Route("/task/{id}", name="task_view")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Project $project
     * @return Response
     */

    public function addTask(Request $request, EntityManagerInterface $entityManager, Project $project)
    {

        $taskForm = $this->createForm(TaskFormType::class, array("project_id" => $project->getId() ) );
        $taskForm ->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()){
        /**@var Task $task */


        $task = $taskForm->getData();
        $task->setProject($project);
        $entityManager->persist($task);
        $entityManager->flush();

        }
        return $this->render('project/task.html.twig', [

            'taskForm' => $taskForm->createView(),
            'user' => $this->getUser(),
            'project' => $project



        ]);
    }

}