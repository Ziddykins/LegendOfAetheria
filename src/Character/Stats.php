<?php
namespace Game\Character;
use Game\Traits\PropManager\PropManager;
use Game\Traits\PropManager\Enums\PropType;

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
        } else {
            return $this->propSync($method, $params, PropType::CSTATS);
        }
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}