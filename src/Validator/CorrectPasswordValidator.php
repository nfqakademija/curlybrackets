<?php

namespace App\Validator;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CorrectPasswordValidator extends ConstraintValidator
{

    private $encoder;
    private $user;

    public function __construct(UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage)
    {
        $this->encoder = $encoder;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function validate($value, Constraint $constraint): void
    {
        $checkPass = $this->encoder->isPasswordValid($this->user, $value);

        /* @var $constraint CorrectPassword */
        if ($checkPass) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
