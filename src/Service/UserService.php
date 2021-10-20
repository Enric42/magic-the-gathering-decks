<?php

namespace App\Service;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class UserService {

    public function __construct(private EntityManagerInterface $manager, private HasherService $hasherservice) {
    }

    public function addUser(User $user, $plainpassword): bool {
        
        $user->setPassword($this->hasherservice->hash($user, $plainpassword));
        $this->manager->persist($user);
        $this->manager->flush();
        return true;
    }
}