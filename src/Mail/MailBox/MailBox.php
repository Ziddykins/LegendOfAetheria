<?php
namespace Game\Mail\MailBox;

use Game\Mail\Folder\Folder;
use Game\Mail\Folder\Enums\FolderType;

class MailBox {
    public $accountID;
    public $focusedFolder;

    /**
     * Constructs a new MailBox instance using the provided account ID.
     *
     * @param int $accountID The unique identifier for the account linked to this mailbox.
     */
    public function __construct(int $accountID) {
        $this->accountID = $accountID;
    }

    /**
     * Sets the currently viewed folder for the mailbox.
     *
     * @param FolderType $folder The folder type (e.g., INBOX, SENT) to be set as active.
     * @return void
     */
    public function setFocusedFolder(FolderType $folder): void {
        $this->focusedFolder = new Folder($this->accountID, $folder);
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
}