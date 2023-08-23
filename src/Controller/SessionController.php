<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\ProgrammeFormType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository, EntityManagerInterface $entityManager): Response
    {
        $sessions = $sessionRepository->findBy([], ["dateDebut" => "ASC"]); //get all sessions sorted from startDate
        dd($sessions);
        $pastSessions = $sessions->findPastSessions();
        //$currentSessions = $sessions->findCurrentSessions(); 
        //$futureSessions = $sessions->findFutureSessions();
        
        
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
            'currentSessions' => $currentSessions,
            'pastSessions' => $pastSessions,
            'futureSessions' => $futureSessions
        ]);
    }

    #[Route('/session/new', name: 'new_session')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response {

        $session = new Session();

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) { //if form submitted and valid
            
            $session = $form->getData();
            $entityManager->persist($session); //prepare
            $entityManager->flush(); //execute

            return $this->redirectToRoute('app_session'); //redirect to sessionList

        }

        return $this->render('session/new.html.twig', [
            'formAddSession' => $form,
        ]);

    }


    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager) {
        
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }


    #[Route('/session/{session_id}/{stagiaire_id}/remove', name: 'remove_stagiaire')]
    public function removeStagiaireFromSession(Session $session_id, Stagiaire $stagiaire_id, EntityManagerInterface $entityManager) {
   
        $session = $session_id;
        $stagiaire = $stagiaire_id;
        
        
        $session->removeStagiaire($stagiaire);
        $entityManager->persist($session);
        $entityManager->flush();
        
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }



    //move stagiaire to a session
    #[Route('/session/{session_id}/{stagiaire_id}/move', name: 'enlist_stagiaire')]
    public function moveStagiaireToSession(Session $session_id, int $stagiaire_id, EntityManagerInterface $entityManager) {

        $session = $entityManager->getRepository(Session::class)->findOneBy(['id'=>$session_id]);
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->findOneBy(['id'=>$stagiaire_id]);
        
        $session->addStagiaire($stagiaire);
        $entityManager->persist($session);
        $entityManager->flush();

        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }



    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, Request $request, EntityManagerInterface $entityManager): Response {

        $nonInscrits = $entityManager->getRepository(Session::class)->findNonInscrits($session->getId()); //obtenir liste de non-inscrits
        

        $programme = new Programme();
        $form = $this->createForm(ProgrammeFormType::class, $programme);
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $programme = $form->getData();
            $programme->setSession($session);
            $entityManager->persist($programme); //prepare
            $entityManager->flush(); //execute

            
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);

        }


        return $this->render('session/show.html.twig', [
            'session' => $session, 
            'nonInscrits' => $nonInscrits,
            'formAddProgramme' => $form,
        ]);
    }
}
