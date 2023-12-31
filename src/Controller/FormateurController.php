<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $formateurRepository): Response
    {
        $formateurs = $formateurRepository->findAll(); //get all formateurs
        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs
        ]);
    }

    #[Route('/formateur/new', name: 'new_formateur')]
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')]
    public function new_edit(Formateur $formateur = null, Request $request, EntityManagerInterface $entityManager): Response {
        
        if(!$formateur) { //condition if no formateur create new one otherwise it's an edit of the existing one
            $formateur = new Formateur();
        }

        $form = $this->createForm(FormateurType::class, $formateur);

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) { //if form submitted and valid
            
            $formateur = $form->getData();
            $entityManager->persist($formateur); //prepare
            $entityManager->flush(); //execute

            return $this->redirectToRoute('app_stagiaire'); //redirect to formateurList

        }
        return $this->render('formateur/new.html.twig', [
            'formAddFormateur' => $form,
        ]);

    }

    #[Route('/formateur/{id}/delete', name: 'delete_formateur')]
    public function delete(Formateur $formateur, EntityManagerInterface $entityManager) {
        $entityManager->remove($formateur);
        $entityManager->flush();

        return $this->redirectToRoute('app_stagiaire');
    }

    #[Route('/formateur/{id}', name: 'show_formateur')]
    public function show(Formateur $formateur = null): Response {

        if($formateur){
            return $this->render('formateur/show.html.twig', [
                'formateur' => $formateur
            ]);
        }
        else {
            return $this->redirectToRoute('app_stagiaire');
        }
    }
}
