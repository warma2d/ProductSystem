<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Exceptions\ApplicationException;
use ProductSystem\Core\Model\Model;
use ProductSystem\Core\Model\Product\ComponentChildType;
use ProductSystem\Core\Model\Product\Product;
use ProductSystem\Core\Model\Product\ProductSetRef;
use ProductSystem\Core\Model\Product\Set;
use ProductSystem\Core\Service\SetCreator;

class SetRepository extends Repository {

    private ProductRepository $productRepo;
    private ProductSetRefRepository $productSetRefRepo;
    private ComponentChildTypeRepository $componentChildTypeRepository;

    public function __construct()
    {
        parent::__construct();
        $this->productRepo = new ProductRepository();
        $this->productSetRefRepo = new ProductSetRefRepository();
        $this->componentChildTypeRepository = new ComponentChildTypeRepository();
    }

    public function findById(int $id): Set|bool
    {
        $sql = 'SELECT * '
                .' FROM `'.Set::TABLE_NAME.'`'
                .' WHERE '.Set::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();
        $result = $sth->fetch();

        if (!$result) {
            return false;
        }

        return (new SetCreator())->createOne($result);
    }

    public function save(Set $set, Set|null $parentSet = null): Set
    {
        $setId = $this->insert($set);
        $set->setId($setId);

        $productChildType = $this->componentChildTypeRepository->findById(Product::TYPE_ID);
        $setChildType = $this->componentChildTypeRepository->findById(Set::TYPE_ID);

        if ($parentSet) {
            $productSetRef = $this->createProductSetRef($parentSet->getId(), $setId, $setChildType);
            $this->productSetRefRepo->save($productSetRef);
        }

        $children = $set->getChildren();
        foreach ($children as $child) {
            if ($child instanceof Product) {
                $productId = Model::hasId($child) ? $child->getId() : $this->productRepo->save($child)->getId();
                $productSetRef = $this->createProductSetRef($setId, $productId, $productChildType);
                $this->productSetRefRepo->save($productSetRef);
            }
        }

        foreach ($children as $child) {
            if ($child instanceof Set) {
                $this->save($child, $set);
            }
        }

        return $set;
    }

    public function delete(Set $set): bool
    {
        try {
            return $this->deleteById($set->getId());
        } catch (\Exception $exception) {
            // TODO запись в лог
            $this->pdo->rollBack();
        }
    }

    /**
     * @throws ApplicationException
     */
    public function deleteById(int $id): bool
    {
        if ($this->isPartOfSet($id)) {
            throw new ApplicationException('Set (id = '.$id.') is part of set.');
        }

        $this->pdo->beginTransaction();

        $this->deleteRelationsForSet($id);
        $result = $this->setDeletedRow($id);

        $this->pdo->commit();

        return $result;
    }

    public function deleteRelationsForSet(int $id): bool
    {
        $sql = 'DELETE FROM '.ProductSetRef::TABLE_NAME.' WHERE '.ProductSetRef::SET_ID .' = :id';
        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);

        return $sth->execute();
    }

    public function isPartOfSet(int $id): bool
    {
        $sql = 'SELECT 1 FROM '.ProductSetRef::TABLE_NAME
            .' WHERE '.ProductSetRef::CHILD_ID.' = :id AND '.ProductSetRef::CHILD_TYPE.' = '.Set::TYPE_ID;

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();

        return !!$sth->fetch();
    }

    private function insert(Set $set): int
    {
        $sql = 'INSERT INTO `'.Set::TABLE_NAME.'` ('.Set::NAME.') VALUES (:name)';
        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':name', $set->getName());
        $sth->execute();

        return $this->pdo->lastInsertId();
    }

    private function createProductSetRef(int $setId, int $productId, ComponentChildType $productChildType): ProductSetRef
    {
        $productSetRef = new ProductSetRef();
        $productSetRef->setSetId($setId);
        $productSetRef->setChildId($productId);
        $productSetRef->setChildType($productChildType);

        return $productSetRef;
    }

    private function setDeletedRow(int $id): bool
    {
        $sql = 'UPDATE `'.Set::TABLE_NAME
            .'` SET '.Set::AT_DELETED.' = NOW() '
            .'WHERE '.Set::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);

        return $sth->execute();
    }
}
