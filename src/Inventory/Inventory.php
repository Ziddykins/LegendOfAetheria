<?php
namespace Game\Inventory;

use Game\Inventory\Items\Item;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Traits\PropSuite\PropSuite;

/**
 * Manages a character's item inventory with slot-based storage and weight limits.
 * Tracks items in fixed slots, enforces max weight capacity, and provides add/remove operations.
 * Uses PropSuite for database synchronization with INVENTORY type.
 * 
 * @method int get_id() Gets inventory ID (character ID)
 * @method int get_slotCount() Gets total inventory slots
 * @method int get_currentWeight() Gets current carried weight
 * @method int get_maxWeight() Gets maximum weight capacity
 * @method int get_nextAvailableSlot() Gets next empty slot index
 * @method array get_slots() Gets array of Item objects
 * 
 * @method void set_id(int $id) Sets inventory ID
 * @method void set_slotCount(int $slotCount) Sets total slots
 * @method void set_currentWeight(int $currentWeight) Sets current weight
 * @method void set_maxWeight(int $maxWeight) Sets weight limit
 * @method void set_nextAvailableSlot(int $nextAvailableSlot) Sets next slot index
 * @method void set_slots(array $slots) Sets item array
 * 
 * @method void add_currentWeight(int $amount) Increases carried weight
 * @method void sub_currentWeight(int $amount) Decreases carried weight
 */
class Inventory {
    use PropSuite;
    /** @var int Inventory identifier (matches character ID) */
    private int $id;
    
    /** @var int Total number of inventory slots available */
    private int $slotCount;
    
    /** @var int Current total weight of all carried items */
    private int $currentWeight;
    
    /** @var int Maximum weight capacity before encumbrance */
    private int $maxWeight;
    
    /** @var int Index of next available empty slot */
    private int $nextAvailableSlot;

    /** @var array<Item> Array of Item objects indexed by slot number */
    private array $slots;

    /**
     * Creates a new inventory instance with default capacity.
     * Initializes all slots with empty Item objects.
     * 
     * @param int $characterID Character who owns this inventory
     * @param int $slotCount Total inventory slots (default 20)
     * @param int $maxWeight Weight limit (default 1000)
     */
    public function __construct($characterID, $slotCount = 20, $maxWeight = 1000) {
        $this->id = $characterID;
        $this->slotCount     = $slotCount;
        $this->maxWeight     = $maxWeight;
        $this->currentWeight = 0;

        $this->nextAvailableSlot = 0;

        for ($i=0; $i<$slotCount; $i++) {
            $this->slots[$i] = new Item();
        }
    }

    /**
     * Magic method routing calls to PropSuite components.
     * Handles get_/set_ (propSync), add_/sub_/mul_/div_ (propMod), propDump/propRestore.
     * Uses PropType::INVENTORY for database table routing.
     * 
     * @param string $method Method name
     * @param array $params Method parameters
     * @return mixed Result from PropSuite method
     */
    public function __call($method, $params) {
        global $db, $log;

        
        if (!count($params)) {
            $params = null;
        }

        $matches = [];
        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::INVENTORY);
        }
    }

    /**
     * Calculates number of empty inventory slots remaining.
     * 
     * @return int Number of available slots
     */
    private function spacesLeft() {
        return count($this->slots) - $this->nextAvailableSlot;
    }

    /**
     * Adds a new item to the next available inventory slot.
     * 
     * @param string $name Item name
     * @param int $weight Item weight
     * @param int $numSockets Number of gem sockets
     * @return void
     */
    private function addItem(Item $item) {
        $targetSlot = $this->nextAvailableSlot++;
        $this->slots[$targetSlot]->item = $item;
    }

    /**
     * Removes an item from specified slot.
     * Replaces slot with empty Item object and decrements available slot pointer.
     * 
     * @param int $slot Slot index to clear
     * @return void
     */
    private function removeItem($slot) {
        $this->slots[$slot] = new Item();
        $this->nextAvailableSlot--;
    }
}