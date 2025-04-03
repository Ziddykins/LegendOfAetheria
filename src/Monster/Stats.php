<?php
namespace Game\Monster;
use Game\Traits\PropManager\Enums\PropType;
use Game\Traits\PropManager\PropManager;

class Stats {
    use PropManager;

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

    public function __construct ($monsterID = 0) {
        $this->id = $monsterID;

    }
    public function __call($method, $params) {
        if ($method == 'propSync' || $method == 'propMod') {
            return;
        }

        switch ($method) {
            case 'propSync':
                return $this->propSync($method, $params, PropType::MSTATS);
            case 'propMod':
                return $this->propMod($method, $params);
        }
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}