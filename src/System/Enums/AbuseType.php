<?php
namespace Game\System\Enums;

enum AbuseType {
    case CHEATING;    /* General cheating/abuse of game mechanics etc. */
    case AUTOBOTTING; /* Using autoclickers to play for you */
    case MULTISIGNUP; /* Abusing the signup form/multi-characters */
    case TAMPERING;   /* Modifying POST requests */
}