<?php
namespace Game\Mail;


use Game\Mail\Enums\Type;

class Folder {
    public $envelopes = [];
    public $folderType;
    public $accountID;

    /**
     * Constructs a new MailFolder instance.
     *
     * @param int $account_id The ID of the account associated with the folder.
     * @param \Game\Mail\Enums\Type $folder The type of the folder (default is INBOX).
     */
    public function __construct($account_id, Type $folder = Type::INBOX) {
        $this->accountID  = $account_id;
        $this->folderType = $folder;
    }

    /**
     * Get the count of messages in the current mail folder.
     *
     * @return int The number of messages in the folder.
     *
     */
    public function getMessageCount() {
        return count($this->envelopes);
    }

    /**
     * Retrieves messages from the database and populates the envelopes array.
     */
    public function getMessages() {
        global $db;
        $sql_query = <<<SQL
            SELECT * FROM {$_ENV['SQL_MAIL_TBL']} 
            WHERE
                account_id = ? AND
                folder = ?
        SQL;

        $characters = $db->execute_query($sql_query, [ $this->accountID, $this->folderType->name ])->fetch_all(MYSQLI_ASSOC);

        foreach ($characters as $row) {
            $envelope = new Envelope($row['sender'], $row['recipient']);
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