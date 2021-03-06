<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Bug;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminEditCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('textComments')
            ->add('date')
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email'
            ])
            ->add('idBug', EntityType::class, [
                'class' => Bug::class,
                'choice_label' => 'titleBug'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
