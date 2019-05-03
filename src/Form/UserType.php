<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Vardas',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Pavardė',
                'required' => false,

            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite e paštą',
                    ])
                ]
            ])
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'imagine_pattern' => 'square',
                'download_uri' => false,
                'label' => 'Pasirinkite nuotrauką',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
