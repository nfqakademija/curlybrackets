<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\User;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/location")
 */
class LocationController extends AbstractController
{
    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * LocationController constructor.
     *
     * @param LocationRepository $locationRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        LocationRepository $locationRepository,
        UserRepository $userRepository
    ) {
        $this->locationRepository = $locationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/set", name="location_set", methods={"GET","POST"})
     * @param Request $request
     * @param UserInterface|null $user
     * @return Response
     */
    public function set(Request $request, UserInterface $user = null): Response
    {
        $location = new Location();
        $form = $this->makeForm($request, $location);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->locationRepository->save($location);

            $this->setUsersLocation($user, $location);

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('location/set.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/setRedirect", name="location_redirect", methods={"GET","POST"})
     * @param Request $request
     * @param UserInterface|null $user
     * @return Response
     */
    public function setRedirect(Request $request, UserInterface $user = null): Response
    {
        $location = new Location();
        $form = $this->makeForm($request, $location);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->locationRepository->save($location);

            $this->setUsersLocation($user, $location);

            return $this->redirectToRoute('product_new');
        }

        return $this->render('location/setRedirect.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="location_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Location $location
     * @param UserInterface|null $user
     * @return Response
     */
    public function edit(Request $request, Location $location, UserInterface $user = null): Response
    {
        $form = $this->makeForm($request, $location);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setUsersLocation($user, $location);

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('location/edit.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $request
     * @param $location
     * @return FormInterface
     */
    private function makeForm(Request $request, Location $location): FormInterface
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        return $form;
    }

    /**
     * @param $user
     * @param $location
     */
    private function setUsersLocation(User $user, Location $location): void
    {
        $user->setLocation($location);
        $this->userRepository->save($user);
    }
}
