<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DeckRepository;

#[Route('/card')]

class CardController extends AbstractController
{
    #[Route('/', name: 'card', methods:['GET'])]
    public function index(DeckRepository $deckrepository): Response
    {
        return $this->render('card/index.html.twig', [
            'decks' => $deckrepository->findByUser(
                [
                    "name" => $this->getUser()
                ],
                [
                    'id' => 'DESC'
                ]
            )
        ]);
    }
}
