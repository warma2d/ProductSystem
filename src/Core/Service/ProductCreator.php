<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\Product\Product;

class ProductCreator {

    public function __construct()
    {
    }

    public function createSome(array $data): array
    {
        $products = [];
        foreach ($data as $productData) {
            $products[] = $this->createOne($productData);
        }

        return $products;
    }

    public function createOne(array $data): Product
    {
        $product = new Product();

        if (isset($data[Product::ID])) {
            $product->setId($data[Product::ID]);
        }

        $product->setName($data[Product::NAME]);
        $product->setPrice((float)$data[Product::PRICE]);

        if (!isset($data[Product::AT_CREATED])) {
            $atCreated = new \DateTime();
        } else {
            $atCreated = $data[Product::AT_CREATED] instanceof \DateTime ? $data[Product::AT_CREATED] : new \DateTime($data[Product::AT_CREATED]);
        }
        $product->setAtCreated($atCreated);

        return $product;
    }
}
