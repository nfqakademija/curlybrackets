<?php


namespace App\Event;

use App\Entity\User;
use App\Service\MailingService;
use Symfony\Component\EventDispatcher\Event;

class UserRegisteredEvent extends Event
{
    /**
     * UserRegisteredEvent constructor.
     *
     * @param $form
     * @param User $user
     * @param Swift_Mailer $mailer
     * @param Environment $templating
     * @param MailingService $mailingService
     */
    public function __construct(
        $form,
        User $user

    ) {
        $this->form = $form;
        $this->user = $user;
    }
}
