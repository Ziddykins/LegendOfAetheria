<?php
namespace Game\Mail;

class Envelope {
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

    public function __construct($sender, $recipient) {
        $this->sender    = $sender;
        $this->recipient = $recipient;
    }
}