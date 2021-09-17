<?php

use ProductSystem\Secondary\Auth\AppAuth;
use ProductSystem\Secondary\Router\AppRouter;

require_once(__DIR__.'/../vendor/autoload.php');

try {
    session_start();
    $auth = AppAuth::getInstance();
    $auth->initialize();
    AppRouter::run($auth);
} catch (\Exception $exception) {
    echo $exception->getMessage();
    echo 'Произошла ошибка системы, пожалуйста, повторите запрос позднее';
    // TODO здесь следует реализовать запись текста исключений в лог
}

