<?php


namespace App\Event;

use App\Entity\User;
use App\Service\MailingService;
use Symfony\Component\EventDispatcher\Event;

/**
 * @property User user
 * @property Environment templating
 * @property  form
 * @property Swift_Mailer mailer
 */
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
        User $user,
        MailingService $mailingService
    ) {
        $this->form = $form;
        $this->user = $user;
        $this->mailingService = $mailingService;
    }
}
