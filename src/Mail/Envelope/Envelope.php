<?php
namespace Game\Mail\Envelope;
use Game\Mail\Envelope\Enums\EnvelopeStatus;
use Game\Mail\Folder\Enums\FolderType;

/**
 * Class Envelope
 *
 * Represents an email envelope containing details such as sender, recipient,
 * subject, message, folder information, and status.
 *
 * @package Game\Mail\Envelope
 */
class Envelope {
    public int $mail_id;
    public string $sender;
    public string $recipient;
    public string $subject;
    public string $message;
    public $date;
    public FolderType $folder;
    public string $status;

    /**
     * Constructs a new Envelope instance with the specified sender and recipient.
     *
     * @param string $sender The email address of the sender.
     * @param string $recipient The email address of the recipient.
     */
    public function __construct($sender, $recipient) {
        $this->sender    = $sender;
        $this->recipient = $recipient;
    }

    public static function get_statuses(int $value) {
        $statuses = [];

        if ($value & EnvelopeStatus::FAVORITE->value) {
            $statuses['favorite'] = 1;
        }

        if ($value & EnvelopeStatus::IMPORTANT->value) {
            $statuses['important'] = 1;
        }

        if ($value & EnvelopeStatus::REPLIED->value) {
            $statuses['replied'] = 1;
        }
    }
}