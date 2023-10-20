<?php
    class Inventory {
        protected $accountID;
        protected $currentWeight;
        protected $maximumWeight;
        protected $totalSlots;
        
        public function __construct($playerID) {
            $this->accountID = $accountID;
            $this->currentWeight = 0;
            $this->maximumWeight = 100;
            $this->totalSlots = 25;
        }
    }
?>
