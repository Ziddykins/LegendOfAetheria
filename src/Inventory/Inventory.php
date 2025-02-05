<?php
namespace Game\Inventory;

use Game\Inventory\Items\Item;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;
use Game\Traits\Enums\Type;

class Inventory {
    use PropConvert;
    use Propsync;
    private int $slotCount;
    private int $currentWeight;
    private int $maxWeight;
    private int $nextAvailableSlot;

    private array $slots;

    public function __construct($slotCount = 20, $maxWeight = 1000) {
        $this->slotCount     = $slotCount;
        $this->maxWeight     = $maxWeight;
        $this->currentWeight = 0;

        $this->nextAvailableSlot = 0;

        for ($i=0; $i<$slotCount; $i++) {
            $this->slots[$i] = new Item();
        }
    }

    public function __call($method, $params) {
        if ($method == 'propSync') {
            return;
        }
        
        return $this->propSync($method, $params, Type::CHARACTER);
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