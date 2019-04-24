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
        if($this->getUser()) {

            $username = $this->getUser()->getUsername();

            return $this->render('home/index.html.twig', [
                'username' => $username
            ]);
        }

        else {
            return $this->render('home/index.html.twig');
        }

    }
}
