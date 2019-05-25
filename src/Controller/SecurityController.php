<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name = "app_logout")
     */
    public function logout(): void
    {
    }


    /**
     * @Route("/confirm/{hash}", name="confirmation")
     * @param $hash
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response|null
     */
    public function confirmation(
        $hash,
        Request $request,
        EntityManagerInterface $em,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ) {
        $repository = $em->getRePository(User::class);
        if ($user = $repository->findByregistrationHash($hash)) {
            $user[0]->setActivated(true);

            $this->addFlash('success', 'Sveikiname, vartotojas sėkmingai aktyvuotas!');
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user[0],
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        $this->addFlash('danger', 'Pagal pateiktą nuorodą vartotojas nerastas');
        return $this->redirectToRoute('home');
    }
}
