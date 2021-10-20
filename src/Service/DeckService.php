<?php

namespace App\Service;


use App\Entity\Deck;
use App\Repository\DeckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class DeckService{

    public function __construct(private DeckRepository $deckrepository , private EntityManagerInterface $manager)
    {

    }

    public function getDecks(UserInterface $user): Array
    {  

        return $this->deckrepository->findByUser($user,
            [
                'id'=> 'DESC'
            ]
        );

    }

    public function getDeck(UserInterface $user, string $name): ?Deck
    {  
        return $this->deckrepository->findOneBy(
            [
                'user'=> $user,
                'name' => $name
            ]
        );

    }

    public function deleteDeck(UserInterface $user, string $name): bool
    {  

        $deck = $this->getDeck($user, $name);

        try {
            if (!$deck) {
                throw new \LogicException();
            }

            $this->manager->remove($deck);
            $this->manager->flush();
            return true;

        } catch (\Throwable $e) {
            return false;
        }
    }

    public function addDeck(UserInterface $user, Deck $deck): bool
    {  
        $deck->setUser($user);

        try {

           $this->manager->persist($deck);
           $this->manager->flush();
           return true;

        } catch (\Throwable $e) {
            return false;
        }

    }


}