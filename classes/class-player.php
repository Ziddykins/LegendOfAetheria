<?php
    class Player {
        private $accountID;
        
        public function __construct($playerID) {
            $this->accountID = $accountID;
        }
    }
    
    class Inventory {
        protected $accountID;
        
        public function __construct($playerID) {
            $this->accountID = $accountID;
        }
    }
    
    class Familiar {
        protected $accountID;
        protected $name;
        
        public function __construct($playerID) {
            $this->accountID = $accountID;
        }
        
        protected function set_name($name) {
            $this->name = $name;
        }
        
        protected function get_name() {
            return $this->name;
        }
    }
}