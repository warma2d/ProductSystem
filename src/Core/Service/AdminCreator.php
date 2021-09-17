<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\User\Admin;
use ProductSystem\Core\Model\User\UserType;
use ProductSystem\Core\Repository\UserTypeRepository;

class AdminCreator {

    private UserType $adminUserType;

    public function __construct()
    {
        $userTypeRepo = new UserTypeRepository();
        $this->adminUserType = $userTypeRepo->findById(Admin::USER_TYPE_ID);
    }

    public function createOne(array $data): Admin
    {
        $admin = new Admin();
        if (isset($data[Admin::ID])) {
            $admin->setId($data[Admin::ID]);
        }
        $admin->setName($data[Admin::NAME]);
        $admin->setSurname($data[Admin::SURNAME]);
        $admin->setPatronymic($data[Admin::PATRONYMIC]);
        $admin->setEmail($data[Admin::EMAIL]);
        $passwordHash = isset($data['password']) ? md5($data['password']) : $data[Admin::PASSWORD_HASH];

        if (isset($data['password'])) {
            $admin->setPassword($data['password']);
        }

        $admin->setPasswordHash($passwordHash);

        if (!isset($data[Admin::AT_CREATED])) {
            $atCreated = new \DateTime();
        } else {
            $atCreated = $data[Admin::AT_CREATED] instanceof \DateTime ? $data[Admin::AT_CREATED] : new \DateTime($data[Admin::AT_CREATED]);
        }

        $admin->setAtCreated($atCreated);
        $admin->setUserType($this->adminUserType);

        return $admin;
    }
}
