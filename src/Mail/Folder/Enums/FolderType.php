<?php
namespace Game\Mail\Folder\Enums;

use Error;

/**
 * Class FolderType
 *
 * Defines the available folder types used within the mail system
 * (e.g., INBOX, SENT, DRAFT, TRASH, ARCHIVE).
 *
 * @package Game\Mail\Folder\Enums
 */
enum FolderType {
    case INBOX;
    case DRAFTS;
    case OUTBOX;
    case DELETED;

    public static function name_to_value(string $folder) {
        foreach (self::cases() as $case) {
            if ($folder == $case->name) {
                return $case;
            }
        }
        throw new Error("Not valid backing member of enum FolderType");
    }
}