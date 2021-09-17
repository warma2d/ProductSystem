<?php

namespace ProductSystem\Core\Repository;

use ProductSystem\Core\Exceptions\ApplicationException;
use ProductSystem\Core\Model\Model;
use ProductSystem\Core\Model\Product\Product;
use ProductSystem\Core\Model\Product\Set;
use ProductSystem\Core\Model\Product\ProductSetRef;
use ProductSystem\Core\Service\ProductCreator;
use ProductSystem\Core\Service\SetCreator;

class ProductRepository extends Repository {

    public function getAllInterfaceProducts(): array
    {
//        $untouchedProducts = (new ProductCreator())->createSome($this->selectUntouchedProducts());
        $simpleProducts = (new ProductCreator())->createSome($this->selectSimpleProducts());
        $sets = (new SetCreator())->createSome($this->selectSets());

        /**@var Set $set*/
        foreach ($sets as &$set) {
            $childProducts = $this->getChildInterfaceProducts($set);

            foreach ($childProducts as &$childProduct) {
                $set->addComponent($childProduct);
            }
        }

        return array_merge($simpleProducts, $sets);
    }

    public function getInterfaceProductsByInputData(array $data): array
    {
        $products = [];

        foreach ($data as $idWithType) {
            preg_match('%(\d*|)%', $idWithType, $m);
            $productId = $m[1];
            preg_match('%.*\|(\d*)%', $idWithType, $m);
            $isSet = $m[1];

            if (!$isSet) {
                $products[] = $this->findById($productId);
            } else {
                $products[] = (new SetRepository())->findById($productId);
            }
        }

        return $products;
    }

    public function findById(int $id): Product|bool
    {
        $result = $this->selectProduct($id);

        if (!$result) {
            return false;
        }

        return $this->generateProductFromDbResult($result);
    }

    public function save(Product $product): Product
    {
        if (Model::hasId($product)) {
            return $product;
        }

        return $this->insertProduct($product);
    }

    /**
     * @throws ApplicationException
     */
    public function delete(Product $product): bool
    {
        return $this->deleteById($product->getId());
    }

    /**
     * @throws ApplicationException
     */
    public function deleteById(int $id): bool
    {
        if ($this->isPartOfSet($id)) {
            throw new ApplicationException('Product (id = '.$id.') is part of set.');
        }

        return $this->setDeletedRow($id);
    }

    public function isPartOfSet(int $id): bool
    {
        $sql = 'SELECT 1 FROM '.ProductSetRef::TABLE_NAME
            .' WHERE '.ProductSetRef::CHILD_ID.' = :id AND '.ProductSetRef::CHILD_TYPE.' = '.Product::TYPE_ID;

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();

        return !!$sth->fetch();
    }

    private function setDeletedRow(int $id): bool
    {
        $sql = 'UPDATE '.Product::TABLE_NAME
            .' SET '.Product::AT_DELETED.' = NOW() '
            .'WHERE '.Product::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);

        return $sth->execute();
    }

    private function insertProduct(Product $product): Product
    {
        $sql = 'INSERT INTO '.Product::TABLE_NAME.'('.Product::NAME.', '.Product::PRICE.') VALUES (:name, :price)';
        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':name', $product->getName());
        $sth->bindValue(':price', $product->getPrice());
        $sth->execute();

        $product->setId($this->pdo->lastInsertId());

        return $product;
    }

    private function generateProductFromDbResult(array $result): Product
    {
        $product = new Product();
        $product->setId($result[Product::ID]);
        $product->setName($result[Product::NAME]);
        $product->setPrice($result[Product::PRICE]);
        $product->setAtCreated(new \DateTime($result[Product::AT_CREATED]));
        $product->setAtDeleted($result[Product::AT_DELETED] ? new \DateTime($result[Product::AT_DELETED]) : null);

        return $product;
    }

    private function selectProduct(int $id): array
    {
        $sql = 'SELECT '.Product::ID.', '.Product::NAME.', '.Product::PRICE.', '.Product::AT_CREATED.', '.Product::AT_DELETED.' '
            .'FROM '.Product::TABLE_NAME.' '
            .'WHERE '.Product::ID.' = :id';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':id', $id);
        $sth->execute();

        return $sth->fetch();
    }

    private function selectSimpleProducts(): array
    {
        $sql = 'SELECT p.*
                FROM `Product` p
                WHERE atDeleted IS NULL;';

        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }

    private function selectUntouchedProducts(): array
    {
        $sql = 'SELECT p.*
                FROM `Product` p
                LEFT JOIN ProductSetRef sr ON p.id = sr.childId AND sr.childType = '.Product::TYPE_ID.' 
                WHERE sr.childType IS NULL AND atDeleted IS NULL;';

        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }

    private function selectSets(): array
    {
        $sql = 'SELECT s.*
                FROM `Set` s
                WHERE atDeleted IS NULL;';

        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }

    private function getChildInterfaceProducts(Set $set): array
    {
        $childProducts = (new ProductCreator())->createSome($this->selectChildProducts($set->getId()));
        $childSets = (new SetCreator())->createSome($this->selectChildSets($set->getId()));

        foreach ($childSets as &$childSet) {
            $products = $this->getChildInterfaceProducts($childSet);
            foreach ($products as $product) {
                $childSet->addComponent($product);
            }
        }

        return array_merge($childProducts, $childSets);
    }

    private function selectChildProducts(int $id): array
    {
        $sql = 'SELECT p.*
                FROM `Product` p
                JOIN ProductSetRef sr ON p.id = sr.childId AND sr.childType = '.Product::TYPE_ID.'
                WHERE sr.setId = :setId';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':setId', $id);
        $sth->execute();

        return $sth->fetchAll();
    }

    private function selectChildSets(int $id): array
    {
        $sql = 'SELECT s.*
                FROM `Set` s
                JOIN ProductSetRef sr ON s.id = sr.childId AND sr.childType = '.Set::TYPE_ID.'
                WHERE sr.setId = :setId';

        $sth = $this->pdo->prepare($sql);
        $sth->bindValue(':setId', $id);
        $sth->execute();

        return $sth->fetchAll();
    }
}
