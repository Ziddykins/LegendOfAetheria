<?php
namespace Game\Battle\Enums;
use Game\Traits\EnumExtender\EnumExtender;

/**
 * Turn enum defines whose turn it is in battle.
 * 
 * @package Game\Battle\Enums
 */
enum Turn: int{
    use EnumExtender;
    
    /** Enemy's turn to act */
    case ENEMY = 0;
    
    /** Player's turn to act */
    case PLAYER = 1;
}
?>