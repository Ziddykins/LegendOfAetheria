<?php
    namespace Game\Components\Enums;

    enum ComponentType {
        case FLOATING_LABEL_TEXTBOX;
        case OFFCANVAS_CHAT;
        case CHARACTER_SELECT;
        case MODAL;
        case SIDEBAR;
        case STATS;
    }