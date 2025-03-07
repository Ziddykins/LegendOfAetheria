<?php
namespace Game\Mail\Folder;

use Game\Mail\Folder\Enums\FolderType;
use Game\Mail\Envelope\Envelope;
use Game\Mail\Envelope\Enums\EnvelopeStatus;

class Folder {
    public $envelopes = [];
    public $folderType;
    public $accountID;

    /**
     * Constructs a new Folder instance using the provided account ID and folder type.
     *
     * @param int $account_id The unique identifier for the account associated with the folder.
     * @param FolderType $folder The folder type to be used. Defaults to FolderType::INBOX.
     */
    public function __construct($account_id, FolderType $folder = FolderType::INBOX) {
        $this->accountID  = $account_id;
        $this->folderType = $folder;
    }

    /**
     * Returns the total number of messages stored in the folder.
     *
     * @return int The number of messages in the folder.
     */
    public function getMessageCount() {
        return count($this->envelopes);
    }

    /**
     * Retrieves messages from the database based on the account ID and folder type,
     * instantiates Envelope objects for each record, and populates the envelopes array.
     */
    public function getMessages() {
        global $db;
        $sql_query = <<<SQL
            SELECT * FROM {$_ENV['SQL_MAIL_TBL']} 
            WHERE
                account_id = ? AND
                folder = FIND_IN_SET(?, folder)
        SQL;

        $characters = $db->execute_query($sql_query, [ $this->accountID, $this->folderType->name ])->fetch_all(MYSQLI_ASSOC);

        foreach ($characters as $row) {
            $envelope = new Envelope($row['from'], $row['to']);
            $envelope->mail_id    = $row['id'];
            $envelope->sender     = $row['from'];
            $envelope->recipient  = $row['to'];
            $envelope->subject    = $row['subject'];
            $envelope->message    = $row['message'];
            $envelope->folder     = FolderType::name_to_value($row['folder']);
            $envelope->date       = $row['date'];
            $envelope->status     = $row['status'];

            array_push($this->envelopes, $envelope);
        }
    }
}