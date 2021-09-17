<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\Product\Product;

class ProductValidator {
    public static function validate(Product $product)
    {
        $errors = '';

        if (!$product->getName()) {
            $errors .= 'Название не введено<br>';
        }

        if ($product->getPrice() == 0) {
            $errors .= 'Цена не задана<br>';
        }

        return $errors;
    }
}
