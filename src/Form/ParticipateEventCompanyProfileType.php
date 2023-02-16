<?php

namespace App\Form;

use App\Entity\IndividualProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipateEventCompanyProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('participants', EntityType::class, [
                'class' => IndividualProfile::class,
                'choices' => $options['collaborators'],
                'choice_label' => function (IndividualProfile $individualProfile) {
                    return "{$individualProfile->getFirstName()} {$individualProfile->getLastName()} ({$individualProfile->getUser()->getEmail()}))";
                },
                'group_by' => function (IndividualProfile $individualProfile) {
                    return $individualProfile->getCompany()->getName();
                },
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => ParticipateEventCompanyProfile::class,
        ]);

        $resolver->setRequired('collaborators');
    }
}
