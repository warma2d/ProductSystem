<?php

namespace ProductSystem\Secondary\Auth;

use Jasny\Auth\Auth;
use Jasny\Auth\Authz;

class AppAuth {
    private static Auth|null $auth = null;

    public static function getInstance(): Auth
    {
        if (self::$auth === null) {

            $levels = new Authz\Levels([
                'manager' => 1,
                'admin' => 10,
            ]);

            self::$auth = new Auth($levels, new AuthStorage());
        }

        return self::$auth;
    }

    private function __construct() { }
    private function __clone() { }
    public function __wakeup() {}
}
