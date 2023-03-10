<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Fiche;
use App\Entity\RendezVous;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RendezVousRepository;
use App\Repository\FicheRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use App\Form\FicheType;

#[Route('/docteur')]
class DocteurController extends AbstractController
{
    #[Route('/', name: 'app_docteur')]
    public function index(): Response
    {
        return $this->render('docteur/index.html.twig', [
            'controller_name' => 'DocteurController',
        ]);
    }
    #[Route('/register', name: 'app_docteur_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_DOCTEUR']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_docteur');
        }

        return $this->render('docteur/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/list', name: 'app_docteur_listRDV')]
    public function listRDV(Security $security, RendezVousRepository $RendezVousRepository): Response
    { {
            if (!$security->isGranted('ROLE_DOCTOR')) {
                return $this->redirectToRoute('app_login');
            }
            $user = $security->getUser();
            return $this->render('bord.html.twig', [
                'RendezVouss' => $RendezVousRepository->findByDocteur($user),
            ]);
        }
    }
    #[Route('/Confirmer/{id}', name: 'Confirmer')]

    public function Confirmer($id, RendezVousRepository $repository)
    {
        //récupérer le RendezVous à supprimer
        $RendezVous = $repository->find($id);
        // $newRendezVous= new RendezVous();

        $RendezVous->setStatut("Confirmé");
        $repository->save($RendezVous, true);
        return $this->redirectToRoute("app_docteur_listRDV");
    }
    #[Route('/Annuler/{id}', name: 'Annuler')]

    public function Annuler($id, RendezVousRepository $repository)
    {
        //récupérer le RendezVous à supprimer
        $RendezVous = $repository->find($id);
        // $newRendezVous= new RendezVous();
        $RendezVous->setStatut("Annulé");

        $repository->save($RendezVous, true);
        return $this->redirectToRoute("app_docteur_listRDV");
    }

    #[Route('/list/fiche', name: 'app_docteur_listFiche')]
    public function listFiche(Security $security, FicheRepository $FicheRepository): Response
    { {
            if (!$security->isGranted('ROLE_DOCTOR')) {
                return $this->redirectToRoute('app_login');
            }
            $user = $security->getUser();

            return $this->render('docteur/listFiche.html.twig', [
                'Fiche' => $FicheRepository->findAll(),
            ]);
        }
    }
    #[Route('/fiche/{id}', name: 'app_docteur_Fiche')]
    public function showFiche(FicheRepository $FicheMedRepository, $id, RendezVousRepository $RendezVousRepository): Response
    {
        return $this->render('docteur/showFiche.html.twig', [
            'Fiche' => $FicheMedRepository->find($id),
            'rendezVous' => $RendezVousRepository->findByFiche($FicheMedRepository->find($id)),
        ]);
    }

    #[Route('/fiche/new/{id}', name: 'app_add_Fiche')]
    public function addFiche(RendezVousRepository $Repository, $id, UserRepository $repo, Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        $RendezVous = $Repository->find($id);

        $patient = $RendezVous->getPatient();
        $docteur = $security->getToken()->getUser();


        $Fiche = new Fiche();


        $Fiche->setDocteur($docteur);
        $Fiche->setPatient($patient);


        $form = $this->createForm(FicheType::class, $Fiche);


        $form->handleRequest($request);
        if ($form->isSubmitted()) {


            $entityManager->persist($Fiche);
            $entityManager->flush();
            // $this->sendEmail($mailer,$RendezVous->getPatient()->getEmail());
            // $repository = $entityManager->getRepository(RendezVous::class);


            $RendezVous->setFiche($Fiche);
            $entityManager->persist($RendezVous);
            $entityManager->flush();
            return $this->redirectToRoute('app_docteur_listFiche');
        }
        return $this->render(
            'docteur/newFiche.html.twig',
            [
                'Fiche' => $Fiche,
                'form' => $form->createView()
            ]
        );
    }
}
