<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Model\User\Manager;
use ProductSystem\Core\Model\User\User;
use ProductSystem\Core\Service\ManagerCreator;

class UserRepository extends Repository {

    public function findByEmail($email): User|bool
    {
        $sql = 'SELECT *'
            .' FROM '.User::TABLE_NAME
            .' WHERE '
            .User::EMAIL.' = :email'
            .' AND '.User::PASSWORD_HASH.' = :passwordHash'
            .' AND '.User::AT_DELETED.' IS NULL';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':email', $email);
        $sth->execute();

        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        return (new ManagerCreator())->createOne($result);
    }
}
