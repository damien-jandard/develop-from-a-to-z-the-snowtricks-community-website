<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Valid;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'autofocus' => true
                ],
                'label' => 'Nom de la figure',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom de la figure.',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom de la figure doit contenir au moins {{ limit }} caractères',
                        'max' => '255',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5
                ],
                'label' => 'Description de la figure',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez décrire la figure.',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ]),
                    new Length([
                        'min' => 20,
                        'minMessage' => 'La description de la figure doit contenir au moins {{ limit }} caractères',
                        'max' => '4096',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ]),
                ],
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Vous devez sélectionner au moins un tag',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ])
                ]
            ])
            ->add('pictures', CollectionType::class, [
                'attr' => [
                    'class' => 'from-check'
                ],
                'label' => false,
                'label_attr' => [
                    'class' => 'form-check mt-3'
                ],
                'entry_type' => PictureFormType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Vous devez ajouter au moins une image',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ]),
                    new Valid()
                ],
            ])
            ->add('videos', CollectionType::class, [
                'attr' => [
                    'class' => 'from-control mt-3'
                ],
                'label' => false,
                'entry_type' => VideoFormType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Vous devez ajouter au moins une vidéo',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ]),
                    new Valid()
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
