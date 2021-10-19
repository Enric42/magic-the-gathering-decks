<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Type;
use App\Entity\Color;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class CardRepository extends ServiceEntityRepository
{

    public $cardList = [];

    public function __construct(ManagerRegistry $registry, HttpClientInterface $client)
    {
        parent::__construct($registry, Card::class);
        $this->client = $client;
    }

    // /**
    //  * @return Card[] Returns an array of Card objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Card
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function fetchMTGDApi()
    {
        
            $response = $this->client->request(
                'GET',
                'https://api.magicthegathering.io/v1/cards'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $content = $response->toArray();
                foreach ($content["cards"] as $value) {
                    $card = new Card();
                    $card->setName($value['name']);

                    $card->setManaCost($value['manaCost']);
                    if (isset($value['text'])) {
                        $card->setText($value['text']);
                    }
                    if (isset($value['multiverseid'])) {
                        $card->setMultiverseId($value['multiverseid']);
                    }
                    foreach ($value['colors'] as $color) {
                        $newColor = new Color();
                        $newColor->setName(strtolower($color));
                        $card->addColor($newColor);
                    };
                    foreach ($value['types'] as $type) {
                        $newType = new Type();
                        $newType->setName(strtolower($type));
                        $card->addType($newType);
                    };
                    array_push($this->cardList, $card);
                }

                return $this->cardList;
            }
    }
}
