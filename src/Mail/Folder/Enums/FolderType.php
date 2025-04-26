<?php
namespace Game\Mail\Folder\Enums;

use Game\Traits\EnumExtender\EnumExtender;

/**
 * Class FolderType
 *
 * Defines the available folder types used within the mail system
 * (e.g., INBOX, SENT, DRAFT, TRASH, ARCHIVE).
 *
 * @package Game\Mail\Folder\Enums
 */
enum FolderType {
    use EnumExtender;
    case INBOX;
    case DRAFTS;
    case OUTBOX;
    case DELETED;
}