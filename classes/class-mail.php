<?php
    class MailBox {
        private $accountID;
        private $focused_folder;

        public function __construct ($accountID) {
            $this->accountID = $accountID;
        }

        public function set_focused_folder(MailFolderType $folder) {
            $this->focused_folder = new MailFolder($folder);
        }

        public function populate_focused_folder() {
            if (isset($this->focused_folder)) {
                $this->focused_folder->get_messages();
            } else {
                // handle the situation when focused_folder is not set
            }
        }

    }

    class MailFolder {
        private $populatedFolder = Array();
        private $accountID;

        public function __construct($account_id) {
            $this->accountID = $account_id;
        }

        private function get_message_count() {
            return count($this->populatedFolder);
        }

        private function get_messages(MailFolderType $folder_type = MailFolderType::INBOX) {
             $sql_query = 'SELECT * FROM ' . $_ENV['TBL_MAIL_SQL'] . 
                        '  WHERE accountID = '. $this->accountID . 
                        '  AND folder = "'. $folder_type->name . '"';
                    
        }

    }

    class MailEnvelope {
        private $mail_id;
        private $sender;
        private $recipient;
        private $subject;
        private $message;
        private $folder;
        private $date;
        private $read;
        private $favorite;
        private $important;

        public function __construct ($sender, $recipient, $folder) {
            $this->sender = $sender;
            $this->recipient = $recipient;
        }

        public function set_message ($message) {
            $this->message = $message;
        }

        public function set_folder (MailFolderType $folder) {
            $this->folder = $folder;
        }
    }

    enum MailFolderType {
        case INBOX;
        case DRAFTS;
        case OUTBOX;
        case FAVORITE;
        case DELETED;
    }