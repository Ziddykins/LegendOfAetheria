<?php
namespace Game\Monster;
use Game\Monster\Enums\MonsterScope;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Monster\Stats;

/**
 * Represents an enemy monster with stats, level, loot, and combat mechanics.
 * Monsters can be GLOBAL (world bosses), ZONE (area-specific), or PERSONAL (solo encounters).
 * Uses PropSuite for database synchronization with MONSTER type.
 * 
 * @method int get_id() Gets monster ID
 * @method int get_accountID() Gets account ID if personally spawned
 * @method int get_characterID() Gets character ID if personally spawned
 * @method int get_level() Gets monster level
 * @method string get_name() Gets monster name
 * @method MonsterScope get_scope() Gets spawn scope (GLOBAL/ZONE/PERSONAL)
 * @method string get_seed() Gets random seed for generation
 * @method int get_summondBy() Gets ID of summoner (for global/zone monsters)
 * @method int get_dropLevel() Gets loot drop level/tier
 * @method string get_monsterClass() Gets monster type/class
 * @method Stats get_stats() Gets combat statistics
 * 
 * @method void set_id(int $id) Sets monster ID
 * @method void set_accountID(int $accountID) Sets account ID
 * @method void set_characterID(int $characterID) Sets character ID
 * @method void set_level(int $level) Sets monster level
 * @method void set_name(string $name) Sets monster name
 * @method void set_scope(MonsterScope $scope) Sets spawn scope
 * @method void set_seed(string $seed) Sets generation seed
 * @method void set_summondBy(int $summondBy) Sets summoner ID
 * @method void set_dropLevel(int $dropLevel) Sets loot tier
 * @method void set_monsterClass(string $monsterClass) Sets monster class
 * @method void set_stats(Stats $stats) Sets combat stats
 */
class Monster {
    use PropSuite;
    /** @var int|null Unique monster identifier */
    private ?int $id = null;
    
    /** @var int|null Account ID (for personal monsters) */
    private ?int $accountID = null;
    
    /** @var int|null Character ID (for personal monsters) */
    private ?int $characterID = null;
    
    /** @var int Current level of the monster */
    private int $level = 1;
    
    /** @var string|null Display name of the monster */
    private ?string $name = null;
    
    /** @var MonsterScope|null Spawn scope (GLOBAL/ZONE/PERSONAL) */
    private ?MonsterScope $scope = null;
    
    /** @var string Random seed for stat generation */
    private string $seed = '';
    
    /** @var int|null ID of entity that summoned this monster (global/zone only) */
    private ?int $summondBy = null;
    
    /** @var int Loot drop tier/quality level */
    private int $dropLevel = 1;
    
    /** @var string|null Monster type/class identifier */
    private ?string $monsterClass = null;
    
    /** @var Stats|null Combat statistics (HP, MP, STR, DEF, etc.) */
    public ?Stats $stats = null;

    /**
     * Creates a new monster with specified spawn scope.
     * Generates random seed for stat variance.
     * 
     * @param MonsterScope $scope Where this monster spawns (GLOBAL/ZONE/PERSONAL)
     */
    public function __construct(MonsterScope $scope) {
        $this->scope = $scope;
        $this->seed  = bin2hex(random_bytes(8));
    }

    /**
    * Magic method to handle dynamic getters and setters for the Monster class properties.
    *
    * This method intercepts calls to properties that start with "get_" or "set_" and performs
    * the corresponding actions. For getters, it logs the access and returns the property value.
    * For setters, it updates the corresponding database column and logs the operation.
    *
    * @param string $method The name of the method being called.
    * @param array $params The parameters passed to the method.
    *
    * @return mixed The value of the property if a getter is called, or void if a setter is called.
    */
    public function __call($method, $params) {
        global $db, $log;

        
        if (!count($params)) {
            $params = null;
        }

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::MONSTER);
        }
    }

    /**
     * Scales monster stats based on player level with random variance.
     * Adjusts HP, MP, STR, DEF, INT, and rewards using base + multiplier formulas.
     * 
     * @param Monster $monster Monster to scale
     * @param int $player_level Player's level to scale against
     * @return Monster Scaled monster instance
     */
    private function scale_monster(Monster $monster, int $player_level): Monster {
        global $log;

        $stats         = ["level", "hp", "mp", "str", "def", "int", "expAwarded", "goldAwarded"];
        $bases         = [1.0, 10.0, 10.0, 2.0, 2.0, 2.0, 5.0, 5.0];
        $multipliers   = [0.1, 0.5, 0.5, 0.3, 0.3, 0.3, 0.7, 0.7];
        $std_deviation = random_float(-0.5, 0.5, 2);

        for ($i=0; $i<count($stats); $i++) {
            // Calculate the additional stat value based on player level
            $additional_stat = $bases[$i] * (1 + ($player_level - 1) * $multipliers[$i]) + $std_deviation * ($player_level - 1);
            
            if ($stats[$i] == "level") {
                $monster->set_level($additional_stat);
                continue;
            }

            if ($additional_stat < 0) {
                $additional_stat = 0;
            }

            // Add the scaled value to existing stat instead of replacing it
            $get_func = "get_{$stats[$i]}";
            $set_func = "set_{$stats[$i]}";
            $current_value = $monster->stats->$get_func();
            $monster->stats->$set_func($current_value + $additional_stat);
        }

        // Ensure maxHP is updated to match new HP if it changed
        $monster->stats->set_maxHP($monster->stats->get_hp());

        return $monster;
    }
    
    public function random_monster($player_level): void{
        global $system;
        $monster = $system->monsters[random_int(0, count($system->monsters) - 1)];
        $temp_stats_arr = explode(',', $monster);

        $stats = new Stats($this->id);

        $this->name = $temp_stats_arr[0];

        $stats->set_hp(intval($temp_stats_arr[1]));
        $stats->set_maxHP(intval($temp_stats_arr[2]));
        $stats->set_mp(intval($temp_stats_arr[3]));
        $stats->set_maxMP(intval($temp_stats_arr[4]));
        $stats->set_str(intval($temp_stats_arr[5]));
        $stats->set_int(intval($temp_stats_arr[6]));
        $stats->set_def(intval($temp_stats_arr[7]));

        $this->dropLevel(intval($temp_stats_arr[8]));
        $this->expAwarded(intval($temp_stats_arr[9]));
        $this->goldAwarded(intval($temp_stats_arr[10]));
        $this->monsterClass($temp_stats_arr[11]);

        $this->stats = $stats;

        $this->scale_monster($this, $player_level);
    }
}
