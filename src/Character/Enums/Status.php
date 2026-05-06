<?php
namespace Game\Character\Enums;
use Game\Traits\EnumExtender\EnumExtender;

/**
 * Defines character status effects using bitmask values for efficient bitwise operations.
 * Multiple statuses can be combined using bitwise OR, allowing characters to have
 * multiple simultaneous status effects in combat.
 * 
 * @method static Status name_to_enum(string $name) Converts a case name to enum instance
 */
enum Status: int {
    use EnumExtender;
    
    /** No status effects - character is in normal condition */
    case HEALTHY        = 1;
    
    /** Taking damage over time from poison - reduces HP gradually */
    case POISONED       = 2;
    
    /** Reduced accuracy - makes attacks more likely to miss */
    case BLINDED        = 4;
    
    /** Reduced effectiveness in combat - may affect attack/defense */
    case SCARED         = 8;
    
    /** Carrying too much weight - reduces speed and mobility */
    case OVERENCUMBERED = 16;
    
    /** Taking heat damage - may lead to burning status */
    case OVERHEATED     = 32;
    
    /** Cannot act for one or more turns */
    case STUNNED        = 64;
    
    /** Immobilized by ice - cannot move, may take damage */
    case FROZEN         = 128;
    
    /** Taking fire damage over time - continuous HP loss */
    case BURNING        = 256;
    
    /** May attack allies or act randomly - loss of control */
    case CONFUSED       = 512;
    
    /** Under enemy control - may be forced to help opponent */
    case CHARMED        = 1024;
    
    /** Cannot act - vulnerable to attacks */
    case SLEEPING       = 2048;
    
    /** Character has been defeated - HP at 0 */
    case DEAD           = 4096;
    
    /** Losing HP gradually - stacks with other damage effects */
    case BLEEDING       = 8192;
}