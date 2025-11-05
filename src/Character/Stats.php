<?php
namespace Game\Character;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;

/**
 * Stats class manages character combat and attribute statistics.
 * 
 * Tracks health, mana, energy, and various combat attributes.
 * Uses PropSuite trait for dynamic property management and database synchronization.
 * 
 * @package Game\Character
 * 
 * @method int get_id() Gets the character ID
 * @method int get_hp() Gets current HP
 * @method int get_maxHP() Gets maximum HP
 * @method int get_mp() Gets current MP
 * @method int get_maxMP() Gets maximum MP
 * @method int get_ep() Gets current EP
 * @method int get_maxEP() Gets maximum EP
 * @method int get_ap() Gets ability points
 * @method int get_str() Gets strength
 * @method int get_int() Gets intelligence
 * @method int get_def() Gets defense
 * @method int get_luck() Gets luck
 * @method int get_chsm() Gets charisma
 * @method int get_dext() Gets dexterity
 * @method int get_sped() Gets speed
 * @method int get_mdef() Gets magic defense
 * @method int get_crit() Gets critical hit chance
 * @method int get_dodg() Gets dodge chance
 * @method int get_blck() Gets block chance
 * @method int get_accu() Gets accuracy
 * @method int get_rsst() Gets resistance
 * @method int get_rgen() Gets regeneration
 * 
 * @method void set_hp(int $hp) Sets current HP
 * @method void set_maxHP(int $maxHP) Sets maximum HP
 * @method void set_mp(int $mp) Sets current MP
 * @method void set_maxMP(int $maxMP) Sets maximum MP
 * @method void set_ep(int $ep) Sets current EP
 * @method void set_maxEP(int $maxEP) Sets maximum EP
 * @method void set_ap(int $ap) Sets ability points
 * @method void set_str(int $str) Sets strength
 * @method void set_int(int $int) Sets intelligence
 * @method void set_def(int $def) Sets defense
 * @method void set_luck(int $luck) Sets luck
 * @method void set_chsm(int $chsm) Sets charisma
 * @method void set_dext(int $dext) Sets dexterity
 * @method void set_sped(int $sped) Sets speed
 * @method void set_mdef(int $mdef) Sets magic defense
 * @method void set_crit(int $crit) Sets critical hit chance
 * @method void set_dodg(int $dodg) Sets dodge chance
 * @method void set_blck(int $blck) Sets block chance
 * @method void set_accu(int $accu) Sets accuracy
 * @method void set_rsst(int $rsst) Sets resistance
 * @method void set_rgen(int $rgen) Sets regeneration
 * 
 * @method void add_hp(int $amount) Adds HP (capped at maxHP)
 * @method void sub_hp(int $amount) Subtracts HP
 * @method void add_mp(int $amount) Adds MP (capped at maxMP)
 * @method void sub_mp(int $amount) Subtracts MP
 * @method void add_ep(int $amount) Adds EP (capped at maxEP)
 * @method void sub_ep(int $amount) Subtracts EP
 * @method void add_ap(int $amount) Adds ability points
 * @method void sub_ap(int $amount) Subtracts ability points
 * @method void add_str(int $amount) Increases strength
 * @method void add_int(int $amount) Increases intelligence
 * @method void add_def(int $amount) Increases defense
 * @method void add_luck(int $amount) Increases luck
 * @method void add_chsm(int $amount) Increases charisma
 * @method void add_dext(int $amount) Increases dexterity
 * @method void add_sped(int $amount) Increases speed
 * @method void add_mdef(int $amount) Increases magic defense
 * @method void add_crit(int $amount) Increases critical chance
 * @method void add_dodg(int $amount) Increases dodge chance
 * @method void add_blck(int $amount) Increases block chance
 * @method void add_accu(int $amount) Increases accuracy
 * @method void add_rsst(int $amount) Increases resistance
 * @method void add_rgen(int $amount) Increases regeneration
 */
class Stats {
    use PropSuite;
    
    /** @var int Character ID these stats belong to */
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
    
    /** @var int Ability points available for spending */
    private int $ap = 0;

    /** @var int Strength - affects physical damage */
    private int $str = 10;
    
    /** @var int Intelligence - affects magical damage */
    private int $int = 10;
    
    /** @var int Defense - reduces physical damage */
    private int $def = 10;
    
    /** @var int Luck - affects drops and random events */
    private int $luck = 3;
    
    /** @var int Charisma - affects NPC interactions */
    private int $chsm = 3;
    
    /** @var int Dexterity - affects crafting and precision */
    private int $dext = 3;
    
    /** @var int Speed - determines turn order */
    private int $sped = 3;
    
    /** @var int Magic defense - reduces magical damage */
    private int $mdef = 10;
    
    /** @var int Critical hit chance */
    private int $crit = 0;
    
    /** @var int Dodge chance */
    private int $dodg = 0;
    
    /** @var int Block chance */
    private int $blck = 0;
    
    /** @var int Accuracy */
    private int $accu = 0;
    
    /** @var int Resistance to status effects */
    private int $rsst = 0;
    
    /** @var int HP/MP regeneration rate */
    private int $rgen = 0;
    
    /**
     * Constructs a new Stats instance.
     * 
     * @param int $characterID Character ID these stats belong to
     */
    public function __construct($characterID = 0) {
        $this->id = $characterID;
    }

    /**
     * Magic method for dynamic property access and modification.
     * 
     * Handles get/set operations, mathematical operations (add, sub, mul, div, exp, mod),
     * and property dump/restore operations via PropSuite trait.
     * Note: add/sub operations on HP/MP/EP are automatically capped at their max values.
     * 
     * @param string $method Method name to invoke
     * @param array $params Parameters for the method
     * @return mixed Result of the invoked method
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
            return $this->propSync($method, $params, PropType::CSTATS);
        }
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}
