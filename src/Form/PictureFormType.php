<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotNull;

class PictureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control mt-3'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez sélectionner une image',
                        'groups' => [
                            'new'
                        ]
                    ]),
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). La taille maximale autorisée est de {{ limit }} {{ suffix }}',
                        'extensions' => [
                            'jpg' => ['image/jpg', 'image/jpeg'],
                            'jpeg' => ['image/jpeg', 'image/jpg'],
                            'png' => 'image/png'
                        ],
                        'extensionsMessage' => 'L\'extension du fichier est invalide ({{ extension }}). Les extensions autorisées sont {{ extensions }}',
                        'groups' => [
                            'new',
                            'edit'
                        ]
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
