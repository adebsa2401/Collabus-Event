<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\EventType as EntityEventType;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Name',
                ],
            ])
            ->add('startedAt', DateType::class, [
                'label' => 'Commence le',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => [
                    'placeholder' => 'Commence le',
                ],
            ])
            ->add('endedAt', DateType::class, [
                'label' => 'Fini le',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => [
                    'placeholder' => 'Fini le',
                ],
            ])
            ->add('type', EntityType::class, [
                'class' => EntityEventType::class,
                'choice_label' => 'name',
                'label' => 'Type',
                'attr' => [
                    'placeholder' => 'Choisir le type',
                ],
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'label' => 'Lieu',
                'attr' => [
                    'placeholder' => 'Choisir le lieu',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
