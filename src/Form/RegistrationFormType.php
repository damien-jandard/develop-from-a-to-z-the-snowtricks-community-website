<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom d\'utilisateur',
                    'autofocus' => true
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                    'autocomplete' => 'email'
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'invalid_message' => 'Les champs de mot de passe doivent correspondre.',
                'first_options' => [

                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Mot de passe'
                    ]
                ],
                'second_options' => [

                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Confirmation du mot de passe'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                        'message' => 'Votre mot de passe doit comporter au minimum 8 caractères, et être composé d\'au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial (#?!@$%^&*-)'
                    ]),
                ],
            ])

            ->add('RGPDConsent', CheckboxType::class, [
                'mapped' => false,
                'label' => 'En cliquant sur le bouton d\'inscription, vous consentez à la collecte et au traitement de vos données personnelles conformément à notre politique de confidentialité et aux exigences du Règlement Général sur la Protection des Données (RGPD).',
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'En cliquant sur le bouton d\'inscription, vous consentez à la collecte et au traitement de vos données personnelles conformément à notre politique de confidentialité et aux exigences du Règlement Général sur la Protection des Données (RGPD).',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
