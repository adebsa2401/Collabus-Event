<?php

namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('standCount', IntegerType::class, [
                'label' => 'Nombre de places (debout)',
                'attr' => [
                    'placeholder' => 'Nombre de places (debout)',
                ],
            ])
            ->add('sitCount', IntegerType::class, [
                'label' => 'Nombre de places (assis)',
                'attr' => [
                    'placeholder' => 'Nombre de places (assis)',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
