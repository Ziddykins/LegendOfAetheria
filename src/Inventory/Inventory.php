<?php
namespace Game\Inventory;

use Game\Inventory\Items\Item;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Traits\PropSuite\PropSuite;
class Inventory {
    use PropSuite;
    private int $id;
    private int $slotCount;
    private int $currentWeight;
    private int $maxWeight;
    private int $nextAvailableSlot;

    private array $slots;

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

    private function spacesLeft() {
        return count($this->slots) - $this->nextAvailableSlot;
    }

    private function addItem($name, $weight, $numSockets) {
        $targetSlot = $this->nextAvailableSlot++;
        $this->slots[$targetSlot]->name    = $name;
        $this->slots[$targetSlot]->weight  = $weight;
        $this->slots[$targetSlot]->socketCount = $numSockets;
    }

    private function removeItem($slot) {
        $this->slots[$slot] = new Item();
        $this->nextAvailableSlot--;
    }
}