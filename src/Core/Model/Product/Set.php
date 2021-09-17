<?php

namespace ProductSystem\Core\Model\Product;

class Set extends Component {
    public const TABLE_NAME = 'Set';
    public const TYPE_ID = 2;

    private array $children = [];

    public function addComponent(Component $component): void
    {
        if (!$this->existsComponent($component)) {
            $this->children[] = $component;
        }
    }

    public function removeComponent(Component $component): void
    {
        foreach ($this->children as $key => $child) {
            if ($child == $component) {
                unset($this->children[$key]);
            }
        }
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getPrice(): float
    {
        $price = 0;
        foreach ($this->children as $component) {
            $price += $component->getPrice();
        }

        return $price;
    }

    public function getInnerDescription(): string
    {
        $out = '';
        $children = $this->getChildren();
        foreach ($children as $child) {
            $out .= $child->getName().',';
        }
        return trim($out, ',');
    }

    private function existsComponent(Component $component): bool
    {
        foreach ($this->children as $child) {
            if ($child == $component) {
                return true;
            }
        }

        return false;
    }

    public function isSet(): bool
    {
        return true;
    }
}
