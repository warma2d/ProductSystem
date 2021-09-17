<?php

namespace ProductSystem\Secondary\Router;

use Bramus\Router\Router;
use Jasny\Auth\Auth;
use ProductSystem\Secondary\Controllers\AdminController;
use ProductSystem\Secondary\Controllers\GuestController;
use ProductSystem\Secondary\Controllers\ManagerController;

class AppRouter {
    public static function run(Auth $auth): void
    {
        $router = new Router();

        if ($auth->is('admin')) {
            new AdminController($router, $auth);
        } elseif ($auth->is('manager')) {
            new ManagerController($router, $auth);
        } else {
            new GuestController($router, $auth);
        }

        $router->set404('/(.*)?', function() {
            echo '<h1>404</h1> <a href="/">На главную</a>';
        });

        $router->run();
    }

    private function __construct() { }
    private function __clone() { }
    public function __wakeup() {}
}


