<?php
namespace Game\Inventory\Enums;

/**
 * Defines rarity tiers for game objects (items, familiars, etc.) with probability thresholds.
 * Uses descending percentage values where higher rolls yield better rarities.
 * Provides static method to convert dice roll to rarity name.
 */
enum ObjectRarity: string {
    /**
     * Determines rarity based on random roll percentage.
     * Compares roll against threshold values to find matching tier.
     * 
     * @param float $roll Dice roll result (0.0-100.0)
     * @return string Rarity name (e.g., "LEGENDARY", "COMMON")
     */
    public static function getObjectRarity(float $roll): string {
        foreach (self::cases() as $rarity) {
            if ($roll >= $rarity->value) {
                return $rarity->name;
            }
        }
        return "none";
    }

    /** Junk tier - rolls 50.0+ (50% chance) */
    case WORTHLESS = "50.0";
    
    /** Damaged/old items - rolls 30.0-49.9 (20% chance) */
    case TARNISHED = "30.0";
    
    /** Standard quality - rolls 20.0-29.9 (10% chance) */
    case COMMON    = "20.0";
    
    /** Magically enhanced - rolls 12.0-19.9 (8% chance) */
    case ENCHANTED = "12.0";
    
    /** Powerful magic - rolls 8.0-11.9 (4% chance) */
    case MAGICAL   = "8.00";
    
    /** Storied artifacts - rolls 5.0-7.9 (3% chance) */
    case LEGENDARY = "5.00";
    
    /** Grand artifacts - rolls 2.5-4.9 (2.5% chance) */
    case EPIC      = "2.50";
    
    /** Ancient relics - rolls 1.5-2.49 (1% chance) */
    case MYSTIC    = "1.50";
    
    /** Hero-forged items - rolls 0.75-1.49 (0.75% chance) */
    case HEROIC    = "0.75";
    
    /** Notorious artifacts - rolls 0.24-0.74 (0.51% chance) */
    case INFAMOUS  = "0.24";
    
    /** Divine creations - rolls 0.01-0.23 (0.23% chance) */
    case GODLY     = "0.01";
    
    /** No rarity/default fallback */
    case NONE      = "0.00";
}