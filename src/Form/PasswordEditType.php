<?php


namespace App\Form;

use App\Entity\User;
use App\Validator\CorrectPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite seną slaptažodį',
                    ]),
                    new CorrectPassword()

                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Nauji slaptažodžiai turi sutapti',
                'required' => false,
                'mapped' => false,
                'first_options' => ['label' => false],
                'second_options' => ['label' => false],

                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite naują slaptažodį',
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

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
