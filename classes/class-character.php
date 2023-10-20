<?php  
    class Character {
        protected $accountID;
        protected $charName;
        
        public function __construct($playerID) {
            $this->accountID = $accountID;
        }
        
        protected function set_name($name) {
            $this->charName = $name;
        }
        
        protected function get_name() {
            return $this->charName;
        }
    }
?>