<?php
    class Inventory {
        /* Trait, functions.php */
        use HandlePropsAndCols;
        use HandlePropSync;
        

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
            if ($method == 'prop_sync') {
                return;
            }
            
            return $this->prop_sync($method, $params, PropSyncType::CHARACTER);
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
    
    class Item {
        private string $name;
        private int $weight;
        /* Array of Class ItemSockets */
        private array $sockets;
        private array $modifiers;

        public function __construct($name = "None", $weight = 0, $socketCount = 1) {
            $this->name   = $name;
            $this->weight = $weight;
            
            for ($i=0; $i<$socketCount; $i++) {
                $this->sockets[$i] = new ItemSocket($i);
            }
        }
    }

    class ItemSocket {
        private int $socketID;
        private int $itemID;
        private Gem $gem;
        private ItemModifiers $modifiers;

        public function __construct($socketID = 0) {
            $this->socketID = $socketID;
        }
    }

    class ItemModifiers {
        private int $itemID;
        private string $target;
        private array $effects;
        

        public function __construct($itemID = 0) {
            $this->itemID = $itemID;
        }
    }

    class Gem {
        private int $itemID;
        private int $socketID;
        private $color;
        private $quality;
        private ObjectRarity $rarity;
        private string $name;
        private GemModifiers $modifiers;

        public function __construct($socketID = 0) {
            $this->rarity = ObjectRarity::getObjectRarity(random_int(0,100));
        }
    }

    class GemModifiers {
        private int $gemID;
        private string $target;
        private int $effect;

        public function __construct($gemID = 0) {
            $this->gemID = $gemID;
        }

    }