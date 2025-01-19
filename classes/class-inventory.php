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
        private ?string $name;
        private int $weight;
        private int $socketCount;
        /* Array of Class ItemSockets */
        private array $sockets;

        public function __construct($name = null, $weight = 0, $socketCount = 1) {
            $this->name   = $name;
            $this->weight = $weight;
            
            for ($i=0; $i<$socketCount; $i++) {
                $this->sockets[$i] = new ItemSocket();
            }
        }
    }

    class ItemSocket {
        private Gem $gem;
        private ItemModifiers $modifiers;

        public function _construct($modifiers = null) {
            $this->modifiers = $modifiers;
        }
    }

    class ItemModifiers {
        private array $modifiers;

        public function _construct($modifiers) {
            $properties = get_class_vars("Character");

            foreach ($properties as $property) {
                if ($modifiers[$property]) {
                    
                }
            }
        }
    }

    class Gem {
        private int $itemID;
        private $color;
        private $quality;
        private ObjectRarity $rarity;
        private ?string $name;

        public function __construct($itemID) {
            $this->rarity = ObjectRarity::getObjectRarity(random_int(0,100));
        }
    }