<?php
namespace Game\Inventory\Items;

class Modifiers {
    private int $itemID;
    private string $target;
    private array $effects;


    public function __construct($itemID = 0) {
        $this->itemID = $itemID;
    }
}