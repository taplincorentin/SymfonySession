<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\Session2Type;
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

    #[Route('/stession/{id}/edit', name: 'edit_session')]
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

    #[Route('/session/{session_id}/{stagiaire_id}', name: 'remove_stagiaire')]
    #[ParamConverter('session', options: ['id' => 'session_id'])]
    #[ParamConverter('stagiaire', options: ['idS' => 'stagiaire_id'])]
    public function removeStagiaire(Session $session, Stagiaire $Stagiaire) {

        $id = $session->getId();
        $idS = $stagiaire->getId();

        $session->removeStagiaire($Stagiaire);
        $entityManager->flush();
        return ;
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, EntityManagerInterface $entityManager): Response {

        $id = $session->getId();
        $nonInscrits = $entityManager->getRepository(Session::class)->findNonInscrits($id); //obtenir liste de non-inscrits
        
        return $this->render('session/show.html.twig', [
            'session' => $session, 
            'nonInscrits' => $nonInscrits,
        ]);
    }
}
