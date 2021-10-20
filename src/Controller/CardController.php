<?php

namespace App\Controller;

use App\Entity\Card;
use App\Repository\ColorRepository;
use App\Repository\DeckRepository;
use App\Repository\TypeRepository;
use Psr\Cache\CacheItemPoolInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/card')]
#[IsGranted('ROLE_USER')]

class CardController extends AbstractController
{

    #[Route('/{name<.{1,256}>}', name: 'card_active', methods: ['GET'])]
    public function active(
        string $name,
        DeckRepository $deckrepository,
        RequestStack $requeststack): Response
    {
        $deck = $deckrepository->findOneBy([
            'user' => $this->getUser(),
            'name' => $name
        ]);
        if ($deck) {
            $requeststack->getSession()->set('active-deck', $deck->getName());
        }
        return $this->redirectToRoute('card');
    }

    #[Route('/', name: 'card', methods:['GET'])]
    public function index(
        Request $request,
        DeckRepository $deckrepository,
        ColorRepository $colorrepository,
        TypeRepository $typerepository,
        HttpClientInterface $httpclient,
        CacheItemPoolInterface $cacheitempool
    ): Response
    {

        $colors = $colorrepository->findAll();

        $color =current(array_filter(
            $colors, 
            function ($color) use ($request) {
                return $color->getName() === ucfirst(strtolower($request->query->get('color')));
            }));


        $options = [
            'page' => abs((int) $request->query->get('page')),
            'colors' => false !== $color ? strtolower($color->getName()) : null
        ];

        if (0 === $options['page']) {
            $options['page'] = 1;
        }

        $endpoint = 'cards?pageSize=48&' . http_build_query($options);

        try {
            $item = $cacheitempool->getItem($endpoint);
            if (!$item->isHit()) {
                $apicards = $httpclient->request(
                    'GET',
                    'https://api.magicthegathering.io/v1/'.$endpoint
                )->toArray()['cards'];

                $cards = [];

                foreach ($apicards as $apicard) {
                    $card = new Card();
                    $card->setName($apicard['name']);
                    if (array_key_exists('colors', $apicard)) {
                        foreach ($apicard['colors'] as $color) {
                            $card->addColor($colorrepository->findOneByName($color));
                        }
                    }
                    if (array_key_exists('types', $apicard)) {
                        foreach ($apicard['types'] as $type) {
                            $card->addType($typerepository->findOneByName($type));
                        }
                    }
                    if (array_key_exists('manaCost', $apicard)) {
                        $card->setManaCost($apicard['manaCost']);
                    }
                    if (array_key_exists('multiverseid', $apicard)) {
                        $card->setMultiverseId($apicard['multiverseid']);
                    }
                    if (array_key_exists('text', $apicard)) {
                        $card->setText($apicard['text']);
                    }
                    $cards[] = $card;
                }
                $item->set($cards);
                $cacheitempool->save($item);
            } else {
                $cards = $item->get();
            }
        } catch (\Throwable $e) {
            dump($e);
            $cards = [];
        }
        return $this->render('card/index.html.twig', [
            'colors' => $colors,
            'cards' => $cards,
            'options' => $options,
            'decks' => $deckrepository->findByUser($this->getUser(), [
                'id' => 'DESC'
            ]),
        ]);
    }
}
