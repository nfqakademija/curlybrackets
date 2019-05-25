<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordEditType;
use App\Form\UserType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/", name="user_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        if ($user->getId() === $this->get('security.token_storage')->getToken()->getUser()->getId()) {
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }
        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, User $user): Response
    {
//todo galima iskelt i servisa
        if ($user->getId() === $this->get('security.token_storage')->getToken()->getUser()->getId()) {
            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setUpdatedAt(new DateTime('now'));
                $this->getDoctrine()->getManager()->flush();
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
        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }

    /**
     * @Route("/{id}/password", name="user_password", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function password(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        if ($user->getId() === $this->get('security.token_storage')->getToken()->getUser()->getId()) {
            $form = $this->createForm(PasswordEditType::class, $user);

            $form->handleRequest($request);
// todo gal i servisa
            if ($form->isSubmitted() && $form->isValid()) {
                $newPassword = $request->request->get('password_edit')['newPassword']['first'];
                $newPasswordConfirm = $request->request->get('password_edit')['newPassword']['second'];

                $old_pwd = $request->request->get('password_edit')['password'];

                $checkPass = $encoder->isPasswordValid($user, $old_pwd);

                if (($newPassword === $newPasswordConfirm) && $checkPass) {
                    $encoded = $encoder->encodePassword($user, $newPassword);
                    $user->setPassword($encoded);
                }

                $this->getDoctrine()->getManager()->flush();
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
        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($user->getId() === $this->get('security.token_storage')->getToken()->getUser()->getId()) {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('danger', 'Vartotojas sėkmingai pašalintas');
            }

            return $this->redirectToRoute('user_index');
        }

        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }
}
