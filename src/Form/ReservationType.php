<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('start', \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class, [
                'widget' => 'single_text',  // Utiliser un champ unique pour la date et l'heure
                'input' => 'datetime',  // Ce paramètre indique à Symfony que l'entrée doit être interprétée comme un objet DateTime
                'html5'=> false,  // Désactiver le type HTML5 pour le champ de formulaire
                'format' => 'dd-MM-yyyy HH:mm:ss', // Formater l'affichage
            ])
            ->add('end', \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5'=> false,  // Désactiver le type HTML5 pour le champ de formulaire

                'format' => 'dd-MM-yyyy HH:mm:ss', // Même format pour end
            ])
            ->add('client', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('barber', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
        // Désactivation du CSRF pour les API
        $builder->add('csrf_token', HiddenType::class, [
            'mapped' => false,
            'attr' => ['style' => 'display:none'],
            'required' => false,
        ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
