<?php

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PhotoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom'
            ])
            ->add('file', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Fichier'
            ])
            ->add('image', FileType::class, [
                "label" => "l'image",
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/png',
                            'image/svg+xml',
                            'image/jpg',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez proposer une image valide.',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
