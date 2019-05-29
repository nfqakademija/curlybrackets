<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordEditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\ChangePasswordService;
use App\Service\CheckOwnershipService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @var CheckOwnershipService
     */
    private $checkOwnershipService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ChangePasswordService
     */
    private $changePasswordService;

    /**
     * UserController constructor.
     *
     * @param CheckOwnershipService $checkOwnershipService
     * @param UserRepository $userRepository
     * @param ChangePasswordService $changePasswordService
     */
    public function __construct(
        CheckOwnershipService $checkOwnershipService,
        UserRepository $userRepository,
        ChangePasswordService $changePasswordService
    ) {
        $this->checkOwnershipService = $checkOwnershipService;
        $this->userRepository = $userRepository;
        $this->changePasswordService = $changePasswordService;
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        if ($this->checkOwnershipService->isCorrectUser($user)) {
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }
        throw $this->createAccessDeniedException();
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, User $user): Response
    {
        if (!$this->checkOwnershipService->isCorrectUser($user)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->makeForm(UserType::class, $request, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($user);

            $this->addFlash('success', 'Jūsų informacija sėkmingai pakeista!');

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/password", name="user_password", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function password(Request $request, User $user): Response
    {
        if ($user->getId() === $this->get('security.token_storage')->getToken()->getUser()->getId()) {
            $form = $this->makeForm(PasswordEditType::class, $request, $user);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->changePasswordService->changePassword($request, $user);

                $this->userRepository->save($user);

                $this->addFlash('success', 'Jūsų slaptažodis sėkmingai pakeistas!');

                return $this->redirectToRoute('user_show', [
                    'id' => $user->getId(),
                ]);
            }

            return $this->render('user/passwordEdit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }
        throw $this->createAccessDeniedException();
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->checkOwnershipService->isCorrectUser($user)) {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $this->userRepository->remove($user);

                $this->addFlash('danger', 'Vartotojas sėkmingai pašalintas');
            }
            return $this->redirectToRoute('user_index');
        }
        throw $this->createAccessDeniedException();
    }

    /**
     * @param $type
     * @param $request
     * @param $entity
     * @return FormInterface
     */
    private function makeForm($type, $request, $entity): FormInterface
    {
        $form = $this->createForm($type, $entity);
        $form->handleRequest($request);
        return $form;
    }
}
