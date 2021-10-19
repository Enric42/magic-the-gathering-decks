<?php

namespace App\Controller;

use App\Entity\Card;
use App\Repository\CardRepository;
use App\Repository\DeckRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/card')]
#[IsGranted('ROLE_USER')]

class CardController extends AbstractController
{
    #[Route('/', name: 'card', methods:['GET'])]
    public function index(DeckRepository $deckrepository , CardRepository $cardrepository): Response
    {   

        $cache = new FilesystemAdapter();
        $APICard = $cache->getItem('API_cache');
        //$cache->deleteItem('API_cache');

            if (!$APICard->isHit()) {
                $APICard = $cardrepository->fetchMTGDApi();
                $send = $cache->getItem('API_cache');
                $send->set($APICard);
                $cache->save($send);

            } else {
                $OkCache = $APICard->get();
            }

        return $this->render('card/index.html.twig', [

            'cards' => $OkCache,

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
