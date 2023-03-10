<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;
use App\Form\UserType1;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{
    //Profile
    #[Route('/profile', name: 'user_profile')]
    public function userProfile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('profile.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/admin_profile', name: 'admin_profile')]
    public function adminProfile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('profileA.html.twig', [
            'user' => $user,

        ]);
    }
    //Edit Profile
    #[Route('/profile/{id}', name: 'user_profile_edit', methods: ['GET', 'POST'])]
    public function editUserProfile(Request $request, user $user, UserRepository $UserRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(UserType1::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $UserRepository->save($user, true);

            return $this->redirectToRoute('user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile_edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin_profile/{id}', name: 'admin_profile_edit', methods: ['GET', 'POST'])]
    public function editAdminProfile(Request $request, user $user, UserRepository $UserRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(UserType1::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $UserRepository->save($user, true);

            return $this->redirectToRoute('admin_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    //Lists
    #[Route('/doctors', name: 'user_doctors')]
    public function listDoctor(UserRepository $userRepository): Response
    {

        // Vérifie que l'utilisateur connecté a le rôle ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupère la liste des utilisateurs ayant le rôle ROLE_DOCTOR
        $doctors = $userRepository->findByRole('ROLE_DOCTOR');

        // Affiche la vue avec la liste des utilisateurs
        return $this->render('user/list_doctor.html.twig', [
            'doctors' => $doctors,
        ]);
    }

    #[Route('/patients', name: 'user_patients')]
    public function patientList(UserRepository $userRepository): Response
    {

        // Vérifie que l'utilisateur connecté a le rôle ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupère la liste des utilisateurs ayant le rôle ROLE_PATIENT
        $patients = $userRepository->findByRole('ROLE_PATIENT');

        // Affiche la vue avec la liste des utilisateurs
        return $this->render('user/list_patient.html.twig', [
            'patients' => $patients,
        ]);
    }

    #[Route('/paras', name: 'user_paras')]
    public function listPara(UserRepository $userRepository): Response
    {

        // Vérifie que l'utilisateur connecté a le rôle ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupère la liste des utilisateurs ayant le rôle ROLE_PARA
        $paras = $userRepository->findByRole('ROLE_PARA');

        // Affiche la vue avec la liste des utilisateurs
        return $this->render('user/list_para.html.twig', [
            'paras' => $paras,
        ]);
    }

    //INDEX

    #[Route('/home', name: 'app_patient_index', methods: ['GET'])]
    public function home(UserRepository $patientRepository): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('home/index.html.twig', [
            'patients' => $patientRepository->findAll(),
        ]);
    }
    #[Route('/admin', name: 'app_Admin_index', methods: ['GET'])]
    public function Admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('Admin.html.twig');
    }

    //Register

    #[Route('/registration', name: 'app_registration')]
    public function index(): Response
    {
        return $this->render('registration/register.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }

    #[Route('/registerAdmin', name: 'app_registerA')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_ADMIN']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerA.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerP', name: 'app_registerP')]
    public function registerP(MailerInterface $mailer, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_PATIENT']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new Email())
                ->from('achref.chaabani@esprit.tn')
                ->to('mahmoud.gharbi@esprit.tn')

                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerP.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerD', name: 'app_registerD')]
    public function registerD(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_DOCTOR']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerD.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerPara', name: 'app_registerPara')]
    public function registerPara(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_PARA']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerPara.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('ban/{id}', name: 'ban')]
    public function ban($id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setActivate(false);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_user_index');
    }

    #[Route('activate/{id}', name: 'activate')]
    public function activate($id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setActivate(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_user_index');
    }
}
