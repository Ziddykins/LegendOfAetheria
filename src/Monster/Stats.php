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
    private float $expAwarded = 0;
    private float $goldAwarded = 0;

    public function __construct($monsterID = 0) {
        $this->id = $monsterID;
    }

    public function __call($method, $params) {
        global $db;

        if (!count($params)) {
            $params = null;
        }

        if ($method == 'propSync' || $method == 'propMod') {
            return;
        }

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } else {
            return $this->propSync($method, $params, PropType::MSTATS);
        }
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}