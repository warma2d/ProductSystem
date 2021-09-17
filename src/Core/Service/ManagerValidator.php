<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\User\Manager;
use ProductSystem\Core\Model\User\UserType;
use ProductSystem\Core\Repository\UserTypeRepository;

class ManagerValidator {
    public static function validate(Manager $manager)
    {
        $errors = '';

        if (!$manager->getName()) {
            $errors .= 'Имя не введено<br>';
        }

        if (!$manager->getSurname()) {
            $errors .= 'Фамилия не введена<br>';
        }

        if (!$manager->getPatronymic()) {
            $errors .= 'Отчество не введено<br>';
        }

        if (!$manager->getEmail()) {
            $errors .= 'Email не введен<br>';
        } elseif (!filter_var($manager->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors .= 'Email невалидный<br>';
        }

        if (!$manager->getPassword()) {
            $errors .= 'Пароль не введен<br>';
        }

        return $errors;
    }
}
