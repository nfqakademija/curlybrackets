<?php


namespace App\Event;

use App\Entity\User;
use Swift_Mailer;
use Symfony\Component\EventDispatcher\Event;
use Twig\Environment;

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
     */
    public function __construct($form, User $user, Swift_Mailer $mailer, Environment $templating)
    {
        $this->form = $form;
        $this->user = $user;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }
}
