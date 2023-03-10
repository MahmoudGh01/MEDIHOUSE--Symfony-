<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(RendezVousRepository $RendezVousRepository): Response
    {    $events= $RendezVousRepository->findAll();
        $rdvs = [];

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getDate()->format('Y-m-d H:i:s'),
               
                'title' => $event->getPatient()->getnom(),
                
                
                
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('test/index.html.twig', compact('data'));
    }

    #[Route('/stats', name: 'app_stat')]
    public function statistiques(RendezVousRepository $RendezVousRepository, UserRepository $UserRepository){
        // On va chercher toutes les catégories
        $RoleCount = [];
        
        //$users = $UserRepository->findAll();
        $Admins = $UserRepository->findByRole('ROLE_Admin');
        $Patients = $UserRepository->findByRole('ROLE_PATIENT');
        $Docteurs = $UserRepository->findByRole('ROLE_DOCTOR');
       $para =$UserRepository->findByRole('ROLE_PARA');
        

        // On "démonte" les données pour les séparer tel qu'attendu par ChartJS
            
            $RoleCount = [count($Docteurs),count($Patients),count($Admins),count($para)];
       

            $RendezVous = $RendezVousRepository->countByDate();

            $dates = [];
            $RendezVousCount = [];
    
            // On "démonte" les données pour les séparer tel qu'attendu par ChartJS
            foreach($RendezVous as $RendezVou){
                $dates[] = $RendezVou['date'];
                $RendezVousCount[] = $RendezVou['count'];
            }
       

        return $this->render('test/Chart.html.twig', [
          'RoleCount' => json_encode($RoleCount),
          'dates' => json_encode($dates),
          'RendezVousCount' => json_encode($RendezVousCount),
        ]);
    }



}
