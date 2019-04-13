<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/new", name="userCreate", methods={"POST"})
     */
    public function new(EntityManagerInterface $em, Request $request)
    {
        $user = new User();
        $user->setUsername($request['username'])
            ->setEmail($request['email'])
            ->setPassword($request['password'])
            ->setCreatedAt(new \DateTime("now"))
            ->setUpdatedAt(new \DateTime("now"));

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('home');
    }
}