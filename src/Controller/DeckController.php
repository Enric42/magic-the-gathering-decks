<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Form\DeckType;
use App\Repository\DeckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/deck')]

class DeckController extends AbstractController
{
    #[Route('/', name: 'deck_index', methods:['GET'])]
    public function index(DeckRepository $deckrepository): Response
    {

        return $this->render('deck/index.html.twig', [
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

    #[Route('/new', name: 'deck_new', methods:['GET','POST'])]
    public function new(Request $request , EntityManagerInterface $manager): Response
    {   
        $deck = new Deck();
        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $deck->setUser($this->getUser());
            $manager->persist($deck);
            $manager->flush();
            return $this->redirectToRoute('deck_index');
        }
        return $this->render('deck/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{name<^\w+( +\w+)*$>}', name: 'deck_show', methods:['GET'])]
    //#[Route('/{name}', name: 'deck_show', methods:['GET'], requirements:['name=\w{1,256}'] )]
    public function show(string $name , DeckRepository $deckrepository): Response
    {

        return $this->render('deck/show.html.twig', [
            'decks' => $deckrepository->findByName($name)
        ]);
    }

    #[Route('/{name<^\w+( +\w+)*$>}/delete', name: 'deck_delete', methods:['GET'])]
    //#[Route('/{name}/delete', name: 'deck_delete', methods:['GET'], requirements:['name=\w{1,256}'] )]
    public function delete(string $name, DeckRepository $deckrepository, EntityManagerInterface $manager): Response
    {  

        $deck = $deckrepository->findByName($name);

            foreach ($deck as $decks) { 
                $manager->remove($decks);
            }

            $manager->flush();

        return $this->redirectToRoute('deck_index');

    }
}
