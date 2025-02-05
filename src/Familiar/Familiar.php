<?php
namespace Game\Familiar;

use Game\Inventory\Enums\ObjectRarity;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;

class Familiar {
    use PropConvert;
    use Propsync;
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

        $sqlQuery = "INSERT INTO " . $this->table . "(`character_id`) VALUES ($this->characterID)";
        
        $db->query($sqlQuery);

        $sqlQuery = 'SELECT `id` FROM ' . $this->table . 
            " WHERE `character_id` = $this->characterID";
        
        $result = $db->query($sqlQuery);

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

        $sqlQuery = 'UPDATE ' . $this->table .' SET ';

        foreach ((Array)$this as $key => $val) {
            if ($key !== 'id' && $key !== 'table') {
                $column = $this->clsprop_to_tblcol(
                    preg_replace("/[^a-zA-Z_]+/", '', $key)
                );
                
                $sqlQuery .= "$column = ";
                
                if (is_numeric($val)) {
                    $sqlQuery .= $val;
                } else if (!isset($val)) {
                    $sqlQuery .= 'null';
                } else {
                    $sqlQuery .= "'$val'";
                }
                
                $sqlQuery .= ', ';
            }
        }
        
        $sqlQuery = rtrim($sqlQuery, ', ');
        $sqlQuery .= " WHERE `id` = ?";
        $db->execute_query($sqlQuery, [ $this->id ]);

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

        $sqlQuery = "SELECT * FROM " . $this->table . " WHERE `character_id` = ?";
        $result = $db->execute_query($sqlQuery, [ $characterID ]);

        if ($result->num_rows === 0) {
            $log->warning('Attempted to load familiar but no ' .
                            'corresponding character ID found: ' . $characterID);
            $this->registerFamiliar();
            return;
        }

        $familiar = $result->fetch_assoc();

        foreach ((Array)$this as $key => $val) {
            if ($key == 'table') {
                break;
            }
            $key = preg_replace("/[^a-zA-Z_]/", '', $key);
            $table_column = $this->clsprop_to_tblcol($key);
            $log->debug("key: $key tblcol: $table_column");
            $this->$key = $familiar[$table_column];
        }
    }

    public function getRarityColor($rarity) {
        $color = null;
        
        switch($rarity->name) {
            case "WORTHLESS":
                $color = "#FACEF0";
                break;
            case "TARNISHED":
                $color = "#779988";
                break;
            case "COMMON":
                $color = "#ADD8D7";
                break;
            case "ENCHANTED":
                $color = "#A6D9F8";
                break;
            case "MAGICAL":
                $color = "#08E71C";
                break;
            case "LEGENDARY":
                $color = "#F8C81C";
                break;
            case "EPIC":
                $color = "#CAB51F";
                break;
            case "MYSTIC":
                $color = "#01CBF6";
                break;
            case "HEROIC":
                $color = "#1C4F2C";
                break;
            case "INFAMOUS":
                $color = "#CB20EE";
                break;
            case "GODLY":
                $color = "#FF2501";
                break;
            default:
                $color = "#AAAAAA";
                break;
        }
        
        return $color;
    }

    public function generateEgg($familiar, $rarity_roll) {
        global $log;
        
        $rarity       = ObjectRarity::getObjectRarity($rarity_roll);
        $rarity_color = $this->getRarityColor($rarity);

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
            $sqlQuery =  'UPDATE ' . $this->table . ' ';
            $table_col = $this->clsprop_to_tblcol($var);

            if (is_int($params[0])) {
                $sqlQuery .= "SET `$table_col` = " . $params[0] . " ";
            } else {
                $sqlQuery .= "SET `$table_col` = '" . $params[0] . "' ";
            }

            $sqlQuery .= 'WHERE `id` = ' . $this->id;

            // file deepcode ignore Sqli:
            $db->query($sqlQuery);
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
//                $sqlQuery =  'UPDATE ' . $this->table . ' ';
//                $this->table_col = clsprop_to_tblcol($var);
//
//                if (is_int($params[0])) {
//                    $sqlQuery .= "SET `$this->table_col` = " . $params[0] . " ";
//                } else {
//                    $sqlQuery .= "SET `$this->table_col` = '" . $params[0] . "' ";
//                }
//
//                $sqlQuery .= 'WHERE `id` = ' . $this->id;
//
//                $db->query($sqlQuery);
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
