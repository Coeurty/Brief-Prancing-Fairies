<?php 

// src/Form/UserType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    ->add('nickname', TextType::class, [
        'label' => 'Pseudo',
    ])
    ->add('profilePicture', FileType::class, [
        'label' => ' ',
        'required' => false,
        'mapped' => false,
        'attr' => ['accept' => 'image/*'],
        'constraints' => [
            new File([
                'maxSize' => '500K', // max size 500kb
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                ],
                'mimeTypesMessage' => 'Veuillez ajouter une image aux formats valides (JPEG, PNG ou GIF)',
            ])
        ],
    ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
