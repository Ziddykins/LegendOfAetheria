<?php
    class Character {
        protected $id;
        protected $accountID;
        protected $email;
        protected $name;
        protected $password;

        protected $stats;
        protected $inventory;

        use HandlePropsAndCols;

        public function __construct($accountID, $email) {
            $this->email = $email;
            $this->accountID    = $accountID;

            $this->inventory = new Inventory(MAX_STARTING_INVSLOTS, MAX_STARTING_INVWEIGHT);
            $this->stats     = new Stats();
        }

        private function saveCharacter($accountID, Character $character, $slot = 0) {
            global $db, $log;
    
            $serializedCharacter = serialize($character);
            $serializedInventory = serialize($this->inventory);
            $serializedStats     = serialize($this->stats);

            $serializedCompleteCharacter = "$serializedCharacter###$serializedInventory###$serializedStats";

            $sqlQuery = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `serialized_character` = ? WHERE `id` = ?";

            $db->execute_query($sqlQuery, [$serializedCompleteCharacter, $accountID])->fetch_assoc();
            
            $log->info('Saving character', ['id' => $accountID, 'character' => $character->name]);
            $log->debug('Serialized data', ['serialized_character' => $serializedCompleteCharacter]);
        }
    
        private function loadCharacter($accountID) {
            global $db, $log;

            $sqlQuery = "SELECT `serialized_character` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";

            $serializedCompleteCharacter = $db->execute_query($sqlQuery, [$accountID])->fetch_assoc();
            [$serializedCharacter, $serializedInventory, $serializedStats] = explode('###', $serializedCompleteCharacter);

            $character = unserialize($serializedCharacter);
            $character->inventory = unserialize($serializedInventory);
            $character->stats     = unserialize($serializedStats);

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
        protected $slots;
        protected $slotCount;
        
        protected $currentWeight;
        protected $maxWeight;

        protected $nextAvailableSlot;

        public function __construct($slotCount, $maxWeight) {
            for ($i=0; $i<$slotCount; $i++) {
                $this->slots[$i] = new Slot();
            }
            
            $this->currentWeight = 0;
            $this->maxWeight = $maxWeight;

            $this->nextAvailableSlot = 0;
        }
        
        public function spacesLeft() {
            return (count($this->slots) - $this->nextAvailableSlot);
        }
    }

    class Slot {
        protected $itemName;
        protected $itemWeight;
        protected $itemSockets;
    }
?>
