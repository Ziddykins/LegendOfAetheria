<?php

namespace Game\Abstract;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;

abstract class BaseStats implements \JsonSerializable {
    use PropSuite;
        /** @var int Character ID these stats belong to */
    protected int $id;

    /** @var int Current health points */
    protected int $hp     = 100;
    
    /** @var int Maximum health points */
    protected int $maxHP  = 100;
    
    /** @var int Current mana points */
    protected int $mp     = 100;
    
    /** @var int Maximum mana points */
    protected int $maxMP  = 100;
    
    /** @var int Strength - affects physical damage */
    protected int $str = 10;
    
    /** @var int Intelligence - affects magical damage */
    protected int $int = 10;
    
    /** @var int Defense - reduces physical damage */
    protected int $def = 10;

     /** @var int Speed - determines turn order */
    protected int $sped = 3;
    
    /** @var int Magic defense - reduces magical damage */
    protected int $mdef = 10;
    
    /** @var int Critical hit chance */
    protected int $crit = 0;
    
    /** @var int Dodge chance */
    protected int $dodg = 0;
    
    /** @var int Block chance */
    protected int $blck = 0;
    
    /** @var int Accuracy */
    protected int $accu = 0;
    
    /** @var int Resistance to status effects */
    protected int $rsst = 0;

    abstract protected function getType(): PropType;
    
    public function __construct(int $id = 0) {
        $this->id = $id;
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
            return $this->propSync($method, $params, $this->getType());
        }
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}
