<?php
   class Monster {
        private $scope;
        private $seed;
        
        private $accountID;
        
        private $strength;
        private $intelligence;
        private $defense;
        
        private $monsterClass;
        
        /* 
            Cleaned up getter/setter handler from:
            https://stackoverflow.com/a/32191
        */
        function __call($function , $args) {
            global $log;
            list ($name , $var) = split ('_' , $function);
            
            if ($name == 'get' && isset($this->$var)) {
                return $this->$var;
            }
            
            if ($name == 'set' && isset($this->$var)) {
                $this->$var= $args[0];
                return;
            }

            $log->critical("Fatal error: Call to undefined method " . __CLASS__ . "::$function()");
        }
        
        public function __construct(MonsterScope $scope, $account_id = null) {
            $this->accountID = $account_id;
            $this->scope     = $scope;
        }
    }
}