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
     * @var GuardAuthenticatorHandler
     */
    private $guardAuthenticatorHandler;
    /**
     * @var LoginFormAuthenticator
     */
    private $loginFormAuthenticator;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * SecurityController constructor.
     *
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @param UserRepository $userRepository
     * @param AuthenticationUtils $authenticationUtils
     */
    public function __construct(
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator,
        UserRepository $userRepository,
        AuthenticationUtils $authenticationUtils
    ) {
       $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
       $this->loginFormAuthenticator  = $loginFormAuthenticator;
       $this->userRepository = $userRepository;
       $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/login", name="app_login")
     * @return Response
     */
    public function login(): Response
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

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
     * @return RedirectResponse|Response|null
     */
    public function confirmation(
        $hash,
        Request $request
    ) {

        if ($user = $this->userRepository->findOneByRegistratingHash($hash)) {
            if ($user->getActivated()) {
                $this->addFlash('danger', 'Vartotojas jau aktyvuotas');
                return $this->redirectToRoute('app_login');
            }

            $user->setActivated(true);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Sveikiname, vartotojas sėkmingai aktyvuotas!');
            return $this->guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->authenticator,
                'main' // firewall name in security.yaml
            );
        }

        $this->addFlash('danger', 'Pagal pateiktą nuorodą vartotojas nerastas');
        return $this->redirectToRoute('home');
    }
}
