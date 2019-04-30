<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite pavadinimą',
                    ]),
                ]
            ])
            ->add('deadline', DateTimeType::class, [
                'label' => 'Atsiimti iki',
                'widget' => 'single_text',
                'required' => true,
                'invalid_message' => 'Įveskite laiką',
            ])

            ->add('picture', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                'label' => 'Pasirinkite nuotrauką',
                'required' => false,
                'constraints' => [
                    new Image()
                ]

            ])


            ->add('status', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Ar aktyvuoti produktą?',
                'choices' => [
                    'Aktyvuoti' => true,
                    'Palikti neaktyvų' => false ,
                ]
            ])

            ->add('givenAway', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Gal produktas jau atiduotas?',
                'choices' => [
                    'Atiduotas' => true,
                    'Vis dar ne' => false,
                ]
            ])


            ->add('description', TextareaType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nurodykite bent trumpą aprašymą',
                    ]),
                ]

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
