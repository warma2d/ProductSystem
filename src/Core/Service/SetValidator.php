<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\Product\Set;

class SetValidator {
    public static function validate(Set $product)
    {
        $errors = '';

        if (!$product->getName()) {
            $errors .= 'Название не введено<br>';
        }

        return $errors;
    }
}
