<?php
namespace Game\Inventory\Enums;

enum ObjectRarity: string {
    public static function getObjectRarity($roll) {
        foreach (self::cases() as $rarity) {
            if ($roll >= $rarity->value) {
                return $rarity;
            }
        }
    }

    case WORTHLESS = "50.0";  /* 50.00% chance */
    case TARNISHED = "30.0";  /* 20.00% chance */
    case COMMON    = "20.0";  /* 10.00% chance */
    case ENCHANTED = "12.0";  /*  8.00% chance */
    case MAGICAL   = "8.0";  /*  4.00% chance */
    case LEGENDARY = "5.0";  /*  3.00% chance */
    case EPIC      = "2.50";  /*  2.50% chance */
    case MYSTIC    = "1.50";  /*  1.00% chance */
    case HEROIC    = "0.75";  /*  0.75% chance */
    case INFAMOUS  = "0.24";  /*  0.51% chance */
    case GODLY     = "0.01";  /*  0.23% chance */
    case NONE      = "0.00";  /* Used to determine if player has egg yet */
}