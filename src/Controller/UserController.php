<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]

class UserController extends AbstractController
{
    #[Route('/', name: 'user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/signup', name: 'user_new', methods:['GET','POST'])]
    public function new(): Response
    {
        return $this->render('user/signup.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/signin', name: 'user_show', methods:['GET','POST'])]
    public function show(): Response
    {
        return $this->render('user/signin.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
