<?php
namespace Game\Inventory\Gems;

/**
 * Defines stat modifiers provided by a socketed gem.
 * Specifies which stat is affected (target) and the magnitude of the effect.
 */
class GemModifiers {
    /** @var int ID of gem these modifiers belong to */
    private int $gemID;
    
    /** @var string Target stat name (e.g., "str", "def", "crit") */
    private string $target;
    
    /** @var int Numeric value of stat modification (positive or negative) */
    private int $effect;

    /**
     * Creates a gem modifier instance.
     * 
     * @param int $gemID ID of parent gem
     */
    public function __construct($gemID = 0) {
        $this->gemID = $gemID;
    }

}