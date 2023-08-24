<?php

namespace App\Form;

use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StagiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de famille :'])
            ->add('prenom', TextType::class, ['label' => 'Prénom :'])
            ->add('sexe', TextType::class, ['label' => 'Sexe (F/M/A) :'])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance :'
            ])
            ->add('ville',TextType::class, ['label' => 'Lieu de domiciliation :'])
            ->add('email', TextType::class, ['label' => 'Adresse e-mail  :'])
            ->add('telephone', TextType::class, ['label' => 'Numéro de téléphone :'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
