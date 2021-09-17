<?php

namespace ProductSystem\Core\Model\Product;

use ProductSystem\Core\Model\Model;

class ComponentChildType extends Model {
    public const TABLE_NAME = 'ComponentChildType';
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
