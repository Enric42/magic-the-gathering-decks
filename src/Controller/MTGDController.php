<?php

namespace App\Controller;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MTGDController extends AbstractController
{
    #[Route('/', name: 'mtgd', methods:['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('card');
        }

        return $this->render('mtgd/index.html.twig', [
            'controller_name' => 'MTGDController',
        ]);
    }
}
