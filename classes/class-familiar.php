<?php
class Familiar {
    protected $id;
    protected $characterID;
    protected $level;
/*       
    protected $health;
    protected $maxHealth;

    protected $mana;
    protected $maxMana;

    protected $energy;
    protected $maxEnergy;

    protected $intelligence;
    protected $strength;
    protected $defense;

    protected $experience;
    protected $nextLevel;
*/
    protected $eggsOwned;
    protected $eggsSeen;
    
    protected $name;
    
    protected $rarity;
    protected $rarityColor;
    protected $avatar;
    protected $lastRoll;

    /* Classes 
    protected $stats;
    protected $egg;
    */ 
    protected $hatchTime;
    protected $dateAcquired;
    protected $hatched;

    protected $table;

    public function __construct($characterID, $table) {
        $this->characterID = $characterID;
        $this->table = $table;
    }

    public function registerFamiliar() {
        global $db;

        //$sql_time   = get_mysql_datetime();
        //$hatch_time = get_mysql_datetime('+8 hours');

        $sql_query = "INSERT INTO " . $this->table . "(`character_id`) VALUES ($this->characterID)";
        
        $db->query($sql_query);

        $sql_query = 'SELECT `id` FROM ' . $this->table . 
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
        global $db;

        $sql_query = 'UPDATE '.$this->table.' SET ';

        foreach ((Array)$this as $key => $val) {
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
        $sql_query .= " WHERE `id` = ?";
        $db->execute_query($sql_query, [ $this->id ]);

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

        $sql_query = "SELECT * FROM " . $this->table . " WHERE `character_id` = ?";
        $result = $db->execute_query($sql_query, [ $characterID ]);

        if ($result->num_rows === 0) {
            $log->warning('Attempted to load familiar but no ' .
                            'corresponding character ID found: ' . $characterID);
            $this->registerFamiliar();
            return;
        }

        $familiar = $result->fetch_assoc();

        foreach ((Array)$this as $key => $val) {
            if ($key == 'table') {
                continue;
            }
            $key = preg_replace("/[^a-zA-Z_]/", '', $key);
            $table_column = clsprop_to_tblcol($key);
            $this->$key = $familiar[$table_column];
        }
    }

    public function get_rarity_color($rarity) {
        switch($rarity->name) {
            case "WORTHLESS":
                return "#FACEF0";
                break;
            case "TARNISHED":
                return "#779988";
                break;
            case "COMMON":
                return "#ADD8D7";
                break;
            case "ENCHANTED":
                return "#08E71C";
                break;
            case "MAGICAL":
                return "#A6D9F8";
                break;
            case "LEGENDARY":
                return "#F8C81C";
                break;
            case "EPIC":
                return "#CAB51F";
                break;
            case "MYSTIC":
                return "#01CBF6";
                break;
            case "HEROIC":
                return "#1C4F2C";
                break;
            case "INFAMOUS":
                return "#CB20EE";
                break;
            case "GODLY":
                return "#FF2501";
                break;
            default:
                return "#FFF000";
                break;
        }
    }

    public function generate_egg($familiar, $rarity_roll) {
        global $log;
        
        $rarity       = ObjectRarity::getObjectRarity($rarity_roll);
        $rarity_color = $this->get_rarity_color($rarity);

        $familiar->set_level(1);
        
        $familiar->set_rarityColor($rarity_color);
        $familiar->set_rarity($rarity->name);
        $familiar->set_lastRoll($rarity_roll);
        
        $familiar->set_dateAcquired(get_mysql_datetime());
        $familiar->set_hatchTime(get_mysql_datetime('+8 hours'));

        $familiar->set_eggsOwned(0);
        $familiar->set_eggsSeen(0);
        
        $familiar->saveFamiliar();
    }

    function __call($method, $params) {
        global $log, $db;
        $caller = debug_backtrace()[1]['function'];

        $var = lcfirst(substr($method, 4));

        if (strncasecmp($method, "get_", 4) === 0) {
            return $this->$var;
        }

        if (strncasecmp($method, "set_", 4) === 0) {
            $sql_query =  'UPDATE ' . $this->table . ' ';
            $this->table_col = clsprop_to_tblcol($var);

            if (is_int($params[0])) {
                $sql_query .= "SET `$this->table_col` = " . $params[0] . " ";
            } else {
                $sql_query .= "SET `$this->table_col` = '" . $params[0] . "' ";
            }

            $sql_query .= 'WHERE `id` = ' . $this->id;

            $db->query($sql_query);
            $this->$var = $params[0];
        }
    }
}

///*
//    class FamiliarStats
////    {
//        private $level;
//        
//        private $health
//        private $maxHealth;
//    
//        private $mana
//     	private $maxMana;
//    
//        private $energy
// 	    private $maxEnergy;
//
//        private $intelligence
//     	private $strength
// 	    private $defense;
//    
//        private $experience
//        private $nextLevel;
//               
//        private $eggsOwned;
//        private $eggsSeen;
//        
//        function __call($method, $params) {
//            global $log, $db;
//            $caller = debug_backtrace()[1]['function'];
//
//            $var = lcfirst(substr($method, 4));
//
//            if (strncasecmp($method, "get_", 4) === 0) {
//                return $this->$var;
//            }
//
//            if (strncasecmp($method, "set_", 4) === 0) {
//                $sql_query =  'UPDATE ' . $this->table . ' ';
//                $this->table_col = clsprop_to_tblcol($var);
//
//                if (is_int($params[0])) {
//                    $sql_query .= "SET `$this->table_col` = " . $params[0] . " ";
//                } else {
//                    $sql_query .= "SET `$this->table_col` = '" . $params[0] . "' ";
//                }
//
//                $sql_query .= 'WHERE `id` = ' . $this->id;
//
//                $db->query($sql_query);
//                $this->$var = $params[0];
//            }
//        }
//    }
//
//    class FamiliarEgg
//    {
//        private $hatchTime;
//        private $dateAcquired;
//        private $hatched;
//    }
?>
