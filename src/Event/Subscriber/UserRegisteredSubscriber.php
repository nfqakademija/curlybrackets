<?php


namespace App\Event\Subscriber;

use App\Event\UserRegisteredEvent;
use App\Service\MailingService;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserRegisteredSubscriber
 *
 * @package App\Event\Subscriber
 */
class UserRegisteredSubscriber implements EventSubscriberInterface
{

    /**
     * @var MailingService
     */
    private $mailingService;

    /**
     * UserRegisteredSubscriber constructor.
     *
     * @param MailingService $mailingService
     */
    public function __construct(MailingService $mailingService)
    {
        $this->mailingService = $mailingService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'user.registered' => [
                ['sendMail']
            ]
        ];
    }

    /**
     * @return string
     * @throws Exception
     */
    private function createHash(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @param UserRegisteredEvent $event
     * @throws Exception
     */
    public function sendMail(UserRegisteredEvent $event): void
    {
        $hash = $this->createHash();
        $event->user->setRegistrationHash($hash);
        $subject = 'Foodsharing paskyra sukurta vartotojui: '
            . $event->form['username']->getData();
        $recipient = $event->user->getEmail();
        $data = $event->user;
        $twig = 'emails/register.html.twig';

        $this->mailingService->sendMail($data, $recipient, $subject, $twig);
    }
}
