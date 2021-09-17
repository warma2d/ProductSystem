<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Model\Model;
use ProductSystem\Core\Model\User\Manager;
use ProductSystem\Core\Service\ManagerCreator;

class ManagerRepository extends Repository {

    public function findAllActive(): array
    {
        $result = $this->selectAllManagers();

        if (!$result) {
            return [];
        }

        $managerCreator = new ManagerCreator();
        return $managerCreator->createSome($result);
    }

    function findById(int $id): Manager|bool
    {
        $sql = 'SELECT *'
            .' FROM '.Manager::TABLE_NAME
            .' WHERE '
            .Manager::ID.' = :id '
            .' AND '.Manager::TYPE.' = '.Manager::USER_TYPE_ID
            .' AND '.Manager::AT_DELETED.' IS NULL';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();

        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        return (new ManagerCreator())->createOne($result);
    }

    function findByEmail(string $email): Manager|bool
    {
        $sql = 'SELECT *'
            .' FROM '.Manager::TABLE_NAME
            .' WHERE '
            .Manager::EMAIL.' = :email '
            .' AND '.Manager::TYPE.' = '.Manager::USER_TYPE_ID
            .' AND '.Manager::AT_DELETED.' IS NULL';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':email', $email);
        $sth->execute();

        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        return (new ManagerCreator())->createOne($result);
    }

    public function save(Manager $manager): Manager
    {
        if (Model::hasId($manager)) {
            return $manager;
        }

        return $this->insertManager($manager);
    }

    public function delete(Manager $Manager): bool
    {
        return $this->deleteById($Manager->getId());
    }

    public function deleteById(int $id): bool
    {
        $sql = 'UPDATE '.Manager::TABLE_NAME
            .' SET '.Manager::AT_DELETED.' = NOW() '
            .'WHERE '.Manager::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);

        return $sth->execute();
    }

    private function selectAllManagers(): array|bool
    {
        $sql = 'SELECT '
                .Manager::ID.','
                .Manager::NAME.','
                .Manager::SURNAME.','
                .Manager::PATRONYMIC.','
                .Manager::EMAIL.','
                .Manager::PASSWORD_HASH.','
                .Manager::AT_CREATED.' '
        .' FROM '.Manager::TABLE_NAME
        .' WHERE '.Manager::TYPE.' = '.Manager::USER_TYPE_ID.' AND '.Manager::AT_DELETED.' IS NULL';

        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }

    private function insertManager(Manager $manager): Manager
    {
        $sql = 'INSERT INTO '.Manager::TABLE_NAME.'('
            .Manager::TYPE.', '
            .Manager::NAME.', '
            .Manager::SURNAME.', '
            .Manager::PATRONYMIC.', '
            .Manager::EMAIL.', '
            .Manager::PASSWORD_HASH
    .') VALUES (:typeId, :name, :surname, :patronymic, :email, :passwordHash)';
        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':typeId', $manager->getUserType()->getId());
        $sth->bindValue(':name', $manager->getName());
        $sth->bindValue(':surname', $manager->getSurname());
        $sth->bindValue(':patronymic', $manager->getPatronymic());
        $sth->bindValue(':email', $manager->getEmail());
        $sth->bindValue(':passwordHash', $manager->getPasswordHash());
        $sth->execute();

        $id = $this->pdo->lastInsertId();
        $manager->setId($id);

        return $manager;
    }
}
