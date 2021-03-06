<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class UserType
 *
 * @package App\Form
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Jūsų vardas',
                'attr' => ['placeholder' => 'Jūsų vardas'],
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Jūsų pavardė',
                'attr' => ['placeholder' => 'Jūsų pavardė'],
                'required' => false,

            ])
            ->add('email', EmailType::class, [
                'label' => 'Jūsų elektroninis paštas',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite e paštą',
                    ])
                ],
            ])
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'imagine_pattern' => 'square',
                'download_uri' => false,
                'label' => 'Jūsų avataras',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
