<?php

namespace App\Service;


use App\Entity\Admin;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class InteractionInterface
{

    public function __construct(private  CheckerInterface $checker,
                                private EntityManagerInterface $entityManager,
                                private RolesInterface $roles)
    {
    }

    public function getAdmin(array $post): Admin|false
    {
        if (!$this->checker->checkData($post, 'array', ['id', 'password']) ||
            !($user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $post['id']])) ||
            $user->getPassword() !== $post['password'] ||
            !($admin = $user->getAdmin($this->roles, $this->entityManager)))
            return false;
        else
            return $admin;
    }


    public function getInfo(array $post, string $info_key, array $expected_keys): array | false
    {
        if (!$this->checker->checkArrayData($post, $info_key, 'string'))
            return false;
        return $this->checkJson($post[$info_key], $expected_keys);
    }

    public function checkJson(string $json, array $expected_keys): array | false
    {
        $info = json_decode($json, true);
        if (!$this->checker->checkData($info, 'array', $expected_keys))
            return false;
        else
            return $info;
    }
}