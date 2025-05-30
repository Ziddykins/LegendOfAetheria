<?php
namespace Game\Monster;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Traits\PropSuite\PropSuite;

class Stats {
    use PropSuite;

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
        } elseif (preg_match('/^(dump|restore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::MSTATS);
        }
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}