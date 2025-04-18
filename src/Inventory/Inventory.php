<?php
namespace Game\Inventory;

use Game\Inventory\Items\Item;
use Game\Traits\PropManager\Enums\PropType;
use Game\Traits\PropManager\PropManager;


class Inventory {
    use PropManager;
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
        if ($method == 'propSync' || $method == 'propMod') {
            return;
        }

        switch ($method) {
            case 'propSync':
                return $this->propSync($method, $params, PropType::INVENTORY);
            case 'propMod':
                return $this->propMod($method, $params);
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