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
		private $x = 0;
		private $y = 0;
		private $location = 'The Shrine';
		private $alignment = 0;
		private $gold = 1000;
		private $exp = 0;

		private $floor = 1;
		private $description = 'None Provided';
		private $ap = 0;

        /* class Monster */
		public $monster;

        /* class Stats */
        public $stats;

        /* class Inventory */
        public $inventory;

        public function __construct($accountID) {
            $this->accountID = $accountID;
        }

        public function __call($method, $params) {
            if ($method == 'prop_sync') {
                return;
            }
            
            return $this->prop_sync($method, $params, PropsyncType::CHARACTER);
        }

        private function getProps(): array {
            return get_class_vars(get_class($this));
        }

        private function getConstructor() {
            return 'accountID';
        }

        private function getNextCharSlotID($accountID): int {
            global $db;
            $sqlQuery = "SELECT IF (`char_slot1` IS NULL, 1, IF (`char_slot2` IS NULL, 2, IF (`char_slot3` IS NULL, 3, -1))) AS `free_slot` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
            return $db->execute_query($sqlQuery, [ $accountID ])->fetch_assoc()['free_slot'];
        }
    }

	class Stats {
        /* Trait, functions.php */
        use HandlePropsAndCols;
        use HandlePropSync;

        private int $id;
		private int $hp = 100;
		private int $maxHp = 100;
		private int $mp = 100;
        private int $maxMp = 100;
        private int $ep = 100;
        private int $maxEp = 100;

        private int $str = 10;
        private int $int = 10;
        private int $def = 10;

        /* Enum CharacterStatus, constants.php */
        //private CharacterStatus $status;
        
        public function __construct ($characterID = 0) {
            $this->id = $characterID;
        }
        public function __call($method, $params): mixed {
            global $log;
            $log->error("STATS CLASS CALL", ['method' => $method, 'params' => $params]);
            return $this->prop_sync($method, $params, PropsyncType::STATS);
        }

        private function getProps(): array {
            return get_class_vars(get_class($this));
        }

        private function getConstructor() {
            return 'id';
        }
    }