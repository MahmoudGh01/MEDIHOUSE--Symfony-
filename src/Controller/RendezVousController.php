<?php

namespace App\Controller;
use App\Entity\RendezVous;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RendezVousType;
 class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous', name: 'app_rendez_vous')]
    public function index(): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'controller_name' => 'RendezVousController',
        ]);
    }

    #[Route('/afficheRDV', name: 'afficheR')]

      public function AfficheR(RendezVousRepository $repository)
      {
          $c= $repository->findAll();
          return $this->render("rendez_vous/listR.html.twig", ["RendezVous"=>$c]);
        }
    
        #[Route('/addRendezVous', name: 'app_addRendezVous')]
        public function addRendezVous(ManagerRegistry $doctrine,Request $request)
        {
            $RendezVous= new RendezVous();
            $form=$this->createForm(RendezVousType::class,$RendezVous);
            
            $form->handleRequest($request);
            if($form->isSubmitted()){
                $em =$doctrine->getManager() ;
                $em->persist($RendezVous);
                $em->flush();
                return $this->redirectToRoute("afficheR");
            }
            return $this->renderForm("Rendez_Vous/addRendezVous.html.twig",
                array("formClass"=>$form));
         }
         #[Route('/suppE/{id}', name: 'supprimerR')]

        public function suppC(ManagerRegistry $doctrine,$id,RendezVousRepository $repository)
          {
          //récupérer le classroom à supprimer
              $RendezVous= $repository->find($id);
          //récupérer l'entity manager
              $em= $doctrine->getManager();
              $em->remove($RendezVous);
              $em->flush();
              return $this->redirectToRoute("afficheR");
          } 
          #[Route('/updatC/{id}', name: 'updateR')]

    public function updatC(ManagerRegistry $doctrine,$id,RendezVousRepository $repository,Request $request)
      {
      //récupérer le RendezVous à supprimer
          $RendezVous= $repository->find($id);
          $newRendezVous= new RendezVous();
          $form=$this->createForm(RendezVousType::class,$newRendezVous);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em =$doctrine->getManager() ;
            $RendezVous->setIdRdv($newRendezVous->getIdRdv());
            $RendezVous->setDocteur($newRendezVous->getDocteur());
            $RendezVous->setPatient($newRendezVous->getPatient());
            $RendezVous->setAdresse($newRendezVous->getAdresse());
            $RendezVous->setdate($newRendezVous->getdate());
            $em->flush();
            return $this->redirectToRoute("afficheR");
        }
        return $this->renderForm("Rendez_Vous/updateR.html.twig",
        array("formClass"=>$form));
      } 

    }


