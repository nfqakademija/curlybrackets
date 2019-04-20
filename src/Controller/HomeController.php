<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $em)
    {

        $repository = $em->getRepository(User::class);
        $user = $repository->find('119');

        return $this->render('home/index.html.twig', [
            'user' => $user
        ]);
    }
}
