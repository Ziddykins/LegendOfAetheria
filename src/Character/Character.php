<?php
namespace Game\Character;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Traits\PropSuite\PropSuite;
use Game\Monster\Monster;
use Game\Character\Stats;
use Game\Inventory\Inventory;
use Game\Bank\BankManager;
use Game\Character\Enums\Races;

class Character {
    use PropSuite;
    
    private int $id;
    private int $accountID;
    private int $level = 1;
    private int $x = 0;
    private int $y = 0;
    private int $alignment = 0;
    private int $spindels = 0;
    private int $exp = 0;
    private int $dateCreated;
    private int $floor = 1;

    private float $gold = 1000.0;

    private string $description = 'None Provided';
    private string $location = 'The Shrine';
    private string $name;
    private string $avatar;
    private string $lastAction;

    /* enum Races */
    private Races $race;

    /* class Monster */
    public Monster $monster;

    /* class Stats */
    public Stats $stats;

    /* class Inventory */
    public Inventory $inventory;

    /* class Bank */
    public BankManager $bank;

    public function __construct($accountID, $characterID = null) {
        $this->accountID = $accountID;
        
        if ($characterID) {
            $this->id = $characterID;
            $this->load($this->id);
        }
    }

    public function __call($method, $params) {
        global $db, $log;

        /* If it's a get, this is true */
        if (!count($params)) {
            $params = null;
        }

        /* Avoid loops with propSync triggering itself */
        if ($method == 'propSync' || $method == 'propMod' || $method == 'propConvert' || $method == 'propDump' || $method == 'propRestore') {
            $log->debug("$method loop");
            return;
        }

        $MATCHES = [];
        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(dump|restore)$/', $method, $MATCHES)) {
            $func = $MATCHES[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::CHARACTER);
        }
    }

    private function getNextCharSlotID($accountID): int {
        global $db, $t;
        $sqlQuery = "SELECT IF (`char_slot1` IS NULL, 1, IF (`char_slot2` IS NULL, 2, IF (`char_slot3` IS NULL, 3, -1))) AS `free_slot` FROM {$t['accounts']} WHERE `id` = ?";
        return intval($db->execute_query($sqlQuery, [ $accountID ])->fetch_assoc()['free_slot']);
    }

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