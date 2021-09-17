<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\User\UserType;

class UserTypeCreator {
    public static function create(array $data): UserType
    {
        $userType = new UserType();
        $userType->setId($data[UserType::ID]);
        $userType->setName($data[UserType::NAME]);
        $userType->setAtCreated(new \DateTime($data[UserType::AT_CREATED]));
        $userType->setAtDeleted($data[UserType::AT_DELETED] ? new \DateTime($data[UserType::AT_DELETED]) : null);

        return $userType;
    }
}
