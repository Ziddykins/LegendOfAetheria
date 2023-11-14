<?php
    class Familiar {
        protected $characterID;
        protected $name;
        protected $rarity;
        protected $rarityColor;
        protected $level;
        protected $hatchTime;
        protected $dateAcquired;
        protected $hatched;
        protected $id;
        protected $eggsOwned;
        protected $eggsSeen;
        protected $avatar;
        protected $lastRoll;
        
        protected $health, $maxHealth;
        protected $mana, $maxMana;
        protected $energy, $maxEnergy;

        protected $intelligence, $strength, $defense;
        protected $experience, $nextLevel;

        public function __construct($characterID) {
            $this->characterID = $characterID;
        }

        public function registerFamiliar() {
            global $db, $log;

            $sql_time   = get_mysql_datetime();
            $hatch_time = get_mysql_datetime('+8 hours');

            $sql_query = 'INSERT INTO ' . $_ENV['SQL_FMLR_TBL'] . 
                " (`character_id`) VALUES ($this->characterID)";
            
            $db->query($sql_query);

            $sql_query = 'SELECT `id` FROM ' . $_ENV['SQL_FMLR_TBL'] . 
                " WHERE `character_id` = $this->characterID";
            
            $result = $db->query($sql_query);

            $familiar_id  = $result->fetch_assoc();

            $this->dateAcquired = '1970-01-01 00:00:00';
            $this->hatchTime    = '1970-01-01 00:00:00';
            $this->rarityColor  = '#000';
            $this->hatched      = 'False';
            $this->rarity       = 'NONE';
            $this->name         = '!Unset!';
            $this->id           = $familiar_id['id'];
            $this->eggsOwned    = 1;
            $this->eggsSeen     = 1;
            $this->avatar       = 'img/generated/eggs/egg-unhatched.jpeg';
            $this->level        = 1;
            $this->lastRoll     = 0.00;
        
            $this->saveFamiliar(); 
        }

        public function saveFamiliar() {
            global $log, $db;

            $sql_query = 'UPDATE ' . $_ENV['SQL_FMLR_TBL'] . ' SET ';

            foreach((Array)$this as $key => $val) {
                if ($key !== 'id') {
                    $column = clsprop_to_tblcol(
                        preg_replace("/[^a-zA-Z_]+/", '', $key)
                    );
                    
                    $sql_query .= "$column = ";
                    
                    if (is_numeric($val)) {
                        $sql_query .= $val;
                    } else if (!isset($val)) {
                        $sql_query .= 'null';
                    } else {
                        $sql_query .= "'$val'";
                    }
                    
                    $sql_query .= ', ';
                }
            }
            
            $sql_query = rtrim($sql_query, ', ');
            $sql_query .= " WHERE `id` = $this->id";
            
            $log->critical("Saved familiar",
                [
                    'FamID' => $this->id,
                    'CharID' => $this->characterID,
                    'Query'  => $sql_query
                ]
            );

            $db->query($sql_query);
        }

        public function getCard($which = 'current') {
            if ($which === 'empty') {
                $html = file_get_contents(
                    ROOT_WEB_DIRECTORY . 'html/card-egg-none.html'
                );

                return $html;
            } else if ($which === 'current') {
                $build_timer = '<script>init_egg_timer("' . $this->hatchTime . 
                    '", "egg-timer");</script>';
                
                $html = "$build_timer\n";
                
                $html .= file_get_contents(
                    ROOT_WEB_DIRECTORY . 'html/card-egg-current.html'
                );
                
                $html .= "\n";

                return $html;
            }
        }

        public function loadFamiliar($characterID) {
            global $db, $log;

            $sql_query = 'SELECT * FROM ' . $_ENV['SQL_FMLR_TBL'] . ' ' .
                         "WHERE `character_id` = $characterID";
            $result = $db->query($sql_query);

            if ($result->num_rows == 0) {
                $log->warning('Attempted to load familiar but no ' .
                              'corresponding character ID found: ' . $characterID);
                $this->registerFamiliar();
                return;
            }

            $familiar = $result->fetch_assoc();

            foreach ((Array)$this as $key => $val) {
                $key = preg_replace("/[^a-zA-Z_]/", '', $key);
                $table_column = clsprop_to_tblcol($key);
                $this->$key = $familiar[$table_column];
            }
        }

        function __call($method, $params) {
            global $log, $db;
            $caller = debug_backtrace()[1]['function'];

            $var = lcfirst(substr($method, 4));

            if (strncasecmp($method, "get_", 4) === 0) {
                $log->info(
                    "'get_' triggered for var '$var'; " . 
                    "returning '$this->$var'"
                );
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

                $log->info("'set_' triggered for var '\$this->$var';" .
                           "assigning '" . $params[0] . "' to it",
                                [ 
                                    'SQLQuery' => $sql_query,
                                    'CallingFunc' => $caller,
                                    'PropToCol' => $table_col
                                ]
                );
            }
        }
    }
?>
