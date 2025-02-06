<?php
namespace Game\Monster;
use Game\Monster\Enums\Scope;
use Game\Character\Character;

class Pool {
    public $monsters = [];

    public function __construct() {

    }

    private function get_monster_count() {
        return count($this->monsters);
    }

    public function random_monster(Scope $scope, $characterID = null): Monster{
        $character = new Character($_SESSION['account-id'], $_SESSION['character-id']);
        $character->load();

        $monster = $this->monsters[rand(0,$this->get_monster_count()-1)];
        $monster->set_characterID($characterID);
        $monster->set_scope($scope->name);


        $this->scale_monster($monster, $character->get_level());
        return $monster;
    }

    private function scale_monster(Monster $monster, int $player_lvl) {
        global $log;

        $stats   = ["level", "hp", "mp", "str", "def", "int", "expAwarded", "goldAwarded"];
        $bases   = [1.0, 10.0, 10.0, 2.0, 2.0, 2.0, 5.0, 5.0];
        $multi   = [0.1,  0.5,  0.5, 0.3, 0.3, 0.3, 0.7, 0.7];
        $st_dv   = random_float(0.001, 0.5);

        for ($i=0; $i<count($stats)-1; $i++) {
            $calculated_stat = $bases[$i] * (1 + ($player_lvl - 1) * $multi[$i]) + $st_dv * ($player_lvl - 1);
            $func = "set_{$stats[$i]}";
            $monster->$func($calculated_stat);
            $log->debug("base: $bases[$i], multi: $multi[$i], st_dv: $st_dv, stat: $calculated_stat");
            $log->debug("\$bases[$i] * (1 + (\$player_lvl - 1) * \$multi[$i]) + \$st_dv * (\$player_lvl - 1);");
            $log->debug("STAT: {$stats[$i]} -> $calculated_stat");

        }

        return $monster;
    }
}