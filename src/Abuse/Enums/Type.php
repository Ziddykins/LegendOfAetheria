<?php
namespace Game\Abuse\Enums;

enum Type {
    case CHEATING;    /* General cheating/abuse of game mechanics etc. */
    case AUTOBOTTING; /* Using autoclickers to play for you */
    case MULTISIGNUP; /* Abusing the signup form/multi-characters */
    case POSTMODIFY;  /* Modifying POST requests */
}