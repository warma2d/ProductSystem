<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Model\Product\Product;
use ProductSystem\Core\Model\Product\ProductSetRef;
use ProductSystem\Core\Repository\Repository;

class ProductSetRefRepository extends Repository {

    function save(ProductSetRef $productSetRef): bool
    {
        $sql = 'INSERT INTO '.ProductSetRef::TABLE_NAME
            .'('.ProductSetRef::SET_ID.', '.ProductSetRef::CHILD_ID.', '.ProductSetRef::CHILD_TYPE.') 
            VALUES (:setId, :childId, :childType)';
        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':setId', $productSetRef->getSetId());
        $sth->bindValue(':childId', $productSetRef->getChildId());
        $sth->bindValue(':childType', $productSetRef->getChildType()->getId());

        return $sth->execute();
    }
}
