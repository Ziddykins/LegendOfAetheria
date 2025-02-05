<?php
namespace Game\Mail\Enums;

enum Type {
        case INBOX;
        case DRAFTS;
        case OUTBOX;
        case DELETED;
    }