<?php
    class Character {
        /* See: functions.php */
        use HandlePropsAndCols;
        use HandlePropSync;

		private $id;
		private $accountID;
		private $name;
		private $race;
		private $avatar;
		private $x;
		private $y;
		private $location;
		private $alignment;
		private $gold;
		private $exp;

		private $floor;
		private $description;
		private $ap;

        /* class Monster */
		public $monster;

        /* class Stats */
        public $stats;

        /* class Inventory */
        public $inventory;

        public function __construct($accountID, $new, $slot = 0) {
            global $db, $log;

            $this->accountID = $accountID;
            $this->inventory = new Inventory( MAX_STARTING_INVSLOTS, MAX_STARTING_INVWEIGHT);

            if ($new) {
                $this->newCharacter($accountID, $this->getNextCharSlot($accountID));
            } else {
                $this->loadCharacter($accountID, $slot);
            }
        }

        public function __call($method, $params) {
            if ($method == 'prop_sync') {
                return;
            }
            
            return $this->prop_sync($method, $params, PropsyncType::CHARACTER);
        }

        private function newCharacter($accountID, $slot) {
            global $db, $log;
            $this->id    = $this->getNextID();
            $this->stats = new Stats($this->id);
            
            $sql_query = "INSERT INTO {$_ENV['SQL_CHAR_TBL']} (`id`, `account_id`) VALUES (?, ?)";
            $db->execute_query($sql_query, [$this->id, $accountID]);

            $query = "SELECT * FROM {$_ENV['SQL_CHAR_TBL']} WHERE `id` = ?";
            $character = $db->execute_query($query, [$this->id])->fetch_assoc();

            if ($character) {
                foreach ($character as $key => $value) {
                    $stats_props = $this->stats->getProps();
                    $key = $this->tblcol_to_clsprop($key);

                    if (array_search($key, $stats_props)) {
                        $this->stats->$key = $value;
                    } else {
                        $this->$key = $value;
                    }
                }
            } else {
                $log->error("Didn't get anything from db for newChar pull: $this->id");
            }

            $this->inventory->addItem("Floopy Dildo", 100, 0);

            return 0;
        }

        private function loadCharacter($accountID, $slot) {
            global $db, $log;

            $char_slot = "char_slot$slot";
            $sql_query = "SELECT `$char_slot` AS `char_id` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
            $char      = $db->execute_query($sql_query, [$accountID])->fetch_assoc();

            $sql_query = "SELECT * FROM {$_ENV['SQL_CHAR_TBL']} WHERE `id` = ?";
            $tmp_char  = $db->execute_query($sql_query, [$char['char_id']]);
                
            foreach ($tmp_char as $key => $value) {
                $stats_props = get_class_vars("Stats");

                /* Messy way to avoid trying to set characterID in the SQL table */
                array_shift($stats_props);

                $key = $this->tblcol_to_clsprop($key);

                if (array_search($key, $stats_props)) {
                    $this->stats->$key = $value;
                } else {
                    $this->$key = $value;
                }
                
                $log->error("Loading char key $key vwl $value"); 
            }

            $this->inventory = unserialize($this->inventory);
        }

        private function setPersonalMonster(Monster $monster) {
            $this->monster = $monster;
        }

        private function getNextID(): int {
            global $db;
            $sql_query = "SELECT IF(MAX(`id`) IS NULL, 1, MAX(`id`)+1) AS `next_id` FROM {$_ENV['SQL_CHAR_TBL']}";
            $next_id = $db->execute_query($sql_query)->fetch_assoc()['next_id'];
            
            return $next_id;
        }

        private function getNextCharSlot($accountID) {
            global $db;

            $sql_query = <<<SQL
                SELECT 
                    IF (`char_slot1` IS NULL, 1,
                        IF (`char_slot2` IS NULL, 2,
                            IF (`char_slot3` IS NULL, 3, -1)
                        )
                    ) AS `free_slot`
                FROM {$_ENV['SQL_ACCT_TBL']}
                WHERE `id` = ?
            SQL;

            return $db->execute_query($sql_query, [ $accountID ])->fetch_assoc()['free_slot'];
        }
    }

	class Stats {
        /* Trait, functions.php */
        use HandlePropsAndCols;
        use HandlePropSync;

        private int $characterID;
		private int $hp;
		private int $maxHp;
		private int $mp;
        private int $maxMp;
        private int $ep;
        private int $maxEp;

        private int $str;
        private int $int;
        private int $def;

        /* Enum CharacterStatus, constants.php */
        private CharacterStatus $status;
        
        public function __construct ($characterID) {
            $this->characterID = $characterID;
        }
        public function __call($method, $params): mixed {
            global $log;
            $log->error("STATS CLASS CALL", ['method' => $method, 'params' => $params]);
            return $this->prop_sync($method, $params, PropsyncType::CHARACTER);
        }

        public function getProps(): array {
            return get_class_vars(get_class($this));
        }
    }