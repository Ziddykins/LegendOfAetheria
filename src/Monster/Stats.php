<?php
namespace Game\Monster;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Abstract\BaseStats;

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
class Stats extends BaseStats {
    /** @var float Experience points awarded when monster defeated */
    protected float $expAwarded = 0;
    
    /** @var float Gold currency awarded when monster defeated */
    protected float $goldAwarded = 0;

    /**
     * Creates monster stats instance.
     * 
     * @param int $monsterID ID of parent monster
     */
    public function __construct($monsterID = 0) {
        parent::__construct($monsterID);
    }

    protected function getType(): PropType {
        return PropType::MSTATS;
    }
}