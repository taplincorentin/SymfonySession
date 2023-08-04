<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
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
    public function new(Request $request): Response {
        $session = new Session();

        $form = $this->createForm(SessionType::class, $session);

        return $this->render('session/new.html.twig', [
            'formAddSession' => $form,
        ]);

    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session): Response {

        return $this->render('session/show.html.twig', [
            'session' => $session
        ]);
    }
}
