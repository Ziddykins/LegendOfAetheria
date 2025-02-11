<?php
namespace Game\Monster;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;
use Game\Traits\Enums\Type;
use Game\Monster\Enums\Scope;

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
    private int $hp;
    private int $maxHP;
    private int $mp;
    private int $maxMP;
    private int $str;
    private int $int;
    private int $def;
    private int $dropLevel;
    private int $expAwarded;
    private int $goldAwarded;
    private string $monsterClass;

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

    public function __construct(Scope $scope, $char_id = null, $account_id = null) {
        $this->scope = $scope;
        $this->seed  = bin2hex(random_bytes(8));
        $this->characterID = $char_id  ?: -1;
        $this->accountID = $account_id ?: -1;
        $this->id = -1;

        if ($scope == Scope::PERSONAL) {
            $this->summondBy = -1;
        } else {
            $this->summondBy =  $char_id;
        }
    }
}
