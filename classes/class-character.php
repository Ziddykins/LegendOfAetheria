<?php
    class Character {
        protected $accountID;
        protected $accountEmail;
        protected $charName;

        protected $stats;
        protected $inventory;

        public function __construct($accountID, $accountEmail) {
            $this->accountEmail = $accountEmail;
            $this->accountID    = $accountID;

            $this->inventory = new Inventory(MAX_STARTING_INVSLOTS, MAX_STARTING_INVWEIGHT);
            $this->stats     = new Stats();
        }
            
        public function setStat($stat, $value) {
            $this->stats->$stat = $value;
        }   
        
        public function getStat($stat) {
            return $this->stats->$stat;
        }
		
		function __call($method, $params) {
			$var = lcfirst(substr($method, 3));

			if (strncasecmp($method, "get", 3) === 0) {
				return $this->$var;
			}
		
            if (strncasecmp($method, "set", 3) === 0) {
				$this->$var = $params[0];
            }
        }
    }

	class Stats {
		private $hp;
		private $maxHp;
		private $mp;
        private $maxMp;
        
        public $strength;
        private $intelligence;
        private $defense;

        private $status;

        public function __construct() {
        }
    }

    class Inventory {
        protected $slots;
        protected $slotCount;
        
        protected $weight;
        protected $maxWeight;

        protected $nextAvailableSlot;

        public __construct($slotCount, $maxWeight) {
            for ($i=0; $i<$slotCount; $i++) {
                $this->slots[$i] = new Slot();
            }
            
            $this->weight    = 0;
            $this->maxWeight = 1000;
            $this->nextAvailableSlot = 0;
        }
        
        public static function spacesLeft() {
            return (count($this->slots) - $this->nextAvailableSlot);
        }
    }

    class Slot {
        protected $itemName;
        protected $itemWeight;
        protected $itemSockets;

        public function __construct() {
            
        }
    }
?>
