<?php


namespace App\Event\Subscriber;

use App\Event\UserRegisteredEvent;
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

        $event->mailingService->sendMail($data, $recipient, $subject, $twig);
    }
}
