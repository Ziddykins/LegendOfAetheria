<?php
namespace Game\Inventory\Items;

/**
 * Represents an inventory item with weight, gem sockets, and stat modifiers.
 * Items can have multiple sockets for gems and provide various stat modifications.
 */
class Item {
    /** @var string Display name of the item */
    private string $name = "None";
    
    /** @var int Weight value (contributes to encumbrance) */
    private int $weight = 0;
    
    /** @var array<Socket> Array of Socket objects for gem insertion */
    private array $sockets = [];
    
    /** @var array Stat modifiers provided by this item */
    private array $modifiers = [];

    /**
     * Creates a new item with specified properties.
     * Initializes empty Socket objects based on socket count.
     * 
     * @param string $name Item display name
     * @param int $weight Item weight
     * @param int $socketCount Number of gem sockets (default 1)
     */
    public function __construct($name = "None", $weight = 0, $socketCount = 1) {
        $this->name   = $name;
        $this->weight = $weight;
        
        for ($i=0; $i<$socketCount; $i++) {
            $this->sockets[$i] = new Socket($i);
        }
    }
}
