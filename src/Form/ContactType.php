<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ContactType
 *
 * @package App\Form
 */
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nurodykite savo vardą',
                    ]),
                ],
                'attr' => ['placeholder' => 'Jūsų vardas'],
            ])
            ->add('message', TextareaType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Parašykite žinutę savininkui',
                    ]),
                ],
                'attr' => ['placeholder' => 'Jūsų žinutė savininkui'],
                ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite e paštą',
                    ]),
                ],
                'attr' => ['placeholder' => 'Jūsų elektroninis paštas'],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
