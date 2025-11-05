<?php
namespace Game\Monster\Enums;

/**
 * Defines special monster variants with unique stat modifiers and mechanics.
 * These modifiers create varied encounters with different risk/reward profiles.
 */
enum Specialty {
    /** Giant variant - 2-3x health pool for longer battles */
    case GIANT;
    
    /** Mini variant - 1/2 to 1/4 health for quick encounters */
    case MINI;
    
    /** Hardened variant - 3-4x defense, high physical resistance */
    case HARDENED;
    
    /** Enraged variant - 4x strength but -5% HP, glass cannon */
    case ENRAGED;
    
    /** Experienced variant - 3x EXP reward, +2% HP and DEF */
    case EXPERIENCED;
    
    /** Packrat variant - better loot rarity (÷1.5 threshold), 3x gold */
    case PACKRAT;
    
    /** Defecting variant - can be spared and recruited instead of killed */
    case DEFECTING;
    
    /** Leader variant - 5-10x all stats, boss-tier encounter */
    case LEADER;
}