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

    private int $str = 10;
    private int $int = 10;
    private int $def = 10;
    private int $luck = 3;
    private int $chsm = 3;
    private int $dext = 3;
    private int $sped = 3;
    private int $mdef = 10;
    private int $crit = 0;
    private int $dodg = 0;
    private int $blck = 0;
    private int $accu = 0;
    private int $rsst = 0;
    private int $evsn = 0;
    private int $rgen = 0;
    private int $absb = 0;
    
    public function __construct($characterID = 0) {
        $this->id = $characterID;
    }

    public function __call($method, $params) {
        global $db;

        if (!count($params)) {
            $params = null;
        }

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
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