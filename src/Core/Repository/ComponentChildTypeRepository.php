<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Model\Product\ComponentChildType;
use ProductSystem\Core\Repository\Repository;

class ComponentChildTypeRepository extends Repository {

    function findById(int $id): ComponentChildType|bool
    {
        $sql = 'SELECT '
            .ComponentChildType::ID.', '
            .ComponentChildType::NAME.', '
            .ComponentChildType::AT_CREATED.', '
            .ComponentChildType::AT_DELETED.' '
                .'FROM '.ComponentChildType::TABLE_NAME.' '
                .'WHERE '.ComponentChildType::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();
        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        $componentChildType = new ComponentChildType();
        $componentChildType->setId($result[ComponentChildType::ID]);
        $componentChildType->setName($result[ComponentChildType::NAME]);
        $componentChildType->setAtCreated(new \DateTime($result[ComponentChildType::AT_CREATED]));
        $componentChildType->setAtDeleted($result[ComponentChildType::AT_DELETED] ? new \DateTime($result[ComponentChildType::AT_DELETED]) : null);

        return $componentChildType;
    }
}
