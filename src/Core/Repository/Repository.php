<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Database\PDO;

abstract class Repository {

    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = PDO::getInstance();
    }
}
