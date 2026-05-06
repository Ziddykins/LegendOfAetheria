<?php
namespace Game\System\Enums;

/**
 * Defines categories of rule violations and abusive behavior.
 * Used for moderation, logging, and enforcement actions.
 */
enum AbuseType {
    /** General cheating or exploiting game mechanics */
    case CHEATING;
    
    /** Using automated tools/bots to play the game */
    case AUTOBOTTING;
    
    /** Creating multiple accounts to abuse signup rewards */
    case MULTISIGNUP;
    
    /** Manipulating POST requests or client-side data */
    case TAMPERING;
    
    /** Inappropriate behavior in chat system */
    case CHATABUSE;
}