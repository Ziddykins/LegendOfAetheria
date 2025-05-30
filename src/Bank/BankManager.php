<?php
    namespace Game\Bank;
    use Game\Bank\Enums\BankBracket;
    use Game\Traits\PropSuite\PropSuite;
    use Game\Traits\PropSuite\Enums\PropType;

    class BankManager {
        use PropSuite;
        private int $id;
        private int $accountID;
        private int $characterID;
        private float $goldAmount;
        private float $interestRate; // Daily interest rate, gained based on amount of gold
        private float $dpr; // Daily Percentage Rate for the current loan
        private int $spindels;
        private float $loan;
        private BankBracket $bracket; // Brackets determine interest rates, transfer limits and loan amounts
        private float $transferLimit;

        public function __construct(int $accountID, int $characterID) {
            $this->accountID = $accountID;
            $this->characterID = $characterID;

            if ($this->characterID) {
                $this->id = $characterID;
                $this->load($this->id);
            }

            $this->goldAmount = 0;
            $this->interestRate = 0.25;
            $this->dpr = 25.0;
            $this->spindels = 0;
            $this->loan = 0;
            $this->bracket = BankBracket::STANDARD;
            $this->transferLimit = 5000;
            $this->load();
        }

        public function __call($method, $params) {
            global $db, $log;
    
            if (!count($params)) {
                $params = null;
            }

            if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
                return $this->propMod($method, $params);
            } elseif (preg_match('/^(dump|restore)$/', $method, $matches)) {
                $func = $matches[1];
                return $this->$func($params[0] ?? null);
            } else {
                return $this->propSync($method, $params, PropType::BANKMANAGER);
            }
        }
    }
?>