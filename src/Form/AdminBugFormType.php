<?php

namespace App\Form;

use App\Entity\Bug;
use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminBugFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titleBug')
            ->add('subtitleBug')
            ->add('smallTextBug')
            ->add('descriptionTextBug')
            ->add('descriptionImgBug', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*'
                        ]
                    ])
                ]
            ])
            ->add('severityBug', ChoiceType::class, [
                'choices' => [
                    'choix de la sévérité :' => [
                        'Insignifiant' => 'Insignifiant',
                        'Faible' => 'Faible',
                        'Modéré' => 'Modéré',
                        'Fort' => 'Fort',
                        'Jeux condamné/lourdement impacté' => 'Jeux condamné/lourdement impacté'
                    ]
                ]
            ])
            ->add('frequencyBug', ChoiceType::class, [
                'choices' => [
                    'choix de la fréquence :' => [
                        'Exceptionnel' => 'Exceptionnel',
                        'Rare' => 'Rare',
                        'Peu courant' => 'Peu courant',
                        'Assez régulier' => 'Assez régulier',
                        'Fréquent' => 'Fréquent',
                        'Systématique' => 'Systématique'
                    ]
                ]
            ])
            ->add('published')
            ->add('idGame', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'nameGame'
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bug::class,
        ]);
    }
}
