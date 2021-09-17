<?php

namespace ProductSystem\Core\Model\Product;

class Product extends Component {
    public const TABLE_NAME = 'Product';
    public const TYPE_ID = 1;

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getInnerDescription(): string
    {
        return '';
    }

    public function isSet(): bool
    {
        return false;
    }
}
