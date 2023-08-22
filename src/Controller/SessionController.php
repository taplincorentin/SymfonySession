<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\Session2Type;
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
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findBy([], ["dateDebut" => "ASC"]); //get all sessions sorted from startDate

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions
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

    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response {
        
        if(!$session) { //condition if no stagiaire create new one otherwise it's an edit of the existing one
            $session = new Session();
        }

        $form = $this->createForm(Session2Type::class, $session);

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) { //if form submitted and valid
            
            $session = $form->getData();
            $entityManager->persist($session); //prepare
            $entityManager->flush(); //execute

            return $this->redirectToRoute('app_session'); //redirect to sessionList

        }

        return $this->render('session/edit.html.twig', [
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
    public function removeStagiaireFromSession(Session $session_id, int $stagiaire_id, EntityManagerInterface $entityManager) {
        
        $session = $entityManager->getRepository(Session::class)->findOneBy(['id'=>$session_id]);
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->findOneBy(['id'=>$stagiaire_id]);
        
        
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
    public function show(Session $session, EntityManagerInterface $entityManager): Response {

        $nonInscrits = $entityManager->getRepository(Session::class)->findNonInscrits($session->getId()); //obtenir liste de non-inscrits
        

        $programme = new Programme();
        $form = $this->createForm(ProgrammeFormType::class, $programme);
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) { //if form submitted and valid
            
            $programme = $form->getData();
            $entityManager->persist($programme); //prepare
            $entityManager->flush(); //execute

            return $this->redirectToRoute('session/show.html.twig');

        }


        return $this->render('session/show.html.twig', [
            'session' => $session, 
            'nonInscrits' => $nonInscrits,
            'formAddProgramme' => $form,
        ]);
    }
}
