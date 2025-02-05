<?php
namespace Game\Monster\Enums;

enum Specialty {
    case GIANT;       /* 2-3x HP */
    case MINI;        /* 1/2 - 1/4 HP */
    case HARDENED;    /* 3-4x DEF */
    case ENRAGED;     /* 4x STR, -5% HP */
    case EXPERIENCED; /* 3x EXP, +2% HP+DEF */
    case PACKRAT;     /* Item rarity chances increased/1.5x, 3x gold */
    case DEFECTING;   /* Chance to spare life/let join your army */
    case LEADER;      /* 5-10x all stats */
}