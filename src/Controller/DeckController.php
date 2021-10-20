<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Form\DeckType;
use App\Service\DeckService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/deck')]
#[IsGranted('ROLE_USER')]

class DeckController extends AbstractController
{
    #[Route('/', name: 'deck_index', methods:['GET'])]
    public function index(DeckService $deckservice): Response
    {
        return $this->render('deck/index.html.twig', [
            'decks'=> $deckservice->getDecks($this->getUser())
        ]);
    }

    #[Route('/new', name: 'deck_new', methods:['GET','POST'])]
    public function new(Request $request , DeckService $deckservice): Response
    {   
        $deck = new Deck();

        $form = $this->createForm(DeckType::class, $deck)-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $deckservice->addDeck($this->getUser(), $deck);
            return $this->redirectToRoute('deck_index');
        }

        return $this->render('deck/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{name<.{1,256}>}/delete', name: 'deck_delete', methods:['GET'])]
    public function delete(string $name, DeckService $deckservice , Request $request): Response
    {  
        // protection faille CSRF avec un token
        if ($this->isCsrfTokenValid('deck_delete',$request->query->get('token'))) {
            
            $deckservice->deleteDeck($this->getUser(), $name);
        }

        return $this->redirectToRoute('deck_index');

    }

    #[Route('/{name<.{1,256}>}', name: 'deck_show', methods:['GET'])]
    public function show(string $name , DeckService $deckservice): Response
    {

        $deck = $deckservice->getDeck($this->getUser(), $name);

        return !$deck
            ? $this->redirectToRoute('deck_index')
            : $this->render('deck/show.html.twig', [
                'deck' => $deck
            ]);

    }

}
