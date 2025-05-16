<?php
    namespace Game\Bank;
    use Game\Bank\Enums\BankBracket;
    use Game\Traits\PropManager\PropManager;
    use Game\Traits\PropManager\Enums\PropType;

    class BankManager {
        use PropManager;
        private int $id;
        private int $accountID;
        private int $characterID;
        private float $gold;
        private float $interestRate; // Daily interest rate, gained based on amount of gold
        private float $dpr; // Daily Percentage Rate for the current loan
        private int $spindels;
        private float $loan;
        private BankBracket $bracket; // Brackets determine interest rates, transfer limits and loan amounts
        private float $transferLimit;

        public function __construct(int $accountID, int $characterID) {
            if ($characterID) {
                $this->characterID = $characterID;
                $id = $this->checkIfExists('character_id', $characterID, $_ENV['SQL_BANK_TBL']);

                if ($id > 0) {
                    $this->id = $id;
                    $this->load($id);
                }
            }
        }

        public function __call($method, $params) {
            global $db, $log;
    
            /* If it's a get, this is true */
            if (!count($params)) {
                $params = null;
            }
    
            /* Avoid loops with propSync triggering itself */
            if ($method == 'propSync' || $method == 'propMod') {
                $log->debug("$method loop");
                return;
            }
    
            if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
                return $this->propMod($method, $params);
            } else {
                return $this->propSync($method, $params, PropType::BANKMANAGER);
            }
        }
    }
?>