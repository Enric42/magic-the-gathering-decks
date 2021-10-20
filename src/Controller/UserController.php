<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


#[Route('/user')]

class UserController extends AbstractController
{
    #[Route('/signin', name: 'user_show', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        return $this->getUser()
        ? $this->redirectToRoute('mtgd')
        : $this->render('user/signin.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(), 
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/signup', name: 'user_new', methods: ['GET', 'POST'])]
    public function register(Request $request, UserService $userservice): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('mtgd');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userservice->addUser($user, $form->get('plainPassword')->getData());

            return $this->redirectToRoute('mtgd');
        }

        return $this->render('user/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'user_logout', methods: ['GET'])]
    public function logout(): void
    {
    }
}
