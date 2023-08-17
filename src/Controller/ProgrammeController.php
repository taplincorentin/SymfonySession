<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Form\ProgrammeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgrammeController extends AbstractController
{
    #[Route('/programme', name: 'app_programme')]
    public function index(): Response
    {
        return $this->render('programme/index.html.twig', [
            'controller_name' => 'ProgrammeController',
        ]);
    }


    #[Route('/programme/new', name: 'new_programme')]
    #[Route('/programme/{id}/edit', name: 'edit_programme')]
    public function new_edit(Programme $programme = null, Request $request, EntityManagerInterface $entityManager): Response {
        
        if(!$programme) { //condition if no programme create new one otherwise it's an edit of the existing one
            $programme = new Programme();
        }

        $form = $this->createForm(ProgrammeFormType::class, $programme);

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) { //if form submitted and valid
            
            $programme = $form->getData();
            $entityManager->persist($programme); //prepare
            $entityManager->flush(); //execute

            return $this->redirectToRoute('app_session'); //redirect to sessionList

        }

        return $this->render('programme/new.html.twig', [
            'formAddProgramme' => $form,
        ]);

    }

    #[Route('/programme/{id}/delete', name: 'delete_programme')]
    public function delete(Programme $programme, EntityManagerInterface $entityManager) {
        $entityManager->remove($programme);
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }
}
