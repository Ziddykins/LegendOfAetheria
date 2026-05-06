<?php
    namespace Game\Bank;
    use Game\Bank\Enums\BankBracket;
    use Game\Traits\PropSuite\PropSuite;
    use Game\Traits\PropSuite\Enums\PropType;

    /**
     * BankManager handles in-game banking operations and account management.
     * 
     * Manages gold deposits, loans, interest rates, and banking tier brackets.
     * Uses PropSuite trait for dynamic property management and database synchronization.
     * 
     * @package Game\Bank
     * 
     * @method mixed load_(int $id) Loads bank data from database
     * @method mixed new_() Creates new bank record in database
     * 
     * @method int|null get_id() Gets the bank account ID
     * @method int|null get_accountID() Gets the associated account ID
     * @method int|null get_characterID() Gets the associated character ID
     * @method float get_goldAmount() Gets the gold balance
     * @method float get_interestRate() Gets the interest rate
     * @method float get_dpr() Gets the daily percentage rate
     * @method int get_spindels() Gets spindel count
     * @method float get_loan() Gets the outstanding loan amount
     * @method BankBracket|null get_bracket() Gets the banking tier bracket
     * @method float get_transferLimit() Gets the transfer limit
     * 
     * @method void set_goldAmount(float $amount) Sets the gold balance
     * @method void set_interestRate(float $rate) Sets the interest rate
     * @method void set_dpr(float $rate) Sets the daily percentage rate
     * @method void set_spindels(int $count) Sets spindel count
     * @method void set_loan(float $amount) Sets the loan amount
     * @method void set_bracket(BankBracket $bracket) Sets the banking tier
     * @method void set_transferLimit(float $limit) Sets the transfer limit
     * 
     * @method void add_goldAmount(float $amount) Adds gold to balance
     * @method void sub_goldAmount(float $amount) Subtracts gold from balance
     * @method void add_loan(float $amount) Increases loan amount
     * @method void sub_loan(float $amount) Decreases loan amount
     * @method void add_spindels(int $count) Adds spindels
     * @method void sub_spindels(int $count) Removes spindels
     */
    class BankManager {
        use PropSuite;
        
        /** @var int|null Bank account unique identifier */
        private ?int $id = null;
        
        /** @var int|null Associated account ID */
        private ?int $accountID = null;
        
        /** @var int|null Associated character ID */
        private ?int $characterID = null;
        
        /** @var float Gold balance in bank */
        private float $goldAmount = 0.00;
        
        /** @var float Interest rate applied to deposits */
        private float $interestRate = 0.025;
        
        /** @var float Daily percentage rate */
        private float $dpr = 0.25;
        
        /** @var int Spindel currency count */
        private int $spindels = 0;
        
        /** @var float Outstanding loan amount */
        private float $loan = 0.00;
        
        /** @var BankBracket|null Banking tier level */
        private ?BankBracket $bracket = null;
        
        /** @var float Maximum amount that can be transferred */
        private float $transferLimit = 5000.0;

        /**
         * Constructs a new BankManager instance.
         * 
         * If character ID is provided, checks if bank account exists and loads it,
         * otherwise prepares for new bank account creation.
         * 
         * @param int $accountID Account ID to associate bank with
         * @param int|null $characterID Optional character ID to associate
         */
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

        /**
         * Magic method for dynamic property access and modification.
         * 
         * Handles get/set operations, mathematical operations (add, sub, mul, div, exp, mod),
         * and property dump/restore operations via PropSuite trait.
         * 
         * @param string $method Method name to invoke
         * @param array $params Parameters for the method
         * @return mixed Result of the invoked method
         */
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

        /**
         * Checks if a bank account exists for the given character.
         * 
         * @param int $characterID Character ID to check
         * @return int Bank account ID if exists, -1 if not found
         */
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