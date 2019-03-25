<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 15.02.19.
 * Time: 18:06
 */

namespace App\Controller;

use App\Entity\HoursOnTask;
use App\Entity\Project;
use App\Entity\User;
use App\Form\AddHoursFormType;
use App\Form\DatePickerFormType;
use App\Form\ProjectFormType;
use App\Form\SearchHoursFormType;
use App\Repository\HoursOnTaskRepository;
use App\Repository\ProjectRepository;
use App\Repository\TransactionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegistrationFormType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Braintree_Gateway;
use App\Services\Fetcher;

class IndexController extends AbstractController
{

    /**
     * @param      Request $request
     * @Symfony\Component\Routing\Annotation\Route("/", name="index_page")
     * @param      EntityManagerInterface $entityManager
     * @param      ProjectRepository $projectRepository
     * @param      UserRepository $userRepository
     * @param      UserPasswordEncoderInterface $passwordEncoder
     * @param      Fetcher $fetcher
     * @return     \Symfony\Component\HttpFoundation\Response
     */
    public function indexHandler(
        Request $request,
        EntityManagerInterface $entityManager,
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        Fetcher $fetcher
    )
    {


        $projectForm = $this->createForm(ProjectFormType::class);
        $devForm = $this->createForm(RegistrationFormType::class);

        $projectForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $projectForm->isSubmitted() && $projectForm->isValid()) {
            $this->newProject($entityManager, $projectForm);
            $this->addFlash('success', 'New project created!');
            return $this->redirect($request->getUri());
        } else {
            $devForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $devForm->isSubmitted() && $devForm->isValid()) {
                $this->addDeveloper($request, $entityManager, $passwordEncoder, $devForm);
                return $this->redirect($request->getUri());
            }


            if ($fetcher->checkSubscription() == 0) {
                $gateway = $this->gateway()->ClientToken()->generate();


                return $this->render(
                    'payment.html.twig',
                    [
                        'user' => $this->getUser(),
                        'collab' => $this->getUser()->getCollaboration(),
                        'gateway' => $gateway,
                        'amount' => $fetcher->subscriptionAmount($this->getUser()->getCollaboration(), $userRepository)
                    ]
                );
            } elseif ($this->isGranted('ROLE_ADMIN')) {
                return $this->render(
                    'admin.html.twig',
                    [
                        'projects' => $projectRepository->findAll(),
                        'user' => $this->getUser(),
                        'users' => $userRepository->findAllManagersArray(),


                    ]
                );
            }


            return $this->render(
                'index.html.twig',
                [
                    'projectForm' => $projectForm->createView(),
                    'projects' => $projectRepository->findAll(),
                    'user' => $this->getUser(),
                    'users' => $userRepository->findAllDevelopersArray(),
                    'form' => $devForm->createView(),

                ]
            );
        }
    }

    private function gateway()
    {
        $gateway = new Braintree_Gateway(
            [
                'environment' => 'sandbox',
                'merchantId' => 'qmk79j9h7rxpjg8t',
                'publicKey' => 'pnz7bb5774j2j3n4',
                'privateKey' => '7c0e8443e507a26409dc23f6ca1afcb6'
            ]
        );

        return $gateway;
    }


    private function newProject(
        EntityManagerInterface $entityManager,
        $projectForm
    )
    {


        /**
         * @var Project $project
         */

        $project = $projectForm->getData();
        $project->addUser($this->getUser());
        $project->setCompleted(false);
        $entityManager->persist($project);
        $entityManager->flush();
    }

    private function addDeveloper(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        $form
    )
    {
        /**
         * @var User $user
         */
        $user = $form->getData();
        $file = $request->files->get('registration_form')['avatar'];

        try {
            if (isset($file)) {
                $uploadDirectory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploadDirectory,
                    $filename
                );
                $user->setAvatar($filename);
            }

            $user->setAddedBy($this->getUser()->getId());
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(array('ROLE_USER'));
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
        }
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/profile/{id}", name="profile_view")
     * @param                  Request $request
     * @param                  EntityManagerInterface $entityManager
     * @param                  UserPasswordEncoderInterface $passwordEncoder
     * @param                  User $user
     * @return                 \Symfony\Component\HttpFoundation\Response
     */
    public function showProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        User $user,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {

        $updateForm = $this->createForm(RegistrationFormType::class, $user);

        $updateUser = $user->getId();
        $userId = $this->get('security.token_storage')
            ->getToken()
            ->getUser()
            ->getId();

        if ($userId === $updateUser) {
            $updateForm->handleRequest($request);

            if ($updateForm->isSubmitted() && $updateForm->isValid()) {
                $this->updateUser($request, $entityManager, $user, $updateForm, $passwordEncoder);
            }

            return $this->render(
                'profile.html.twig',
                [
                    'user' => $user,
                    'updateForm' => $updateForm->createView()
                ]
            );
        } else {
            return $this->redirectToRoute('index_page');
        }
    }

    private function updateUser(
        Request $request,
        EntityManagerInterface $entityManager,
        $updateForm,
        $passwordEncoder
    )
    {
        $user = $updateForm->getData();
        $file = $request->files->get('registration_form')['avatar'];

        if (isset($file)) {
            $uploadDirectory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploadDirectory,
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

        $id = $user->getAddedBy();
        if ($id == 0) {
            $user->setRoles(array('ROLE_MANAGER'));
        } else {
            $user->setRoles(array('ROLE_USER'));
        }

        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/hours_management", name="hours_management")
     * @param                      ProjectRepository $projectRepository
     * @param                      UserRepository $userRepository
     * @return                     \Symfony\Component\HttpFoundation\Response $response
     */

    public function hoursManagementView(
        ProjectRepository $projectRepository,
        UserRepository $userRepository
    )
    {
        $projects = $projectRepository->findAll();
        $developers = $userRepository->findAllDevelopersArray();

        if ($this->isGranted('ROLE_MANAGER')) {
            return $this->render(
                'hours_management.html.twig',
                [
                    'user' => $this->getUser(),
                    'developers' => $developers,
                    'projects' => $projects
                ]
            );
        }

        return $this->redirectToRoute('index_page');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route
     * (
     *     "/project_hours/{id}/{value}",
     *     defaults={"value" = 0},
     *     name="project_hours",
     *     methods={"POST", "GET"}
     * )
     * @param                                HoursOnTaskRepository $hoursOnTaskRepository
     * @param                                Project $project
     * @param                                Request $request
     * @return                               \Symfony\Component\HttpFoundation\Response $response
     */

    public function projectHoursManagement(
        HoursOnTaskRepository $hoursOnTaskRepository,
        Project $project,
        Request $request
    )
    {
        $total = 0;
        $id = $project->getId();
        $hoursOnProject = $hoursOnTaskRepository->findHoursByProject($id);
        $export = $request->get('value');
        $dateForm = $this->createForm(DatePickerFormType::class);

        $dateForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $dateForm->isSubmitted() && $dateForm->isValid()) {
            $hoursOnProject = $this->findHoursByDate($dateForm, $hoursOnTaskRepository, $project);
        }


        foreach ($hoursOnProject as $singleCommit) {
            $total += $singleCommit->getHours();
        }

        if ($export == 1) {
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($pdfOptions);
            $html = $this->renderView(
                'export_for_project.html.twig',
                [
                    'title' => "Welcome to our PDF Test",
                    'user' => $this->getUser(),
                    'hoursOnProject' => $hoursOnProject,
                    'project' => $project,
                    'total' => $total
                ]
            );

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream(
                "{$project->getName()}.pdf",
                [
                    "Attachment" => true
                ]
            );
        }


        return $this->render(
            'project_hours.html.twig',
            [
                'user' => $this->getUser(),
                'hoursOnProject' => $hoursOnProject,
                'project' => $project,
                'total' => $total,
                'dateForm' => $dateForm->createView()
            ]
        );
    }

    private function findHoursByCriteria($searchForm, $hoursOnTaskRepository)
    {
        $data = $searchForm->getData();
        $project = $data['project'];
        $date = $data['date'];
        $billable = $data['billable'];

        $hoursForUser = $hoursOnTaskRepository->findByCriteria($project, $date, $billable);

        return $hoursForUser;
    }

    private function findHoursByDate($dateForm, $hoursOnTaskRepository, $project)
    {
        $data = $dateForm->getData();
        $date = $data['date'];

        $hoursForUser = $hoursOnTaskRepository->findByDate($date, $project);

        return $hoursForUser;
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/user_hours/{id}", name="user_hours")
     * @param                     HoursOnTaskRepository $hoursOnTaskRepository
     * @param                     User $user
     * @param                     Request $request
     * @return                    \Symfony\Component\HttpFoundation\Response $response
     */
    public function userHoursManagement(
        HoursOnTaskRepository $hoursOnTaskRepository,
        User $user,
        Request $request
    )
    {
        $total = 0;
        $id = $user->getId();
        $hoursForUser = $hoursOnTaskRepository->findHoursByUser($id);
        $searchForm = $this->createForm(SearchHoursFormType::class, $data = null, array('user' => $user));


        $searchForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $searchForm->isSubmitted() && $searchForm->isValid()) {
            $hoursForUser = $this->findHoursByCriteria($searchForm, $hoursOnTaskRepository);
        }


        foreach ($hoursForUser as $singleCommit) {
            $total += $singleCommit->getHours();
        }

        return $this->render(
            'user_hours.html.twig',
            [
                'user' => $this->getUser(),
                'hoursForUser' => $hoursForUser,
                'total' => $total,
                'searchForm' => $searchForm->createView()
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route
     * (
     *     "/edit_user_hours/{id}",
     *     name="edit_user_hours"
     * )
     * @param                          HoursOnTask $hoursOnTask
     * @param                          EntityManagerInterface $entityManager
     * @param                          Request $request
     * @return                         \Symfony\Component\HttpFoundation\Response $response
     */
    public function editUserHours(
        HoursOnTask $hoursOnTask,
        Request $request,
        EntityManagerInterface $entityManager
    )
    {
        $editHoursForm = $this->createForm(AddHoursFormType::class, $hoursOnTask);
        $id = $hoursOnTask->getUser()->getId();

        $editHoursForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $editHoursForm->isSubmitted() && $editHoursForm->isValid()) {
            $hoursOnTask = $editHoursForm->getData();
            $entityManager->persist($hoursOnTask);
            $entityManager->flush();

            return $this->redirectToRoute('user_hours', array('id' => $id));
        }

        return $this->render(
            'edit_user_hours.html.twig',
            [
                'user' => $this->getUser(),
                'editHoursForm' => $editHoursForm->createView()
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("my_payments", name="my_payments")
     * @param                TransactionsRepository $transactionsRepository
     * @return               \Symfony\Component\HttpFoundation\Response
     */
    public function myPayments(TransactionsRepository $transactionsRepository)
    {
        $transactions = $transactionsRepository->findByBuyerEmail(
            $this->getUser()->getEmail()
        );

        if ($this->isGranted('ROLE_MANAGER')) {
            return $this->render(
                'invoice.html.twig',
                [
                    'user' => $this->getUser(),
                    'transactions' => $transactions
                ]
            );
        }
    }
}
