<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 15.02.19.
 * Time: 18:06
 */

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegistrationFormType;


class IndexController extends AbstractController
{


    private function newProject(Request $request, EntityManagerInterface $entityManager, $projectForm)
    {


        /** @var Project $project */

        $project = $projectForm->getData();
        $project->addUser($this->getUser());
        $entityManager->persist($project);
        $entityManager->flush();


    }

    private function addDeveloper(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, $form)
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
        $entityManager->persist($user);
        $entityManager->flush();
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

    /**
     * @param Request $request
     * @Route("/", name="index_page")
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function indexHandler(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {

        $projectForm = $this->createForm(ProjectFormType::class);
        $devForm = $this->createForm(RegistrationFormType::class);

        $projectForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $projectForm->isSubmitted() && $projectForm->isValid()) {
            $this->newProject($request, $entityManager, $projectForm);
            $this->addFlash('success', 'New project created!');
            return $this->redirect($request->getUri());
        } else {
            $devForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $devForm->isSubmitted() && $devForm->isValid()) {
                $this->addDeveloper($request, $entityManager, $passwordEncoder, $devForm);
                return $this->redirect($request->getUri());
            }

            return $this->render('index.html.twig', [
                'projectForm' => $projectForm->createView(),
                'projects' => $projectRepository->findAll(),
                'user' => $this->getUser(),
                'users' => $userRepository->findAllDevelopersArray(),
                'form' => $devForm->createView()

            ]);
        }


    }

    private function updateUser(Request $request, EntityManagerInterface $entityManager, User $user,$updateForm,$passwordEncoder)
    {
        $user = $updateForm->getData();
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
                $updateForm->get('plainPassword')->getData()
            )
        );
        $user->setRoles(array('ROLE_USER'));
        $entityManager->persist($user);
        $entityManager->flush();
    }


    /**
     * @Route("/profile/{id}", name="profile_view")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param User $user
     * @return Response
     */
    public function showProfile(Request $request, EntityManagerInterface $entityManager, User $user,UserPasswordEncoderInterface $passwordEncoder )
    {
        $updateForm = $this->createForm(RegistrationFormType::class, $user);

        $updateUser = $user->getId();
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $updateForm->handleRequest($request);
        if ($userId === $updateUser || $this->isGranted('ROLE_MANAGER') && $updateForm->isSubmitted() && $updateForm->isValid() ){
            $this->updateUser($request,$entityManager,$user,$updateForm, $passwordEncoder);
    }
        if ($userId === $updateUser|| $this->isGranted('ROLE_MANAGER')){
            return $this->render('profile.html.twig', [
                'user'=>$user,
                'updateForm'=>$updateForm->createView()
            ]);
        }return $this->redirectToRoute('index_page');


    }
}
