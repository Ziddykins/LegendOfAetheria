<?php
    namespace Game\Bank;
    use Game\Bank\Enums\BankBracket;
    use Game\Traits\PropSuite\PropSuite;
    use Game\Traits\PropSuite\Enums\PropType;

    class BankManager {
        use PropSuite;
        private ?int $id = null;
        private ?int $accountID = null;
        private ?int $characterID = null;
        private float $goldAmount = 0.00;
        private float $interestRate = 0.025;
        private float $dpr = 0.25;
        private int $spindels = 0;
        private float $loan = 0.00;
        private ?BankBracket $bracket = null;
        private float $transferLimit = 5000.0                        ;

        public function __construct(int $accountID, ?int $characterID) {
            $this->accountID = $accountID;

            if ($characterID) {
                $this->characterID = $characterID;
                $id = $this->checkIfExists($characterID);
            
                if ($id > 0) {
                    $this->id = $id;
                    $this->load($id);
                } else {
                    $this->id = getNextTableID('tbl_bank');
                }
            }
        }

        public function __call($method, $params) {
            global $db, $log;
    
            if (!count($params)) {
                $params = null;
            }

            if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
                return $this->propMod($method, $params);
            } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
                $func = $matches[1];
                return $this->$func($params[0] ?? null);
            } else {
                return $this->propSync($method, $params, PropType::BANKMANAGER);
            }
        }   

        public static function checkIfExists($characterID): int {
            global $db, $log, $t;
            $sqlQuery = "SELECT `id` FROM {$t['bank']} WHERE `character_id` = ?";
            $result = $db->execute_query($sqlQuery, [ $characterID ])->fetch_assoc();

            if ($result && $result['id'] > 0) {
                return $result['id'];
            }

            return -1;
        }
    }
?>