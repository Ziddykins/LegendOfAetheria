<?php
namespace Game\Inventory\Items;

/**
 * Defines stat modifications provided by an item or socketed gems.
 * Each modifier targets a specific stat and provides an array of effects.
 */
class ItemModifiers {
    /** @var int|null ID of item these modifiers belong to */
    private ?int $itemID = null;
    
    /** @var string|null Target stat name (e.g., "str", "int", "def") */
    private ?string $target = null;
    
    /** @var array List of effect values applied to target stat */
    private array $effects = [];
    
    /**
     * Creates an item modifier instance.
     * 
     * @param int $itemID ID of parent item
     */
    public function __construct($itemID = 0) {
        $this->itemID = $itemID;
    }
}