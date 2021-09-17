<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Model\User\Admin;
use ProductSystem\Core\Model\User\UserType;
use ProductSystem\Core\Repository\Repository;
use ProductSystem\Core\Service\UserTypeCreator;

class UserTypeRepository extends Repository {

    function findById(int $id): UserType|bool
    {
        $sql = 'SELECT '.UserType::ID.', 
                        '.UserType::NAME.', 
                        '.UserType::AT_CREATED.', 
                        '.UserType::AT_DELETED.' '
                .'FROM '.UserType::TABLE_NAME.' '
                .'WHERE '.UserType::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();
        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        return UserTypeCreator::create($result);
    }

    function save(Product $product): int
    {
        $sql = 'INSERT INTO '.Product::TABLE_NAME.'('.Product::NAME.', '.Product::PRICE.') VALUES (:name, :price)';
        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':name', $product->getName());
        $sth->bindValue(':price', $product->getPrice());
        $sth->execute();

        return $this->pdo->lastInsertId();
    }

    function delete(Product $product): bool
    {
        $sql = 'UPDATE '.Product::TABLE_NAME
            .' SET '.Product::AT_DELETED.' = NOW() '
            .'WHERE '.Product::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $product->getId());

        return $sth->execute();
    }
}
