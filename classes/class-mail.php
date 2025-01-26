<?php
    /**
     * Represents a mailbox for a specific account.
     */
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
         * @param MailFolderType $folder The type of the folder to set as focused.
         */
        public function setFocusedFolder(MailFolderType $folder) {
            $this->focusedFolder = new MailFolder($this->accountID, $folder);
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

    /**
     * Represents a folder within a mailbox.
     */
    class MailFolder {
        public $envelopes = Array();
        public $folderType;
        public $accountID;

        /**
         * Constructs a new MailFolder instance.
         *
         * @param int $account_id The ID of the account associated with the folder.
         * @param MailFolderType $folder The type of the folder (default is INBOX).
         */
        public function __construct($account_id, MailFolderType $folder = MailFolderType::INBOX) {
            $this->accountID  = $account_id;
            $this->folderType = $folder;
        }

        /**
         * Get the count of messages in the current mail folder.
         *
         * @return int The number of messages in the folder.
         *
         * @throws Exception If there is an error while counting the messages.
         */
        public function getMessageCount() {
            return count($this->envelopes);
        }

        /**
         * Retrieves messages from the database and populates the envelopes array.
         */
        public function getMessages() {
            global $db;
            $sql_query = 'SELECT * FROM '. $_ENV['SQL_MAIL_TBL']. 
                        'WHERE account_id = '. $this->accountID. 
                        'AND folder = "'. $this->folderType->name. '"';
            $results = $db->query($sql_query);

            while ($row = $results->fetch_assoc()) {
                $envelope = new MailEnvelope($row['sender'], $row['recipient']);
                $envelope->mail_id    = $row['id'];
                $envelope->sender     = $row['sender'];
                $envelope->recipient  = $row['recipient'];
                $envelope->subject    = $row['subject'];
                $envelope->message    = $row['message'];
                $envelope->folder     = $row['folder'];
                $envelope->date       = $row['date'];
                $envelope->read       = $row['read'];
                $envelope->favorite   = $row['favorite'];
                $envelope->important  = $row['important'];
                $envelope->replied_to = $row['replied_to'];
                array_push($this->envelopes, $envelope);
            }
        }
    }

    /**
     * Represents an email message envelope.
     */
    class MailEnvelope {
        public $mail_id;
        public $sender;
        public $recipient;
        public $subject;
        public $message;
        public $folder;
        public $date;
        public $read;
        public $favorite;
        public $important;
        public $replied_to;
    }

    /**
     * Represents the type of a mail folder.
     */
    enum MailFolderType {
        case INBOX;
        case DRAFTS;
        case OUTBOX;
        case DELETED;
    }
