<?php
namespace Game\Character;
use Game\Inventory\Inventory;
use Game\Monster\Monster;
use Game\Traits\Enums\PropType;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;

class Character {
    use PropConvert;
    use Propsync;
    
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
    private $exp = 0;

    private $floor = 1;
    private $description = 'None Provided';

    /* class Monster */
    public $monster;

    /* class Stats */
    public $stats;

    /* class Inventory */
    public $inventory;

    public function __construct($accountID, $characterID = null) {
        $this->accountID = $accountID;
        $this->id = $characterID;
    }

    public function __call($method, $params): mixed {
        if ($method == 'propSync') {
            return -1;
        }
        
        return $this->propSync($method, $params, PropType::CHARACTER);
    }

    private function getNextCharSlotID($accountID): int {
        global $db;
        $sqlQuery = "SELECT IF (`char_slot1` IS NULL, 1, IF (`char_slot2` IS NULL, 2, IF (`char_slot3` IS NULL, 3, -1))) AS `free_slot` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
        return intval($db->execute_query($sqlQuery, [ $accountID ])->fetch_assoc()['free_slot']);
    }
}