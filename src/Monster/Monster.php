<?php
namespace Game\Monster;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;
use Game\Traits\Enums\Type;
use Game\Monster\Enums\Scope;

class Monster {
    use PropConvert;
    use Propsync;
    private $id;
    private $accountID;
    private $characterID;
    private $level;
    private $name;
    private $scope;
    private $seed;
    private $summondBy; // Global or Zone monsters
    private $hp;
    private $maxHP;
    private $mp;
    private $maxMP;
    private $str;
    private $int;
    private $def;
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
        if ($method == 'propSync') {
            return;
        }
        
        return $this->propSync($method, $params, Type::MONSTER);
    }

    public function __construct(Scope $scope, $character_id) {
        $this->scope = $scope;
        $this->seed  = bin2hex(random_bytes(8));

        if ($scope == Scope::PERSONAL) {
            $this->characterID = $character_id;
            $this->summondBy = -1;
        } else {
            $this->summondBy = $character_id;
            $this->characterID = -1;
        }
    }

    private function attack(AttackType $attack) {
        
    }
}
