<?php


namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserRegisteredEvent
 *
 * @package App\Event
 */
class UserRegisteredEvent extends Event
{
    /**
     * UserRegisteredEvent constructor.
     *
     * @param $form
     * @param User $user
     */
    public function __construct(
        $form,
        User $user

    ) {
        $this->form = $form;
        $this->user = $user;
    }
}
