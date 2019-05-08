<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'page.product.product_form.title',
                'translation_domain' => 'content',
                'invalid_message' => 'page.product.product_form.invalid_message',
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
                'constraints' => [
                    new NotBlank(),
                ]
            ])

            ->add('pictureFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'imagine_pattern' => 'square',
                'download_uri' => false,
                'label' => false,
            ])


            ->add('status', ChoiceType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull()
                ],
                'choices' => [
                    'Rodyti produktą' => true,
                    'Nerodyti produkto' => false,

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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
