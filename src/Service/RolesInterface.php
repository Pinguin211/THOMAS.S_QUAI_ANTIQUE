<?php

namespace App\Service;

use App\Entity\User;

class RolesInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_CLIENT = 'ROLE_CLIENT';


    public function is_client(User $user): bool
    {
        return in_array(self::ROLE_CLIENT, $user->getRoles());
    }

    public function is_admin(User $user): bool
    {
        return in_array(self::ROLE_ADMIN, $user->getRoles());
    }
}