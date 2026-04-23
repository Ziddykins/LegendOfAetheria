<?php
namespace Game\Mail\MailBox;

use Game\Mail\Folder\Folder;
use Game\Mail\Folder\Enums\FolderType;

/**
 * Manages a character's mail system with multiple folders and focused folder selection.
 * Provides methods to switch between folders, populate with messages, and count folder contents.
 */
class MailBox {
    /** @var int Character ID who owns this mailbox */
    public $characterID;
    
    /** @var Folder Currently active/visible folder */
    public $focusedFolder;

    /**
     * Constructs a new MailBox instance using the provided account ID.
     *
     * @param int $characterID The unique identifier for the account linked to this mailbox.
     */
    public function __construct(int $character_id) {
        $this->characterID = $character_id;
    }

    /**
     * Sets the currently viewed folder for the mailbox.
     *
     * @param FolderType $folder The folder type (e.g., INBOX, SENT) to be set as active.
     * @return void
     */
    public function setFocusedFolder(FolderType $folder): void {
        $this->focusedFolder = new Folder($this->characterID, $folder);
    }

    /**
     * Populates the focused folder with messages from the database.
     * If the focused folder is not set, a warning is logged.
     *
     * @return void
     */
    public function populateFocusedFolder(): void {
        global $log;
        if (isset($this->focusedFolder)) {
            $this->focusedFolder->getMessages();
        } else {
            $log->warning('Focused folder not yet populated in ',
                [ 'File' => __FILE__, 'Line' => __LINE__ - 2 ]);
        }
    }

    /**
     * Counts number of messages in a specific folder for a character.
     * Static method for quick folder counts without instantiating MailBox.
     * 
     * @param FolderType $folder Folder to count messages in
     * @param int $character_id Character whose messages to count
     * @return int Number of messages in folder
     */
    public static function getFolderCount(FolderType $folder, int $character_id): int {
        global $db, $t;
        $sql_query = "SELECT COUNT(`id`) FROM {$t['mail']} WHERE `r_cid` = ? AND `folder` = ?";
        $result = $db->execute_query($sql_query, [ $character_id, $folder->name ])->fetch_column();

        return $result;
    }
}