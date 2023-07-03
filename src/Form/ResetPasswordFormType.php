<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('resetPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'attr' => [
                'autocomplete' => 'new-password',
            ],
            'invalid_message' => 'Les champs de mot de passe doivent correspondre.',
            'first_options' => [
                
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nouveau mot de passe'
                ]
            ],
            'second_options' => [
                
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Confirmation du nouveau mot de passe'
                ]
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe.',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
