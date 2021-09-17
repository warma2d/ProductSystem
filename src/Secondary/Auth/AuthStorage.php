<?php

namespace ProductSystem\Secondary\Auth;

use Jasny\Auth;
use Jasny\Auth\User\BasicUser;
use ProductSystem\Core\Repository\AdminRepository;
use ProductSystem\Core\Repository\ManagerRepository;
use ProductSystem\Core\Repository\UserRepository;

class AuthStorage implements Auth\StorageInterface
{
    public function fetchUserById(string $id): ?Auth\UserInterface
    {
        $user = (new ManagerRepository())->findById($id);

        if (!$user) {
            $user = (new AdminRepository())->findById($id);
        }

        if ($user) {
            return $user;
        }

        return null;
    }

    public function fetchUserByUsername(string $email): ?Auth\UserInterface
    {
        $user = (new ManagerRepository())->findByEmail($email);

        if (!$user) {
            $user = (new AdminRepository())->findByEmail($email);
        }

        if ($user) {
            return $user;
        }

        return null;
    }

    public function fetchContext(string $id) : ?Auth\ContextInterface
    {
        return null;
    }

    public function getContextForUser(Auth\UserInterface $user) : ?Auth\ContextInterface
    {
        return null;
    }
}
