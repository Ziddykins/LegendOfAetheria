<?php
namespace Game\Familiar;

use Game\Inventory\Enums\ObjectRarity;
use Game\Traits\PropSuite\PropSuite;


/**
 * Represents a character's companion familiar creature with stats, leveling, and egg mechanics.
 * Familiars are acquired as eggs, hatch after a time period, and provide combat assistance.
 * Uses PropSuite for database synchronization and custom __call() for dynamic get_/set_ methods.
 * 
 * @method int get_id() Gets familiar ID
 * @method int get_characterID() Gets owning character ID
 * @method int get_level() Gets familiar level
 * @method int get_experience() Gets current experience points
 * @method int get_nextLevel() Gets experience needed for next level
 * @method string get_name() Gets familiar's name
 * @method string get_avatar() Gets familiar avatar image path
 * @method Stats get_stats() Gets familiar combat stats
 * 
 * @method void set_id(int $id) Sets familiar ID
 * @method void set_characterID(int $characterID) Sets owning character ID
 * @method void set_level(int $level) Sets familiar level
 * @method void set_experience(int $experience) Sets experience points
 * @method void set_nextLevel(int $nextLevel) Sets experience threshold
 * @method void set_name(string $name) Sets familiar's name
 * @method void set_avatar(string $avatar) Sets avatar image path
 * @method void set_stats(Stats $stats) Sets combat stats
 * @method void set_rarityColor(string $color) Sets egg rarity color
 * @method void set_rarity(ObjectRarity $rarity) Sets egg rarity
 * @method void set_lastRoll(int $roll) Sets last rarity dice roll
 * @method void set_dateAcquired(string $datetime) Sets acquisition timestamp
 * @method void set_hatchTime(string $datetime) Sets when egg will hatch
 * @method void set_eggsOwned(int $count) Sets total eggs owned
 * @method void set_eggsSeen(int $count) Sets total eggs encountered
 */
class Familiar {
    use PropSuite;

    /** @var int|null Unique familiar identifier */
    private ?int $id = null;
    
    /** @var int|null ID of character who owns this familiar */
    private ?int $characterID = null;
    
    /** @var int Current level of the familiar */
    private int $level = 1;
    
    /** @var int Current experience points earned */
    private int $experience = 0;
    
    /** @var int Experience points needed to reach next level */
    private int $nextLevel = 100;
    
    /** @var string|null Display name of the familiar */
    private ?string $name = null;
    
    /** @var mixed Avatar image file path */
    private $avatar = null;
   
    /** @var Stats|null Combat statistics for the familiar */
    private ?Stats $stats = null;

    /**
     * Creates a new familiar instance.
     * 
     * @param int $characterID Character who owns this familiar
     * @param string $table Database table name (legacy parameter)
     */
    public function __construct($characterID, $table) {
        $this->characterID = $characterID;
    }

    /**
     * Registers a new familiar in the database with default values.
     * Creates database entry, retrieves generated ID, sets initial properties, and saves.
     * 
     * @return void
     */
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

    /**
     * Persists familiar state to database.
     * Updates all properties using snake_case column names converted from camelCase properties.
     * 
     * @return void
     */
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

    /**
     * Generates HTML card for familiar display.
     * Returns 'empty' card for no familiar, or 'current' card showing familiar/egg status.
     * 
     * @param string $which Card type: 'empty' or 'current'
     * @return string|null HTML markup for familiar card
     */
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

    /**
     * Loads familiar data from database by character ID.
     * If no familiar exists, automatically registers a new one.
     * Converts database columns (snake_case) to object properties (camelCase).
     * 
     * @param int $characterID Character whose familiar to load
     * @return void
     */
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

    /**
     * Maps ObjectRarity enum to hex color code for visual representation.
     * Used to colorize familiar eggs based on their rarity level.
     * 
     * @param ObjectRarity $rarity Rarity level enum
     * @return string Hex color code (e.g., "#FF2501" for GODLY)
     */
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

    /**
     * Generates a new familiar egg with random rarity and hatch timer.
     * Sets level to 1, calculates rarity from dice roll, assigns color, sets hatch time (+8 hours).
     * 
     * @param Familiar $familiar Familiar object to configure as egg
     * @param int $rarity_roll Dice roll result to determine rarity
     * @return void
     */
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
