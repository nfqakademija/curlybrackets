<?php


namespace App\Service;

use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailingService
 *
 * @package App\Service
 */
class MailingService
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $templating;

    /**
     * MailingService constructor.
     *
     * @param Swift_Mailer $mailer
     * @param Environment $templating
     */
    public function __construct(Swift_Mailer $mailer, Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param $data
     * @param $recipient
     * @param $subject
     * @param $twig
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendMail($data, $recipient, $subject, $twig): void
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
