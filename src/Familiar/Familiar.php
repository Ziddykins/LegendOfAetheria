<?php
namespace Game\Familiar;

use Game\Inventory\Enums\ObjectRarity;
use Game\Traits\PropSuite\PropSuite;

;

class Familiar {
    use PropSuite;

    private ?int $id = null;
    private ?int $characterID = null;
    private int $level = 1;
    private int $experience = 0;
    private int $nextLevel = 100;
    private ?string $name = null;
    private $avatar = null;
   
    private ?Stats $stats = null;

    public function __construct($characterID, $table) {
        $this->characterID = $characterID;
    }

    public function registerFamiliar() {
        global $db, $t;
        $sqlQuery = "INSERT INTO {$t['characters']} (`character_id`) VALUES (?)";
        
        $db->execute_query($sqlQuery, [ $this->characterID ]);

        $sqlQuery = "SELECT `id` FROM {$t['characters']} WHERE `character_id` = ?";
        
        $result      = $db->execute_query($sqlQuery, [ $this->characterID ])->fetch_assoc();
        $familiar_id = $result['id'];

        
        $this->name         = '!Unset!';
        $this->id           = $familiar_id['id'];
        $this->avatar       = 'img/generated/eggs/egg-unhatched.jpeg';
        $this->level        = 1;

        $this->saveFamiliar(); 
    }

    public function saveFamiliar() {
        global $db;

        $sqlQuery ='UPDATE  SET ';

        foreach ((Array)$this as $key => $val) {
            if ($key !== 'id' && $key !== 'table') {
                $column = $this->clsprop_to_tblcol(
                    preg_replace("/[^a-zA-Z_]+/", '', $key)
                );
                
                $sqlQuery .= "$column = ";
                
                if (is_numeric($val)) {
                    $sqlQuery .= $val;
                } elseif (!isset($val)) {
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
                WEBROOT . 'html/card-egg-none.html'
            );

            return $html;
        } elseif ($which === 'current') {
            //$build_timer = '<script>init_egg_timer("' . $this->hatchTime . 
                '", "egg-timer");</script>';
            
           // $html = "$build_timer\n";
            
            //$html .= file_get_contents(
             //   WEBROOTECTORY . 'html/card-egg-current.html'
           // );
            
            //$html .= "\n";

            //return $html;
        }
    }

    public function loadFamiliar($characterID) {
        global $db, $log, $t;

        $sqlQuery = "SELECT * FROM {$t['familiars']} WHERE `character_id` = ?";
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
        $familiar->set_rarity($rarity);
        $familiar->set_lastRoll($rarity_roll);
        
        $familiar->set_dateAcquired(get_mysql_datetime());
        $familiar->set_hatchTime(get_mysql_datetime('+8 hours'));

        $familiar->set_eggsOwned(0);
        $familiar->set_eggsSeen(0);
        
        $familiar->saveFamiliar();
    }

    function __call($method, $params) {
        global $log, $db, $t;
        $caller = debug_backtrace()[1]['function'];

        $var = lcfirst(substr($method, 4));

        if (strncasecmp($method, "get_", 4) === 0) {
            return $this->$var;
        }

        if (strncasecmp($method, "set_", 4) === 0) {
            $sqlQuery =  "UPDATE {$t['familiars']} ";
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
