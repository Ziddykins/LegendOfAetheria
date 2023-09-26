<?php
    function get_user_folder($account_id, $folder = MailFolderType::INBOX) {
        $user_mailbox = new MailBox($account_id);
        $user_folder  = $user_mailbox->get_folder($folder);
        
    }
    
    class MailBox {
        private $account_id;
        private $focused_folder;

        public function __construct ($account_id) {
            $this->account_id = $account_id;
        }

        public function set_focused_folder(MailFolderType $folder) {
            $this->focused_folder = new MailFolder($folder);
        }

        public function populate_focused_folder() {
            $this->focused_folder->get_messages();
        }

    }
    

    class MailFolder {
        private $populated_folder;

        public function __construct ($account_id) {
        
        }
        
        private function get_message_count($folder) {
            
        }

        private function get_messages($folder) {
             // ye
        }

    }

    class MailEnvelope {
        private $mail_id;
        private $sender;
        private $recipient;
        private $subject;
        private $body;
        private $folder;
        private $date;
        private $read;
        private $favorite;

        public function __construct ($sender, $recipient) {
            $this->sender = $sender;
            $this->recipient = $recipient;
        }

        public function set_body ($body) {
            $this->body = $body;
        }

        public function set_folder (MailFolderType $folder) {
            $this->folder = $folder;
        }
    }

    enum MailFolderType {
        case INBOX;
        case DRAFTS;
        case OUTBOX;
        case FAVORITES;
        case DELETED;
    }