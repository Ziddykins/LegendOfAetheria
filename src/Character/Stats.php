<?php
namespace Game\Character;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;
use Game\Traits\Enums\PropType;

class Stats {
    use PropConvert;
    use Propsync;
    
    private int $id;

    private int $hp     = 100;
    private int $maxHP  = 100;
    private int $mp     = 100;
    private int $maxMP  = 100;
    private int $ep     = 100;
    private int $maxEP  = 100;

    private int $str    = 10;
    private int $int    = 10;
    private int $def    = 10;
    private int $luk    = 3;
    private int $exp    = 0;
    private int $maxExp = 100;
    private int $ap     = 0;

    /* Enum CharacterStatus, constants.php */
    //private CharacterStatus $status;
    
    public function __construct ($characterID = 0) {
        $this->id = $characterID;

    }
    public function __call($method, $params): mixed {
        global $log;
        return $this->propSync($method, $params, PropType::CSTATS);
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}