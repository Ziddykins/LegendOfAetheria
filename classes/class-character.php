<?php
class Character {
		protected $id;
		protected $accountID;
		protected $name;
		protected $race;
		protected $avatar;
		protected $str;
		protected $int;
		protected $def;
		protected $x;
		protected $y;
		protected $location;
		protected $hp;
		protected $maxHp;
		protected $mp;
		protected $maxMp;
		protected $alignment;
		protected $gold;
		protected $exp;
		protected $ep;
		protected $maxEp;
		protected $floor;
		protected $description;
		protected $ap;
		protected $monster;

        protected $stats;
        protected $inventory;
        
        use HandlePropsAndCols;

        public function __construct($accountID) {
            $this->accountID    = $accountID;

            $this->inventory = new Inventory(MAX_STARTING_INVSLOTS, MAX_STARTING_INVWEIGHT);
            $this->stats     = new Stats();
            $this->newCharacter($_SESSION['character-id']);
        }

        private function setPersonalMonster(Monster $monster) {
            $this->monster = $monster;
        }

        private function saveCharacter($accountID, Character $character, $slot = 0) {
            global $db, $log;
    
            $serializedCharacter = serialize($character);
            $serializedInventory = serialize($this->inventory);
            $serializedStats     = serialize($this->stats);
            $serializedMonster   = serialize($this->pmonster);
            
            $serializedCompleteCharacter = "$serializedCharacter###$serializedInventory###$serializedStats###$serializedMonster";

            $sqlQuery = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `serialized_character` = ? WHERE `id` = ?";

            $db->execute_query($sqlQuery, [$serializedCompleteCharacter, $accountID])->fetch_assoc();
            
            $log->info('Saving character', ['id' => $accountID, 'character' => $character->name]);
            $log->debug('Serialized data', ['serialized_character' => $serializedCompleteCharacter]);
        }

        private function newCharacter($id) {
            global $db, $log;
            $query = "SELECT * FROM {$_ENV['SQL_CHAR_TBL']} WHERE `id` = ?";

            $result = $db->execute_query($query, [$id])->fetch_assoc();

            foreach ($result as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);
                $this->$key = $value;
                $log->info("new char key $key vwl $value");
            }

            return 0;
        }

        private function loadCharacter($accountID) {
            global $db, $log;

            $sqlQuery = "SELECT `serialized_character` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";

            $serializedCompleteCharacter = $db->execute_query($sqlQuery, [$accountID])->fetch_assoc();
            [$serializedCharacter, $serializedInventory, $serializedStats, $serializedMonster] = explode('###', $serializedCompleteCharacter);

            $character            = unserialize($serializedCharacter);
            $character->inventory = unserialize($serializedInventory);
            $character->stats     = unserialize($serializedStats);
            $character->pmonster  = unserialize($serializedMonster);
            
            $log->debug(
                "Loaded character {$character['name']} from account ID $accountID",
                [
                     'full'      => $serializedCompleteCharacter,
                     'character' => $serializedCharacter,
                     'inventory' => $serializedInventory,
                     'stats'     => $serializedStats
                ]
            );
            
            return $character;
        }

        function __call($method, $params) {
            global $log, $db;

            $prop = lcfirst(substr($method, 4));
            
            if (strncasecmp($method, "get_", 4) === 0) {
                return $this->$prop;
            } elseif (strncasecmp($method, "set_", 4) === 0) {
                $sql_query = 'UPDATE ' . $_ENV['SQL_CHAR_TBL'] . ' ';
                $table_col = $this->clsprop_to_tblcol($prop);

                if (is_int($params[0])) {
                    $sql_query .= "SET `$table_col` = " . $params[0] . " ";
                } else {
                    $sql_query .= "SET `$table_col` = '" . $params[0] . "' ";
                }

                $sql_query .= 'WHERE `id` = ' . $this->accountID;

                $db->query($sql_query);
                $this->$prop = $params[0];

                $log->info("'set_' triggered for var '\$this->$prop'; assigning '" . $params[0] . "' to it",
                    [ 
                        'SQLQuery' => $sql_query,
                        'CallingFunc' => $caller,
                        'PropToCol' => $table_col
                    ]
                );
            }
        }
    }

	class Stats {
		protected $hp;
		protected $maxHp;
		protected $mp;
        protected $maxMp;
        protected $ep;
        protected $maxEp;
        
        protected $strength;
        protected $intelligence;
        protected $defense;

        protected $status;
    }

    class Inventory {
        public $slots;
        public $slotCount;
        
        protected $currentWeight;
        protected $maxWeight;

        protected $nextAvailableSlot;

        public function __construct($slotCount, $maxWeight) {
            for ($i=0; $i<$slotCount; $i++) {
                $this->slots[$i] = new Slot();
            }

            $this->slotCount = $slotCount;
            $this->currentWeight = 0;
            $this->maxWeight = $maxWeight;

            $this->nextAvailableSlot = 0;
        }
        
        public function spacesLeft() {
            return (count($this->slots) - $this->nextAvailableSlot);
        }

        public function addItem($name, $weight, $numSockets) {
            $targetSlot = $this->nextAvailableSlot++;
            $this->slots[$targetSlot]->itemName       = $name;
            $this->slots[$targetSlot]->itemWeight     = $weight;
            $this->slots[$targetSlot]->itemSockets = $numSockets;
        }
    }

    class Slot {
        public $itemName;
        public  $itemWeight;
        public $itemSockets;
    }
?>
