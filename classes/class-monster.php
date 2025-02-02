<?php
class Monster {
    use HandlePropsAndCols;
    use HandlePropSync;
    private $id;
    private $accountID;
    private $characterID;
    private $name;
    private $scope;
    private $seed;
    private $summondBy; // Global or Zone monsters
    private $hp;
    private $maxHP;
    private $mp;
    private $maxMP;
    private $strength;
    private $intelligence;
    private $defense;
    private $dropLevel;
    private $expAwarded;
    private $goldAwarded;
    private $monsterClass;

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
        if ($method == 'prop_sync') {
            return;
        }
        
        return $this->prop_sync($method, $params, PropsyncType::MONSTER);
    }

    public function __construct(MonsterScope $scope, $character_id) {
        $this->scope = $scope;
        $this->seed  = bin2hex(random_bytes(8));

        if ($scope == MonsterScope::PERSONAL) {
            $this->characterID = $character_id;
            $this->summondBy = -1;
        } else {
            $this->summondBy = $character_id;
            $this->characterID = -1;
        }
    }
}

class MonsterPool {
    public $monsters = [];

    public function __construct() {

    }

    private function get_monster_count() {
        return count($this->monsters);
    }

    public function random_monster() {
        return $this->monsters[rand(0,$this->get_monster_count()-1)];
    }
}

?>
