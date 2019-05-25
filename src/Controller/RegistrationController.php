<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Exception;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use DateTime;
use Twig\Environment;

/**
 * Class RegistrationController
 *
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoginFormAuthenticator $authenticator
     * @param Swift_Mailer $mailer
     * @param EventDispatcherInterface $eventDispatcher
     * @param Environment $templating
     * @return Response
     * @throws Exception
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginFormAuthenticator $authenticator,
        Swift_Mailer $mailer,
        EventDispatcherInterface $eventDispatcher,
        Environment $templating
    ): Response {

        $user = new User();
        $user->setCreatedAt(new DateTime());
        $user->setUpdatedAt(new DateTime());
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (true === $form['agreeTerms']->getData()) {
                $user->agreeToTerms();
            }
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $eventDispatcher->dispatch(
                'user.registered',
                new UserRegisteredEvent($form, $user, $mailer, $templating)
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Sveikiname, vartotojas sukurtas!');

            return $this->render('registration/needConfirmation.html.twig');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
