<?php
namespace Game\Monster;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;
use Game\Traits\Enums\Type;

class Stats {
    use PropConvert;
    use Propsync;

    private int $id;


    private int $hp     = 100;
    private int $maxHp  = 100;
    private int $mp     = 100;
    private int $maxMp  = 100;
    private int $ep     = 100;
    private int $maxEp  = 100;

    private int $str    = 10;
    private int $int    = 10;
    private int $def    = 10;
    private int $luk    = 3;

    public function __construct ($monsterID = 0) {
        $this->id = $monsterID;

    }
    public function __call($method, $params): mixed {
        global $log;
        return $this->propSync($method, $params, Type::MSTATS);
    }
}