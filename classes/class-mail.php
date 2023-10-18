<?php
    class MailBox {
        public $accountID;
        public $focusedFolder;

        public function __construct($accountID) {
            $this->accountID = $accountID;
        }

        public function set_focused_folder(MailFolderType $folder) {
            $this->focusedFolder = new MailFolder($this->accountID, $folder);
        }

        public function populate_focused_folder() {
            global $log, $db;
            if (isset($this->focusedFolder)) {
                $this->focusedFolder->get_messages();
            } else {
                $log->warning('Focused folder not yet populated in ', 
                    [ 'File' => __FILE__, 'Line' => __LINE__ - 2 ]); // lol?
            }
        }
    }

    class MailFolder {
        public $envelopes = Array();
        public $folderType;
        public $accountID;

        public function __construct($account_id, MailFolderType $folder = MailFolderType::INBOX) {
            $this->accountID  = $account_id;
            $this->folderType = $folder;
        }

        public function get_message_count() {
            return count($this->envelopes);
        }

        public function get_messages() {
            global $db;
             $sql_query = 'SELECT * FROM ' . $_ENV['SQL_MAIL_TBL'] . 
                        '  WHERE account_id = ' . $this->accountID . 
                        '  AND folder = "' . $this->folderType->name . '"';
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

    enum MailFolderType {
        case INBOX;
        case DRAFTS;
        case OUTBOX;
        case DELETED;
    }
?>