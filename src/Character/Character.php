<?php
namespace Game\Character;
use Game\Traits\PropManager\Enums\PropType;
use Game\Traits\PropManager\PropManager;

class Character {
    use PropManager;
    
    private $id;
    private $accountID;
    private $name;
    private $race;
    private $avatar;
    private $level = 1;
    private $x = 0;
    private $y = 0;
    private $location = 'The Shrine';
    private $alignment = 0;
    private $gold = 1000;
    private $spindels = 0;
    private $exp = 0;

    private $floor = 1;
    private $description = 'None Provided';

    private $dateCreated;
    private $lastAction;

    /* class Monster */
    public $monster;

    /* class Stats */
    public $stats;

    /* class Inventory */
    public $inventory;

    /* class Bank */
    public $bank;

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
        if ($method == 'propSync' || $method == 'propMod') {
            $log->debug("$method loop");
            return;
        }

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
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