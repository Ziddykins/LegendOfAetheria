<?php
    class Character {
        /* See: functions.php */
        use HandlePropsAndCols;
        use HandlePropSync;

		protected $id;
		protected $accountID;
		protected $name;
		protected $race;
		protected $avatar;
		protected $x;
		protected $y;
		protected $location;
		protected $alignment;
		protected $gold;
		protected $exp;
		protected $ep;
		protected $maxEp;
		protected $floor;
		protected $description;
		protected $ap;
        protected $slot;

        /* class Monster */
		public $monster;

        /* class Stats */
        public $stats;

        /* class Inventory */
        public $inventory;

        public function __construct($accountID, $new = 0, $slot = 0) {
            global $db;
            $this->accountID    = $accountID;
            $this->inventory = new Inventory($this->id, MAX_STARTING_INVSLOTS, MAX_STARTING_INVWEIGHT);
            $this->stats     = new Stats();

            $sql_query = "SELECT MAX(`id`) AS `next_id` FROM {$_ENV['SQL_CHAR_TBL']}";
            $our_id = $db->execute_query($sql_query)->fetch_assoc()['next_id'] + 1;

            if ($new) {
                $this->slot = 1;
                $this->id = $our_id;
                $this->newCharacter($our_id);
                $this->saveCharacter($this);
            } else {
                $this->loadCharacter($accountID, $slot);
            }
        }

        private function setPersonalMonster(Monster $monster) {
            $this->monster = $monster;
        }

        private function newCharacter($id) {
            global $db, $log;
            $query = "SELECT * FROM {$_ENV['SQL_CHAR_TBL']} WHERE `id` = ?";
            $result = $db->execute_query($query, [$id])->fetch_assoc();

            $sql_query = "SELECT char_slot1, char_slot2, char_slot3 FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
            $slots = $db->execute_query($sql_query, [$this->accountID])->fetch_assoc();

            foreach ($result as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);
                $this->$key = $value;
                $log->info("new char key $key vwl $value");
            }

            return 0;
        }

        private function saveCharacter(Character $character) {
            global $db, $log;

            $charSlot = "char_slot" . $character->slot;

            $serializedCharacter = serialize($character);

            $sqlQuery = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET $charSlot = ? WHERE `id` = ?";

            $db->execute_query($sqlQuery,  [$serializedCharacter, $character->id]);
            $log->info('Saving character', ['id' => $character->id, 'character' => $character->name, 'slot' => $charSlot]);
            $log->debug('Serialized data', ['serialized_character' => $serializedCharacter]);
        }


        private function loadCharacter($accountID, $slot) {
            global $db, $log;

            $char_slot = "char_slot" . $slot;

            $sql_query = "SELECT $char_slot FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
            $result = $db->execute_query($sql_query, [$accountID])->fetch_assoc();

            if (!$result) {
                $this->name('Empty Slot');
                $this->avatar('avatar-unknown.jpg');
                $this->slot = -1;
                $log->error("Empty slot found, passing back empty char", [
                    'Requested slot' => $slot,
                    'Account ID'     => $accountID
                ]);
            } else {
                $tmp_char = unserialize($result[$char_slot]);

                
                $log->error("Character found at requested slot", [
                    'Requested slot' => $slot,
                    'Account ID'     => $accountID,
                    'Character ID'   => $this->id,
                    'Char slot data' => $result[$char_slot]
                ]);

                foreach ($tmp_char as $key => $value) {
                    $key = $this->tblcol_to_clsprop($key);
                    $this->$key = $value;
                    $log->error("Loading char key $key vwl $value"); 
                }
            }
        }


        function __call($method, $params) {
            $this->prop_sync($method, $params, PropsyncType::CHARACTER);
        }
    }

	class Stats {
        use HandlePropSync;

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

        function __call($method, $params) {
            $this->prop_sync($method, $params, PropsyncType::CHARACTER);
        }

    }

    class Inventory {
        public $slots;
        public $slotCount;

        protected $currentWeight;
        protected $maxWeight;

        protected $nextAvailableSlot;

        public function __construct($characterID, $slotCount, $maxWeight) {
            $this->characterID   = $characterID;
            $this->slotCount     = $slotCount;
            $this->maxWeight     = $maxWeight;
            $this->currentWeight = 0;

            $this->nextAvailableSlot = 0;

            for ($i=0; $i<$slotCount; $i++) {
                $this->slots[$i] = new Slot();
            }

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

        public function removeItem($slot) {
            $this->slots[$slot] = new Slot();
            $this->nextAvailableSlot--;
        }

        protected function saveInventory() {

        }


    }

    class Slot {
        public $itemName;
        public  $itemWeight;
        public $itemSockets;
    }
?>
