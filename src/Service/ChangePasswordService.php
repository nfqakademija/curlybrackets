<?php


namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ChangePasswordService
 *
 * @package App\Service
 */
class ChangePasswordService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * ChangePasswordService constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param $request
     * @param $user
     */
    public function changePassword(Request $request, User $user): void
    {
        $newPassword = $request->request->get('password_edit')['newPassword']['first'];
        $newPasswordConfirm = $request->request->get('password_edit')['newPassword']['second'];

        $old_pwd = $request->request->get('password_edit')['password'];

        $checkPass = $this->encoder->isPasswordValid($user, $old_pwd);

        if (($newPassword === $newPasswordConfirm) && $checkPass) {
            $encoded = $this->encoder->encodePassword($user, $newPassword);
            $user->setPassword($encoded);
        }
    }
}
