<?php

const MAX_STARTING_INVWEIGHT = 500;
const MAX_STARTING_INVSLOTS = 30;
const MAX_ASSIGNABLE_AP = 40;
const REGEN_PER_TICK = 3;


enum ModalButtonType {
    case YesNo;
    case Close;
    case OKCancel;
}

enum Components {
    case FLOATING_LABEL_TEXTBOX;
}