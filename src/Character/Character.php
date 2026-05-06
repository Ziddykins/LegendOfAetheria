<?php
namespace Game\Character;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Traits\PropSuite\PropSuite;
use Game\Monster\Monster;
use Game\Character\Stats;
use Game\Inventory\Inventory;
use Game\Bank\BankManager;
use Game\Character\Enums\Races;
use Game\Character\Enums\Status;
use ReflectionEnumPureCase;

/**
 * Character class represents a player character in the game.
 * 
 * Manages character attributes, location, inventory, stats, and associated systems.
 * Uses PropSuite trait for dynamic property management and database synchronization.
 * 
 * @package Game\Character
 * 
 * @method mixed load_(int $character_id) Loads character data from database
 * @method mixed new_() Creates new character in database
 * 
 * @method int get_id() Gets the character ID
 * @method int get_accountID() Gets the associated account ID
 * @method int get_level() Gets the character level
 * @method int get_x() Gets the X coordinate
 * @method int get_y() Gets the Y coordinate
 * @method int get_alignment() Gets the alignment value
 * @method int get_spindels() Gets spindel currency count
 * @method int get_exp() Gets experience points
 * @method string get_dateCreated() Gets character creation date
 * @method int get_floor() Gets current dungeon floor
 * @method float get_gold() Gets gold amount
 * @method string get_description() Gets character description
 * @method string get_location() Gets current location name
 * @method string get_name() Gets character name
 * @method string get_avatar() Gets avatar filename
 * @method string get_lastAction() Gets last action timestamp
 * @method Status get_status() Gets character status
 * @method Races|null get_race() Gets character race
 * @method Monster|null get_monster() Gets current monster encounter
 * @method Stats|null get_stats() Gets character stats object
 * @method Inventory|null get_inventory() Gets inventory object
 * @method BankManager|null get_bank() Gets bank manager object
 * 
 * @method void set_level(int $level) Sets character level
 * @method void set_x(int $x) Sets X coordinate
 * @method void set_y(int $y) Sets Y coordinate
 * @method void set_alignment(int $alignment) Sets alignment value
 * @method void set_spindels(int $count) Sets spindel count
 * @method void set_exp(int $exp) Sets experience points
 * @method void set_floor(int $floor) Sets dungeon floor
 * @method void set_gold(float $gold) Sets gold amount
 * @method void set_description(string $description) Sets character description
 * @method void set_location(string $location) Sets current location
 * @method void set_name(string $name) Sets character name
 * @method void set_avatar(string $avatar) Sets avatar filename
 * @method void set_lastAction(string $action) Sets last action timestamp
 * @method void set_status(Status $status) Sets character status
 * @method void set_race(Races $race) Sets character race
 * @method void set_monster(Monster|null $monster) Sets current monster
 * @method void set_stats(Stats $stats) Sets stats object
 * @method void set_inventory(Inventory $inventory) Sets inventory object
 * @method void set_bank(BankManager $bank) Sets bank manager object
 * 
 * @method void add_level(int $levels) Adds levels to character
 * @method void add_x(int $amount) Moves character along X axis
 * @method void add_y(int $amount) Moves character along Y axis
 * @method void add_alignment(int $amount) Increases alignment
 * @method void sub_alignment(int $amount) Decreases alignment
 * @method void add_spindels(int $count) Adds spindels
 * @method void sub_spindels(int $count) Removes spindels
 * @method void add_exp(int $exp) Adds experience points
 * @method void add_gold(float $amount) Adds gold
 * @method void sub_gold(float $amount) Removes gold
 * @method void add_floor(int $floors) Advances dungeon floors
 * @method void sub_floor(int $floors) Goes back dungeon floors
 */
class Character {
    use PropSuite;
    
    /** @var int Character unique identifier */
    private int $id;
    
    /** @var int Associated account ID */
    private int $accountID;
    
    /** @var int Character level */
    private int $level = 1;
    
    /** @var int X coordinate on map */
    private int $x = 0;
    
    /** @var int Y coordinate on map */
    private int $y = 0;
    
    /** @var int Alignment value (good/neutral/evil) */
    private int $alignment = 0;
    
    /** @var int Spindel currency count */
    private int $spindels = 0;
    
    /** @var int Experience points */
    private int $exp = 0;
    
    /** @var string Character creation timestamp */
    private string $dateCreated = '1970-01-01 00:00:00';
    
    /** @var int Current dungeon floor */
    private int $floor = 1;

    /** @var float Gold currency amount */
    private float $gold = 1000.0;
    
    /** @var string Character biography/description */
    private string $description = 'None Provided';
    
    /** @var string Current location name */
    private string $location = 'The Shrine';
    
    /** @var string Character name */
    private string $name = 'NoName';
    
    /** @var string Avatar image filename */
    private string $avatar = '';
    
    /** @var string Last action timestamp */
    private string $lastAction = '';
    
    /** @var Status Character health/status condition */
    private Status $status = Status::HEALTHY;

    /** @var Races|null Character race */
    private ?Races $race = null;

    /** @var Monster|null Current monster encounter */
    public ?Monster $monster = null;

    /** @var Stats|null Character stats (HP, MP, attributes) */
    public ?Stats $stats = null;

    /** @var Inventory|null Character inventory system */
    public ?Inventory $inventory = null;

    /** @var BankManager|null Character bank account */
    public ?BankManager $bank = null;

    /**
     * Constructs a new Character instance.
     * 
     * If character ID is provided, loads existing character data from database.
     * 
     * @param int $accountID Account ID this character belongs to
     * @param int|null $characterID Optional character ID to load
     */
    public function __construct($accountID, $characterID = null) {
        $this->accountID = $accountID;
        $this->stats = new Stats($characterID ?? 0);

        if ($characterID) {
            $this->id = $characterID;
            $this->inventory = new Inventory($this->id);
            $this->load($this->id);
            $this->stats->set_id($this->id);
        }
    }

    /**
     * Magic method for dynamic property access and modification.
     * 
     * Handles get/set operations, mathematical operations (add, sub, mul, div, exp, mod),
     * and property dump/restore operations via PropSuite trait.
     * 
     * @param string $method Method name to invoke
     * @param array $params Parameters for the method
     * @return mixed Result of the invoked method
     */
    public function __call($method, $params) {
        global $db, $log;

        if (!count($params)) {
            $params = null;
        }

        $matches = [];
        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::CHARACTER);
        }
    }

    /**
     * Gets the next available character slot for an account.
     * 
     * Checks slots 1-3 and returns the first available one.
     * 
     * @param int $accountID Account ID to check
     * @return int Slot number (1-3) or -1 if all slots are full
     */
    private function getNextCharSlotID($accountID): int {
        global $db, $t;
        $sqlQuery = "SELECT IF (`char_slot1` IS NULL, 1, IF (`char_slot2` IS NULL, 2, IF (`char_slot3` IS NULL, 3, -1))) AS `free_slot` FROM {$t['accounts']} WHERE `id` = ?";
        return intval($db->execute_query($sqlQuery, [ $accountID ])->fetch_assoc()['free_slot']);
    }

    /**
     * Retrieves the account ID for a given character.
     * 
     * @param int $characterID Character ID to lookup
     * @return int Account ID if found, -1 if not found
     */
    public static function getAccountID($characterID): int {
        global $db, $t;
        $sql_query = "SELECT `account_id` FROM {$t['characters']} WHERE `id` = ?";
        $result = $db->execute_query($sql_query, [ $characterID ])->fetch_column();

        if (!$result) {
            return -1;
        }

        return $result;
    }
}