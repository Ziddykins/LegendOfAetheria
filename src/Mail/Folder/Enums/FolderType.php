<?php
namespace Game\Mail\Folder\Enums;

use Game\Traits\EnumExtender\EnumExtender;

/**
 * Defines mail folder categories for organizing messages.
 * Each character has separate instances of each folder type.
 * 
 * @method static FolderType name_to_enum(string $name) Converts a case name to enum instance
 */
enum FolderType {
    use EnumExtender;
    
    /** Received messages folder */
    case INBOX;
    
    /** Unsent/saved draft messages folder */
    case DRAFTS;
    
    /** Sent messages folder */
    case OUTBOX;
    
    /** Trash/deleted messages folder */
    case DELETED;
}