<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\FormateurRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $stagiaireRepository, FormateurRepository $formateurRepository): Response
    {
        $stagiaires = $stagiaireRepository->findBy([], ['nom' => 'ASC']); //get all stagiaires
        $formateurs = $formateurRepository->findBy([], ['nom' => 'ASC']); //get all formateurs
        
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
            'formateurs' => $formateurs
        ]);
    }

    #[Route('/stagiaire/new', name: 'new_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    public function new_edit(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response {
        
        if(!$stagiaire) { //condition if no stagiaire create new one otherwise it's an edit of the existing one
            $stagiaire = new Stagiaire();
        }

        $form = $this->createForm(StagiaireType::class, $stagiaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { //if form submitted and valid
            
            $stagiaire = $form->getData();
            $entityManager->persist($stagiaire); //prepare
            $entityManager->flush(); //execute

            return $this->redirectToRoute('app_stagiaire'); //redirect to stagiaireList

        }
        return $this->render('stagiaire/new.html.twig', [
            'formAddStagiaire' => $form,
        ]);

    }

    #[Route('/stagiaire/{id}/delete', name: 'delete_stagiaire')]
    public function delete(Stagiaire $stagiaire, EntityManagerInterface $entityManager) {
        $entityManager->remove($stagiaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_stagiaire');
    }


    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire = null): Response {
        if($stagiaire){
            return $this->render('stagiaire/show.html.twig', [
                'stagiaire' => $stagiaire
            ]);
        }
        else {
            return $this->redirectToRoute('app_stagiaire');
        }
    }
}
