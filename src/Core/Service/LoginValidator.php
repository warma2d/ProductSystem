<?php

namespace ProductSystem\Core\Service;

class LoginValidator {
    public static function validate(array $data)
    {
        $errors = '';

        if (!isset($data['email'])) {
            $errors .= 'Email не введен<br>';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors .= 'Email невалидный<br>';
        }

        if (!isset($data['password'])) {
            $errors .= 'Пароль не введен<br>';
        } elseif (($data['password'] == '')) {
            $errors .= 'Пароль не введен<br>';
        }

        return $errors;
    }
}
