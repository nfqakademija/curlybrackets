<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): ?Response
    {
        if ($this->getUser()) {
            $username = $this->getUser()->getUsername();

            return $this->render('home/index.html.twig', [
                'username' => $username
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}
