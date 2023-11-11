<?php
    class Character {
        protected $id;
        protected $accountID;
        protected $email;
        protected $name;
        protected $password;

        protected $stats;
        protected $inventory;

        public function __construct($accountID, $email) {
            $this->email = $email;
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
            global $log, $db;
            $caller = debug_backtrace()[1]['function'];

            $var = lcfirst(substr($method, 4));

            if (strncasecmp($method, "get_", 4) === 0) {
                $log->info("'get_' triggered for var '$var'; returning '" . $this->$var . "'");
                return $this->$var;
            }

            if (strncasecmp($method, "set_", 4) === 0) {
                $sql_query =  'UPDATE ' . $_ENV['SQL_CHAR_TBL'] . ' ';
                $table_col = clsprop_to_tblcol($var);

                if (is_int($params[0])) {
                    $sql_query .= "SET `$table_col` = " . $params[0] . " ";
                } else {
                    $sql_query .= "SET `$table_col` = '" . $params[0] . "' ";
                }

                $sql_query .= 'WHERE `id` = ' . $this->accountID;

                $db->query($sql_query);
                $this->$var = $params[0];

                $log->info("'set_' triggered for var '\$this->$var'; assigning '" . $params[0] . "' to it",
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
        
        protected $weight;
        protected $maxWeight;

        protected $nextAvailableSlot;

        public function __construct($slotCount, $maxWeight) {
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
