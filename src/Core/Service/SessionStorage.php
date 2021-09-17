<?php

namespace ProductSystem\Core\Service;

class SessionStorage {

    private const PRODUCT_SYSTEM_MESSAGE = 'ProductSystem_Message';
    private const PRODUCT_SYSTEM_ERRORS = 'ProductSystem_Errors';
    private const PRODUCT_SYSTEM_POST_DATA = 'ProductSystem_Post';

    public function saveMessage(string $message): void
    {
        $_SESSION[self::PRODUCT_SYSTEM_MESSAGE] = $message;
    }

    public function getMessage(): string|null
    {
        if (isset($_SESSION[self::PRODUCT_SYSTEM_MESSAGE])) {
            $tmp = $_SESSION[self::PRODUCT_SYSTEM_MESSAGE];
            $_SESSION[self::PRODUCT_SYSTEM_MESSAGE] = null;
            return $tmp;
        }

        return null;
    }

    public function saveErrors(string $errors): void
    {
        $_SESSION[self::PRODUCT_SYSTEM_ERRORS] = $errors;
    }

    public function getErrors(): string|null
    {
        if (isset($_SESSION[self::PRODUCT_SYSTEM_ERRORS])) {
            $tmp = $_SESSION[self::PRODUCT_SYSTEM_ERRORS];
            $_SESSION[self::PRODUCT_SYSTEM_ERRORS] = null;
            return $tmp;
        }

        return null;
    }

    public function savePostData(array $post): void
    {
        $_SESSION[self::PRODUCT_SYSTEM_POST_DATA] = $post;
    }

    public function getPostData(): array
    {
        if (!isset($_SESSION[self::PRODUCT_SYSTEM_POST_DATA])) {
            return [];
        }
        return $_SESSION[self::PRODUCT_SYSTEM_POST_DATA];
    }
}
