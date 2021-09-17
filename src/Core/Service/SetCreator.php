<?php

namespace ProductSystem\Core\Service;

use ProductSystem\Core\Model\Product\Set;

class SetCreator {

    public function __construct()
    {
    }

    public function createSome(array $data): array
    {
        $sets = [];
        foreach ($data as $setData) {
            $sets[] = $this->createOne($setData);
        }

        return $sets;
    }

    public function createOne(array $data): Set
    {
        $set = new Set();

        if (isset($data[Set::ID])) {
            $set->setId($data[Set::ID]);
        }

        $set->setName($data[Set::NAME]);

        if (isset($data['components'])) {
            foreach ($data['components'] as $component) {
                $set->addComponent($component);
            }
        }

        if (!isset($data[Set::AT_CREATED])) {
            $atCreated = new \DateTime();
        } else {
            $atCreated = $data[Set::AT_CREATED] instanceof \DateTime ? $data[Set::AT_CREATED] : new \DateTime($data[Set::AT_CREATED]);
        }
        $set->setAtCreated($atCreated);

        return $set;
    }
}
