<?php
    class Inventory {
        protected $accountID;
        protected $current_weight;
        protected $maximum_weight;
        protected $slots;
        
        public function __construct($playerID) {
            $this->accountID = $accountID;
            $this->current_weight = 0;
            $this->maximum_weight = 100;
            $this->slots = 25;
        }
    }
?>
