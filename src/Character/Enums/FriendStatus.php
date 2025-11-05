<?php
namespace Game\Character\Enums;
use Game\Traits\EnumExtender\EnumExtender;

/**
 * Represents the friendship status between two characters.
 * Used to track friend requests, established friendships, and blocking relationships.
 * 
 * @method static FriendStatus name_to_enum(string $name) Converts a case name to enum instance
 */
enum FriendStatus {
    use EnumExtender;
    
    /** No friendship relationship exists */
    case NONE;
    
    /** Current character has sent a friend request to another character */
    case REQUEST_SENT;
    
    /** Current character has received a friend request from another character */
    case REQUEST_RECV;
    
    /** Both characters have accepted friend requests - active friendship */
    case MUTUAL;
    
    /** Current character has blocked another character */
    case BLOCKED;
    
    /** Current character has been blocked by another character */
    case BLOCKED_BY;
}