<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class RegistrationFormType
 *
 * @package App\Form
 */
class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('username', TextType::class, [
                'label' => false
            ])
            ->add('email', EmailType::class, [
                'label' => false
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite slaptažodį',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Slaptažodis turi turėti bent {{ limit }} simbolius',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => "/^(?=\D*\d)\S{6,}$/",
                        'match' => true,
                            'message' => 'Slaptažodyje reikalingas bent 1 skaitmuo']),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Jūs turite sutikti su taisyklėmis'
                    ])
                ]
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
