<?php

namespace ProductSystem\Core\Model\User;

use Jasny\Auth\ContextInterface;

class Manager extends User {
    public const USER_TYPE_ID = 2;

    public function getAuthId(): string
    {
        return $this->getId();
    }

    public function verifyPassword(string $password): bool
    {
        return md5($password) === $this->getPasswordHash();
    }

    public function getAuthChecksum(): string
    {
        return md5($this->getEmail().$this->getPasswordHash());
    }

    public function getAuthRole(ContextInterface|null $context = null): int
    {
        return 1;
    }

    public function requiresMfa(): bool
    {
        return false;
    }
}
