<?php
namespace Game\Monster;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Traits\PropSuite\PropSuite;

/**
 * Manages combat statistics for monster entities.
 * Similar to Character\Stats but includes experience/gold rewards and uses MSTATS PropType.
 * 
 * @method int get_id() Gets stats ID (monster ID)
 * @method int get_hp() Gets current HP
 * @method int get_maxHP() Gets maximum HP
 * @method int get_mp() Gets current MP
 * @method int get_maxMP() Gets maximum MP
 * @method int get_ep() Gets current EP
 * @method int get_maxEP() Gets maximum EP
 * @method int get_str() Gets strength
 * @method int get_int() Gets intelligence
 * @method int get_def() Gets defense
 * @method int get_luk() Gets luck
 * @method float get_expAwarded() Gets experience reward on defeat
 * @method float get_goldAwarded() Gets gold reward on defeat
 * 
 * @method void set_id(int $id) Sets stats ID
 * @method void set_hp(int $hp) Sets current HP
 * @method void set_maxHP(int $maxHP) Sets maximum HP
 * @method void set_mp(int $mp) Sets current MP
 * @method void set_maxMP(int $maxMP) Sets maximum MP
 * @method void set_ep(int $ep) Sets current EP
 * @method void set_maxEP(int $maxEP) Sets maximum EP
 * @method void set_str(int $str) Sets strength
 * @method void set_int(int $int) Sets intelligence
 * @method void set_def(int $def) Sets defense
 * @method void set_luk(int $luk) Sets luck
 * @method void set_expAwarded(float $expAwarded) Sets experience reward
 * @method void set_goldAwarded(float $goldAwarded) Sets gold reward
 * 
 * @method void add_hp(int $amount) Adds HP (capped at maxHP)
 * @method void sub_hp(int $amount) Subtracts HP
 * @method void add_mp(int $amount) Adds MP (capped at maxMP)
 * @method void sub_mp(int $amount) Subtracts MP
 * @method void add_ep(int $amount) Adds EP (capped at maxEP)
 * @method void sub_ep(int $amount) Subtracts EP
 * @method void add_str(int $amount) Increases strength
 * @method void add_int(int $amount) Increases intelligence
 * @method void add_def(int $amount) Increases defense
 * @method void add_luk(int $amount) Increases luck
 */
class Stats {
    use PropSuite;

    /** @var int Stats identifier (matches monster ID) */
    private int $id;
    
    /** @var int Current health points */
    private int $hp     = 100;
    
    /** @var int Maximum health points */
    private int $maxHP  = 100;
    
    /** @var int Current mana points */
    private int $mp     = 100;
    
    /** @var int Maximum mana points */
    private int $maxMP  = 100;
    
    /** @var int Current energy points */
    private int $ep     = 100;
    
    /** @var int Maximum energy points */
    private int $maxEP  = 100;

    /** @var int Strength (physical attack power) */
    private int $str    = 10;
    
    /** @var int Intelligence (magic power) */
    private int $int    = 10;
    
    /** @var int Defense (damage reduction) */
    private int $def    = 10;
    
    /** @var int Luck (affects critical hits, drops) */
    private int $luk    = 3;
    
    /** @var float Experience points awarded when monster defeated */
    private float $expAwarded = 0;
    
    /** @var float Gold currency awarded when monster defeated */
    private float $goldAwarded = 0;

    /**
     * Creates monster stats instance.
     * 
     * @param int $monsterID ID of parent monster
     */
    public function __construct($monsterID = 0) {
        $this->id = $monsterID;
    }

    /**
     * Magic method routing calls to PropSuite components.
     * Handles get_/set_ (propSync), add_/sub_/mul_/div_ (propMod), propDump/propRestore.
     * Uses PropType::MSTATS for database table routing.
     * 
     * @param string $method Method name
     * @param array $params Method parameters
     * @return mixed Result from PropSuite method
     */
    public function __call($method, $params) {
        global $db;

        if (!count($params)) {
            $params = null;
        }

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::MSTATS);
        }
    }

    /**
     * Serializes stats to array for JSON encoding.
     * 
     * @return array All stat properties as associative array
     */
    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}