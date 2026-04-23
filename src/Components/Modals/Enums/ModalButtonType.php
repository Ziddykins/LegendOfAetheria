<?php
    namespace Game\Components\Modals\Enums;

    /**
     * Defines button configurations for modal dialogs.
     * Determines which action buttons appear at the bottom of modal windows.
     */
    enum ModalButtonType {
        /** Modal with Yes/No buttons for confirmation dialogs */
        case YESNO;
        
        /** Modal with OK/Cancel buttons for action confirmation */
        case OKCANCEL;
        
        /** Modal with only a Close button for information display */
        case CLOSE;
    }