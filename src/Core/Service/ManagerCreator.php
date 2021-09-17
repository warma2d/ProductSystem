<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\User\Manager;
use ProductSystem\Core\Model\User\UserType;
use ProductSystem\Core\Repository\UserTypeRepository;

class ManagerCreator {

    private UserType $managerUserType;

    public function __construct()
    {
        $userTypeRepo = new UserTypeRepository();
        $this->managerUserType = $userTypeRepo->findById(Manager::USER_TYPE_ID);
    }

    public function createSome(array $data): array
    {
        $managers = [];
        foreach ($data as $managerData) {
            $managers[] = $this->createOne($managerData);
        }

        return $managers;
    }

    public function createOne(array $data): Manager
    {
        $manager = new Manager();
        if (isset($data[Manager::ID])) {
            $manager->setId($data[Manager::ID]);
        }
        $manager->setName($data[Manager::NAME]);
        $manager->setSurname($data[Manager::SURNAME]);
        $manager->setPatronymic($data[Manager::PATRONYMIC]);
        $manager->setEmail($data[Manager::EMAIL]);
        $passwordHash = isset($data['password']) ? md5($data['password']) : $data[Manager::PASSWORD_HASH];

        if (isset($data['password'])) {
            $manager->setPassword($data['password']);
        }

        $manager->setPasswordHash($passwordHash);

        if (!isset($data[Manager::AT_CREATED])) {
            $atCreated = new \DateTime();
        } else {
            $atCreated = $data[Manager::AT_CREATED] instanceof \DateTime ? $data[Manager::AT_CREATED] : new \DateTime($data[Manager::AT_CREATED]);
        }

        $manager->setAtCreated($atCreated);
        $manager->setUserType($this->managerUserType);

        return $manager;
    }
}
