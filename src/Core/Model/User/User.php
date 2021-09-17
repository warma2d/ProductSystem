<?php

namespace ProductSystem\Core\Model\User;

use Jasny\Auth\UserInterface;
use ProductSystem\Core\Model\Model;

abstract class User extends Model implements UserInterface {

    public const TABLE_NAME = 'User';

    public const TYPE = 'typeId';
    public const EMAIL = 'email';
    public const NAME = 'name';
    public const SURNAME = 'surname';
    public const PATRONYMIC = 'patronymic';
    public const PASSWORD_HASH = 'passwordHash';

    private string $email;
    private string $name;
    private string $surname;
    private string $patronymic;
    private string $password;
    private string $passwordHash;
    private UserType $userType;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    public function setPatronymic(string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getUserType(): UserType
    {
        return $this->userType;
    }

    public function setUserType(UserType $userType): void
    {
        $this->userType = $userType;
    }
}
