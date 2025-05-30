<?php
namespace Game\Character;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;

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
    private int $exp    = 0;
    private int $maxExp = 100;
    private int $ap     = 0;

    public function __construct($characterID = 0) {
        $this->id = $characterID;
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
            return $this->propSync($method, $params, PropType::CSTATS);
        }
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}