<?php
namespace Game\Familiar\Eggs;

/**
 * Represents a familiar egg with rarity attributes and hatching mechanics.
 * Eggs are acquired by players, have a hatch timer, and produce familiars when hatched.
 * Rarity determines the potential quality of the hatched familiar.
 */
class Egg {   
    /** @var mixed Rarity level of the egg (affects hatched familiar quality) */
    private $rarity;
    
    /** @var mixed Hex color code representing rarity visually */
    private $rarityColor;
    
    /** @var mixed Whether the egg has been hatched */
    private $hatched;
    
    /** @var mixed Timestamp when the egg will hatch */
    private $hatchTime;
    
    /** @var mixed Last dice roll result used to determine rarity */
    private $lastRoll;
    
    /** @var mixed Timestamp when the egg was acquired */
    private $dateAcquired;
}