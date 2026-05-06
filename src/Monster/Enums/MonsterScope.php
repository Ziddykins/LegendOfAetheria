<?php
namespace Game\Monster\Enums;
use Game\Traits\EnumExtender\EnumExtender;

/**
 * Defines monster spawn scopes determining visibility and combat participation rules.
 * 
 * - GLOBAL: World bosses visible to all players, rewards based on damage contribution
 * - ZONE: Area-specific monsters for players in the zone, leaving forfeits contribution
 * - PERSONAL: Solo encounters visible only to one player
 * - NONE: Default/uninitialized state
 * 
 * @method static MonsterScope name_to_enum(string $name) Converts a case name to enum instance
 */
enum MonsterScope: int {
    use EnumExtender;
    
    /** World boss - all players can attack, rewards by contribution */
    case GLOBAL = 0;
    
    /** Zone-restricted - only players in area can contribute */
    case ZONE = 1;
    
    /** Personal encounter - solo only */
    case PERSONAL = 2;
    
    /** Uninitialized/default state */
    case NONE = 3;
}