<?php

namespace App\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class HasherService
{

    public function __construct(private UserPasswordHasherInterface $userpasswordhasher)
    {
    }

    public function hash(
        PasswordAuthenticatedUserInterface $user,
        string $plainPassword): string
    {
        return $this->userpasswordhasher->hashPassword(
            $user,
            $plainPassword
        );
    }

}