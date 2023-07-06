<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                        'max' => '255'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir adresse email.',
                    ]),
                    new Email([
                        'message' => 'L\'adresse email "{{ value }}" n\'est pas une adresse email valide',
                    ]),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'invalid_message' => 'Les champs de mot de passe doivent correspondre.',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation du nouveau mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                        'message' => 'Votre mot de passe doit comporter au minimum 8 caractères, et être composé d\'au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial (#?!@$%^&*-)'
                    ]),
                ],
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image de profil',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). La taille maximale autorisée est de {{ limit }} {{ suffix }}',
                        'extensions' => [
                            'jpg' => 'image/jpg',
                            'jpeg' => 'image/jpg',
                            'png' => 'image/png'
                        ],
                        'extensionsMessage' => 'L\'extension du fichier est invalide ({{ extension }}). Les extensions autorisées sont {{ extensions }}'
                    ])
                ]
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
