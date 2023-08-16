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
    public function new(Request $request, EntityManagerInterface $entityManager): Response {
        $formateur = new Formateur();

        $form = $this->createForm(FormateurType::class, $formateur);

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) { //if form submitted and valid
            
            $formateur = $form->getData();
            $entityManager->persist($formateur); //prepare
            $entityManager->flush(); //execute

            return $this->redirectToRoute('app_formateur'); //redirect to formateurList

        }
        return $this->render('formateur/new.html.twig', [
            'formAddFormateur' => $form,
        ]);

    }

    #[Route('/formateur/{id}/delete', name: 'delete_formateur')]
    public function delete(Formateur $formateur, EntityManagerInterface $entityManager) {
        $entityManager->remove($formateur);
        $entityManager->flush();

        return $this->redirectToRoute('app_formateur');
    }

    #[Route('/formateur/{id}', name: 'show_formateur')]
    public function show(Formateur $formateur): Response {

        return $this->render('formateur/show.html.twig', [
            'formateur' => $formateur
        ]);
    }
}
