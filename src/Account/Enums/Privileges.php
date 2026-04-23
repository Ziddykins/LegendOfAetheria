<?php
namespace Game\Account\Enums;

use Game\Traits\EnumExtender\EnumExtender;

/**
 * Privileges enum defines account permission levels in the game.
 * 
 * Uses bit flags to allow multiple privilege combinations.
 * Higher values indicate greater permission levels.
 * 
 * @package Game\Account\Enums
 */
enum Privileges: int {
    use EnumExtender;

    /** Account is banned from the game */
    case BANNED = 1;
    
    /** Account is muted (cannot chat) */
    case MUTED = 2;
    
    /** Account has not completed registration */
    case UNREGISTERED = 4;
    
    /** Account email has not been verified */
    case UNVERIFIED = 8;
    
    /** Standard registered user */
    case USER = 16;
    
    /** Moderator with basic moderation powers */
    case MODERATOR = 32;
    
    /** Senior moderator with extended powers */
    case SUPER_MODERATOR = 64;
    
    /** Administrator with game management access */
    case ADMINISTRATOR = 128;
    
    /** Global administrator across all game instances */
    case GLOBAL_ADMINISTRATOR = 256;
    
    /** Game owner with full access */
    case OWNER = 512;
    
    /** Root-level system access */
    case ROOTED = 1024;
}