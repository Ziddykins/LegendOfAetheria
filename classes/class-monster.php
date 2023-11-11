<?php
   class Monster {
        protected $name;
        
        protected $scope;
        protected $seed;
        
        protected $summondBy;
        
        protected $hp, $maxHP;
        protected $mp, $maxMP;
        protected $strength;
        protected $intelligence;
        protected $defense;
        
        protected $dropLevel;
        protected $expAwarded, $goldAwarded
        
        protected $monsterClass;

        
        function __call($method, $params) {
            global $log, $db;
            $caller = debug_backtrace()[1]['function'];

            $var = lcfirst(substr($method, 4));

            if (strncasecmp($method, "get_", 4) === 0) {
                $log->info("'get_' triggered for var '$var'; returning '" . $this->$var . "'");
                return $this->$var;
            }

            if (strncasecmp($method, "set_", 4) === 0) {
                $sql_query =  'UPDATE ' . $_ENV['SQL_FMLR_TBL'] . ' ';
                $table_col = clsprop_to_tblcol($var);

                if (is_int($params[0])) {
                    $sql_query .= "SET `$table_col` = " . $params[0] . " ";
                } else {
                    $sql_query .= "SET `$table_col` = '" . $params[0] . "' ";
                }

                $sql_query .= 'WHERE `id` = ' . $this->id;

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
        public function __construct(MonsterScope $scope, $account_id = null) {
            $this->accountID = $account_id;
            $this->scope     = $scope;
        }

        public function load_monster_sheet() {
            $monsters = Array();
            $monsters = file_get_contents(__DIR__ . "monsters.raw");
        }
    }
?>
