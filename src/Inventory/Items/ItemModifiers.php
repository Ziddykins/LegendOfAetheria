<?php
namespace Game\Inventory\Items;

class ItemModifiers {
    private ?int $itemID = null;
    private ?string $target = null;
    private array $effects = [];
    public function __construct($itemID = 0) {
        $this->itemID = $itemID;
    }
}