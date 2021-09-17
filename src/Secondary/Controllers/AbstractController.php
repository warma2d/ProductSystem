<?php

namespace ProductSystem\Secondary\Controllers;

use ProductSystem\Core\Service\SessionStorage;

class AbstractController {

    protected SessionStorage $sessionStorage;

    public function __construct()
    {
        $this->sessionStorage = new SessionStorage();
    }
}
