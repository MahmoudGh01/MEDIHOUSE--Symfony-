<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;
use App\Repository\RendezVousRepository;

#[Route('/patient')]
class PatientController extends AbstractController
{
    #[Route('/', name: 'app_patient')]
    public function index(UserRepository $repo): Response
    {
        $docteurs = $repo->findByRole('ROLE_DOCTOR');

        return $this->render('patient/list_docteur.html.twig', [
            'docteurs' => $docteurs,
        ]);
    }
    #[Route('/', name: 'app_patient_listd')]
    public function list(UserRepository $repo): Response
    {
        $docteurs = $repo->findByPatient();

        return $this->render('patient/list_docteur.html.twig', [
            'docteurs' => $docteurs,
        ]);
    }
    #[Route('/rendez/vous', name: 'app_RendezVous')]
    public function aff(Security $security, RendezVousRepository $RendezVousRepository): Response
    {
        if (!$security->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $security->getUser();
        return $this->render('rendez_vous/index.html.twig', [
            'RendezVouss' => $RendezVousRepository->findByPatient($user),
        ]);
        if (!$security->isGranted('ROLE_DOCTOR')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $security->getUser();
        return $this->render('rendez_vous/index.html.twig', [
            'RendezVouss' => $RendezVousRepository->findByDocteur($user),
        ]);
    }
    #[Route('/register', name: 'app_patient_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // 1) build the form
        $patient = new User();
        $form = $this->createForm(RegistrationFormType::class, $patient);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $patient->setRoles(['ROLE_PATIENT']);
            $patient->setPassword(
                $userPasswordHasher->hashPassword(
                    $patient,
                    $form->get('plainPassword')->getData()
                )
            );
            // 4) save the User!

            $entityManager->persist($patient);
            $entityManager->flush();

            return $this->redirectToRoute('app_patient');
        }

        return $this->render(
            'patient/register.html.twig',
            ['registrationForm' => $form->createView()]
        );
    }


    #[Route('/Reserver/{id}', name: 'app_reserver', methods: ['GET', 'POST'])]
    public function reserver(MailerInterface $mailer, $id, UserRepository $repo, Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$security->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('app_login');
        }
        $RendezVous = new RendezVous();
        $patient = new User($security->getToken()->getUser());
        // $patient->addFich($RendezVous->getFiche());


        $RendezVous->setPatient($patient);
        $RendezVous->setDocteur($repo->find($id));
        $RendezVous->setStatut("Pending");


        $form = $this->createForm(RendezVousType::class, $RendezVous);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            $entityManager->persist($RendezVous);
            $entityManager->flush();
            //$this->sendEmail($mailer, $RendezVous->getPatient()->getEmail());

            return $this->redirectToRoute('app_patient');
        }

        return $this->render(
            'patient/reserver.html.twig',
            [
                'rendezvous' => $RendezVous,
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/rendez/vous', name: 'listRendezVous')]
    public function listR(Security $security, RendezVousRepository $RendezVousRepository): Response
    {
        if (!$security->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $security->getUser();
        return $this->render('patient/index.html.twig', [
            'RendezVouss' => $RendezVousRepository->findByPatient($user),
        ]);
    }

    public function sendEmail(MailerInterface $mailer, String $emailTo)
    {
        $email = (new Email())
            ->from('mahmoud.gharbi@esprit.tn')
            ->to($emailTo)
            ->subject('Hello from Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<div class="success-cont">
        <i class="fas fa-check"></i>
        <h3>Appointment booked Successfully!</h3>
        <p>Appointment booked with <strong>Dr. Darren Elder</strong><br> on <strong>12 Nov
                2019 5:00PM to 6:00PM</strong></p>

    </div>');

        $mailer->send($email);

        return new Response("Success");
    }
}
