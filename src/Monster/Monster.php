<?php
namespace Game\Monster;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;
use Game\Traits\Enums\Type;
use Game\Monster\Enums\Scope;
use Game\Monster\Stats;
use Game\Character\Character;

class Monster {
    use PropConvert;
    use Propsync;

    private ?int $id;
    private int $accountID;
    private int $characterID;
    private int $level;
    private string $name;
    private $scope;
    private string $seed;
    private int $summondBy; // Global or Zone monsters
    private int $dropLevel = 1;
    private int $expAwarded;
    private int $goldAwarded;
    private string $monsterClass;
    
    public Stats $stats;

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
        if ($method == 'propSync') {
            return;
        }
        
        return $this->propSync($method, $params, Type::MONSTER);
    }

    public function __construct(Scope $scope) {
        $this->scope = $scope;
        $this->seed  = bin2hex(random_bytes(8));
    }

    private function scale_monster(Monster $monster, int $player_lvl) {
        global $log;

        $stats   = ["level", "hp", "mp", "str", "def", "int", "expAwarded", "goldAwarded"];
        $bases   = [1.0, 10.0, 10.0, 2.0, 2.0, 2.0, 5.0, 5.0];
        $multi   = [0.1,  0.5,  0.5, 0.3, 0.3, 0.3, 0.7, 0.7];
        $st_dv   = random_float(-0.5, 0.5);

        for ($i=0; $i<count($stats); $i++) {
            $calculated_stat = $bases[$i] * (1 + ($player_lvl - 1) * $multi[$i]) + $st_dv * ($player_lvl - 1);
            $func = "set_{$stats[$i]}";
            $monster->stats->$func($calculated_stat);
        }


        $monster->stats->set_hp($monster->get_maxHP());
        $monster->stats->set_mp($monster->get_maxMP());

        return $monster;
    }



    public function random_monster(){
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
    }
}
