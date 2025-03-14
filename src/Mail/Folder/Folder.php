<?php
namespace Game\Mail\Folder;

use Game\Mail\Folder\Enums\FolderType;
use Game\Mail\Envelope\Envelope;
use Game\Mail\Envelope\Enums\EnvelopeStatus;

class Folder {
    public $envelopes = [];
    public $folderType;
    public $characterID;

    /**
     * Constructs a new Folder instance using the provided account ID and folder type.
     *
     * @param int $character_id The unique identifier for the account associated with the folder.
     * @param FolderType $folder The folder type to be used. Defaults to FolderType:: BOX.
     */
    public function __construct($character_id, FolderType $folder = FolderType::INBOX) {
        $this->characterID  = $character_id;
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
        $clause = null;

        switch($this->folderType) {
            case FolderType::INBOX:
            case FolderType::DELETED:
                $clause = "r_aid = ? AND r_cid = ?";
                break;
            case FolderType::OUTBOX:
            case FolderType::DRAFTS:
                $clause = "s_aid = ? AND s_cid = ?";
                break;
            default:
                $clause = "`to` = ?";
        };

        $sql_query = <<<SQL
            SELECT * FROM {$_ENV['SQL_MAIL_TBL']} 
            WHERE
                $clause AND
                `folder` = FIND_IN_SET(?, folder)
            ORDER BY `status`,`date` ASC
        SQL;

        $envelopes = $db->execute_query($sql_query, [ $_SESSION['account-id'], $_SESSION['character-id'], $this->folderType->name ])->fetch_all(MYSQLI_ASSOC);

        foreach ($envelopes as $row) {
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

    public function getFolderHTML() {
        $html = null;
        $box_count = $this->getMessageCount();


        for ($i=$box_count - 1; $i>=0; $i--) {
            $subject    = $this->envelopes[$i]->subject;
            $sender     = $this->envelopes[$i]->sender;
            $msg_frag   = $this->envelopes[$i]->message;
            $date       = $this->envelopes[$i]->date;
            $flagstring = $this->envelopes[$i]->status;
            
            $status_int = EnvelopeStatus::value_from_flagstring($flagstring);
            $read       = $status_int & EnvelopeStatus::READ->value;


            $status_line = EnvelopeStatus::get_status_line($flagstring);

            $html .= '<div class="list-group">';
            $html .= "    <a href=\"#\" id=\"env-id-$i\" class=\"list-group-item list-group-item-action mb-1 text-truncate ";

            if ($i == 0) {
                $html .= 'active';
            }

            if (!$read) {
                $html .= ' text-bg-tertiary bg-gradient';
            }

            $html .= '" aria-current="true">';
            $html .= '        <div class="d-flex w-100 justify-content-between">';
            $html .= '            <h6 id="env-sub-' . $i . '" class="mb-1">' . $subject . '</h6>';
            $html .= '            <small id="env-date-' . $i . '">' . $date . '</small>';
            $html .= '        </div>';
            $html .= '        <div class="d-flex w-100 justify-content-between">';
            $html .= '            <span id="env-from-' . $i . '" class="mb-1">' . $sender . '</span>';
            $html .= "            $status_line";
            $html .= '        </div>';
            $html .= '        <small id="env-frag-' . $i . '" class="col text-truncate">' . $msg_frag . '</small>';
            $html .= '   </a>';
            $html .= '</div>';
            $html .= '';
        }

        return $html;
    }
}