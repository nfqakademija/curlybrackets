<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @package App\Controller
 */
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
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response|null
     */
    public function confirmation(
        $hash,
        Request $request,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        UserRepository $userRepository
    ) {

        if ($user = $userRepository->findOneByRegistratingHash($hash)) {
            if ($user->getActivated()) {
                $this->addFlash('danger', 'Vartotojas jau aktyvuotas');
                return $this->redirectToRoute('app_login');
            }

            $user->setActivated(true);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Sveikiname, vartotojas sėkmingai aktyvuotas!');
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        $this->addFlash('danger', 'Pagal pateiktą nuorodą vartotojas nerastas');
        return $this->redirectToRoute('home');
    }
}
