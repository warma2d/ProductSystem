<?php

namespace ProductSystem\Core\Model;

abstract class Model {

    public const ID = 'id';
    public const AT_CREATED = 'atCreated';
    public const AT_DELETED = 'atDeleted';

    private int $id;
    private \DateTime $atCreated;
    private \DateTime|null $atDeleted = null;

    public function __construct()
    {
        $this->id = -1;
        $this->atCreated = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAtCreated(): \DateTime
    {
        return $this->atCreated;
    }

    public function setAtCreated(\DateTime $atCreated): void
    {
        $this->atCreated = $atCreated;
    }

    public function getAtDeleted(): \DateTime|null
    {
        return $this->atDeleted;
    }

    public function setAtDeleted(\DateTime|null $atDeleted): void
    {
        $this->atDeleted = $atDeleted;
    }

    public static function hasId(Model $model): bool
    {
        return $model->getId() !== -1;
    }
}
