<?php
namespace Game\Monster;
use Game\Monster\Enums\MonsterScope;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Monster\Stats;

class Monster {
    use PropSuite;
    private ?int $id;
    private int $accountID;
    private int $characterID;
    private int $level;
    private string $name;
    private $scope;
    private string $seed;
    private int $summondBy; // Global or Zone monsters
    private int $dropLevel = 1;
    private string $monsterClass;
    
    public $stats;

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
        } elseif (preg_match('/^(dump|restore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::MONSTER);
        }
    }



    private function scale_monster(Monster $monster, int $player_level): Monster {
        global $log;

        $stats         = ["level", "hp", "mp", "str", "def", "int", "expAwarded", "goldAwarded"];
        $bases         = [1.0, 10.0, 10.0, 2.0, 2.0, 2.0, 5.0, 5.0];  // These are additional values
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
