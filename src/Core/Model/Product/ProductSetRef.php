<?php

namespace ProductSystem\Core\Model\Product;

class ProductSetRef {
    public const TABLE_NAME = 'ProductSetRef';
    public const SET_ID = 'setId';
    public const CHILD_ID = 'childId';
    public const CHILD_TYPE = 'childType';

    private int $setId;
    private int $childId;
    private ComponentChildType $childType;

    public function getSetId(): int
    {
        return $this->setId;
    }

    public function setSetId(int $setId): void
    {
        $this->setId = $setId;
    }

    public function getChildId(): int
    {
        return $this->childId;
    }

    public function setChildId(int $childId): void
    {
        $this->childId = $childId;
    }

    public function getChildType(): ComponentChildType
    {
        return $this->childType;
    }

    public function setChildType(ComponentChildType $childType): void
    {
        $this->childType = $childType;
    }
}
