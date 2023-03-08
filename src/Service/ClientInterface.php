<?php

namespace App\Service;

use App\Entity\Booker;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ClientInterface
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private RolesInterface $roles)
    {
    }

    public function CreateClient(User $user, int $cutlerys = 1, string $name = 'default', array $allergys = []): bool
    {
        if (!$this->roles->is_client($user) || $this->ClientExist($user))
            return false;
        $booker = new Booker($name, $allergys);
        $this->entityManager->persist($booker);
        $this->entityManager->flush($booker);
        $client = new Client($booker, $user, $cutlerys);
        $this->entityManager->persist($client);
        $this->entityManager->flush();
        $user->setClient($client);
        $this->entityManager->flush();
        return true;
    }

    public function ClientExist(User $user): bool
    {
        return (bool)$user->getClient();
    }

}