<?php

namespace ProductSystem\Core\Model\User;

use ProductSystem\Core\Model\Model;

class UserType extends Model {

    public const TABLE_NAME = 'UserType';

    public const NAME = 'name';

    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
