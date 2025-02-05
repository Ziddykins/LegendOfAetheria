<?php
namespace Game\Mail;
use Game\Mail\Enums\Type;

class MailBox {
    public $accountID;
    public $focusedFolder;

    /**
     * Constructs a new MailBox instance.
     *
     * @param int $accountID The ID of the account associated with the mailbox.
     */
    public function __construct($accountID) {
        $this->accountID = $accountID;
    }

    /**
     * Sets the focused folder for the mailbox. The focused folder is the folder
     * which is currently being viewed in the mail tab.
     *
     * @param \Game\Mail\Enums\Type $folder The type of the folder to set as focused.
     */
    public function setFocusedFolder(Type $folder) {
        $this->focusedFolder = new Folder($this->accountID, $folder);
    }

    /**
     * Populates the focused folder with messages from the database.
     */
    public function populateFocusedFolder() {
        global $log;
        if (isset($this->focusedFolder)) {
            $this->focusedFolder->getMessages();
        } else {
            $log->warning('Focused folder not yet populated in ', 
                [ 'File' => __FILE__, 'Line' => __LINE__ - 2 ]); // lol?
        }
    }
}