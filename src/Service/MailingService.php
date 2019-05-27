<?php


namespace App\Service;


use Swift_Message;
use Twig\Environment;

class MailingService
{
    private $mailer;

    private $templating;

    public function __construct(\Swift_Mailer $mailer, Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendMail($data, $recipient, $subject, $twig)
    {

        $message = (new Swift_Message($subject))
            ->setFrom('foodsharinglithuania@gmail.com')
            ->setTo($recipient)
            ->setBody(
                $this->templating->render(
                    $twig,
                    ['data' => $data]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
