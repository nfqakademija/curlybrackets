<?php


namespace App\Event\Subscriber;

use App\Event\UserRegisteredEvent;
use Exception;
use Swift_Message;
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
        dump($event);
        $event->user->setRegistrationHash($hash);
            $message = (new Swift_Message('Foodsharing paskyra sukurta vartotojui: '
                . $event->form['username']->getData()))
                ->setFrom('foodsharinglithuania@gmail.com')
                ->setTo($event->user->getEmail())
                ->setBody(
                    $event->templating->render(
                        'emails/register.html.twig',
                        ['user' => $event->user]
                    ),
                    'text/html'
                );

            $event->mailer->send($message);
    }
}
