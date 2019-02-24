<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);
        /**@var User $user */
        $user = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password

            $file = $request->files->get('registration_form')['avatar'];

            if (isset($file)){
                $uploads_directory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' .$file->guessExtension();
                $file->move(
                    $uploads_directory,
                    $filename

                );
                $user->setAvatar($filename);
            }
            $user->setRoles(array('ROLE_MANAGER'));
            $user->setAddedBy('0');
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
