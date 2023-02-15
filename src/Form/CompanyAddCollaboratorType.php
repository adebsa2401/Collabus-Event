<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\IndividualProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyAddCollaboratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('collaborators', EntityType::class, [
                'class' => IndividualProfile::class,
                'choices' => $options['individualProfiles'],
                'choice_label' => function (IndividualProfile $individualProfile) {
                    return "{$individualProfile->getFirstName()} {$individualProfile->getLastName()} ({$individualProfile->getUser()->getEmail()}))";
                },
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'label' => 'Collaborateurs',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Company::class,
        ]);

        $resolver->setRequired('individualProfiles');
    }
}
