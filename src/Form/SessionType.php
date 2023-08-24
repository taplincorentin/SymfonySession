<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre de la session :'])
            ->add('nbPlaces', NumberType::class, ['label' => 'Nombre de places disponibles :'])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début :'
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin :'
            ])
            ->add('formateur', EntityType::class,[
                'class' => Formateur::class,
                'label' => 'Formateur de la session :'
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
