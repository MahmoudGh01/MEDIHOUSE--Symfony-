<?php

namespace App\Controller;
use App\Entity\Fiche;
use App\Form\FicheType;
use App\Repository\FicheRepository;
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

      public function AfficheR(RendezVousRepository $repository,FicheRepository $ficherepository)
      {     $fiche= $ficherepository->findAll();
          $c= $repository->findAll();
          return $this->render("rendez_vous/listR.html.twig", [
            "RendezVous"=>$c,
            "fiche"=>$fiche
        ]);
        }
    
        #[Route('/addRendezVous', name: 'app_addRendezVous')]
        public function addRendezVous(ManagerRegistry $doctrine,$id,Request $request)
        {
            $RendezVous= new RendezVous();
            $form=$this->createForm(RendezVousType::class,$RendezVous);
            
            $form->handleRequest($request);
            if($form->isSubmitted()&& $form->isValid()){
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
         // $newRendezVous= new RendezVous();
          $form=$this->createForm(RendezVousType::class,$RendezVous);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $repository->save($RendezVous, true);
            return $this->redirectToRoute("afficheR");
        }
        return $this->renderForm('rendez_vous/updateR.html.twig', [
            'RendezVous' => $RendezVous,
            'formClass' => $form,
        ]);
      }
      #[Route('/liste/{id}', name: 'listeByF')]

      public function listebyf(ManagerRegistry $doctrine,$id, RendezVousRepository $repository,Request $request)
        { $RendezVous= $doctrine
            ->getRepository(RendezVous::class)
            ->ListRvByFiche($id);
            return $this->render('fiche/show.html.twig',['RendezVous'=>$RendezVous]);
        }
    }


