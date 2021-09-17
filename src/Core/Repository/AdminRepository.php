<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Model\Model;
use ProductSystem\Core\Model\User\Admin;
use ProductSystem\Core\Service\AdminCreator;

class AdminRepository extends Repository {

    function findById(int $id): Admin|bool
    {
        $sql = 'SELECT *'
                .'FROM '.Admin::TABLE_NAME.' '
                .'WHERE '.Admin::ID.' = :id 
                AND '.Admin::TYPE.' = '.Admin::USER_TYPE_ID
            .' AND '.Admin::AT_DELETED.' IS NULL';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();
        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        return (new AdminCreator())->createOne($result);
    }

    function findByEmail($email): Admin|bool
    {
        $sql = 'SELECT *'
            .' FROM '.Admin::TABLE_NAME
            .' WHERE '
            .Admin::EMAIL.' = :email'
            .' AND '.Admin::TYPE.' = '.Admin::USER_TYPE_ID
            .' AND '.Admin::AT_DELETED.' IS NULL';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':email', $email);
        $sth->execute();

        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        return (new AdminCreator())->createOne($result);
    }

    public function save(Admin $admin): Admin
    {
        if (Model::hasId($admin)) {
            return $admin;
        }

        return $this->insertAdmin($admin);
    }

    private function insertAdmin(Admin $admin): Admin
    {
        $sql = 'INSERT INTO '.Admin::TABLE_NAME.'('
            .Admin::TYPE.', '
            .Admin::NAME.', '
            .Admin::SURNAME.', '
            .Admin::PATRONYMIC.', '
            .Admin::EMAIL.', '
            .Admin::PASSWORD_HASH
            .') VALUES (:typeId, :name, :surname, :patronymic, :email, :passwordHash)';
        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':typeId', $admin->getUserType()->getId());
        $sth->bindValue(':name', $admin->getName());
        $sth->bindValue(':surname', $admin->getSurname());
        $sth->bindValue(':patronymic', $admin->getPatronymic());
        $sth->bindValue(':email', $admin->getEmail());
        $sth->bindValue(':passwordHash', $admin->getPasswordHash());
        $sth->execute();

        $id = $this->pdo->lastInsertId();
        $admin->setId($id);

        return $admin;
    }
}
