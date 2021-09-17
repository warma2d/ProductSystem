<?php

namespace ProductSystem\Core\Model\Product;

use ProductSystem\Core\Model\Model;

abstract class Component extends Model{
    public const NAME = 'name';
    public const PRICE = 'price';

    private string $name;
    protected float $price;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
