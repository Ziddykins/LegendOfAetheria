<?php
    class Account {
        use HandlePropsAndCols;
        private $id;
        private $email;
        private $password;
        private $dateRegistered;
        private $verified;
        private $verificationCode;
        private $privileges;
        private $lastLogin;
        private $loggedOn;
        private $failedLogins;
        private $ipAddress;
        private $credits;
        private $sessionID;
        private $ipLock;
        private $ipLockAddr;

        private $characterSlots;


        public function __construct($accountID) {
            $this->id = $accountID;
            $this->load_account($accountID);
        }

        function __call($method, $params) {
            global $db;
            $var = lcfirst(substr($method, 4));

            if (strncasecmp($method, "get_", 4) === 0) {
                return $this->$var;
            }

            if (strncasecmp($method, "set_", 4) === 0) {
                $this->$var = $params[0];
                $table_column = $this->clsprop_to_tblcol($var);
                $query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET  `$table_column` = ? WHERE `id` = ?";
                $db->execute_query($query, [$params[0], $this->id]);
            }
        }

        /**
         * Load account data from the database and populate the object properties.
         *
         * @param int $id The unique identifier of the account.
         * @return int Returns 0 if successful, otherwise it will exit the script.
         */
        private function load_account($id) {
            global $db, $log;
            $query = "SELECT * FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";

            $result = $db->execute_query($query, [$id])->fetch_assoc();

            /*if (!$result) {
                header('Location: /');
                exit();
            }*/

            foreach ($result as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);
                $this->$key = $value;
            }

            return 0;
        }
    }

    class CharacterSlots {
        /* TODO: implement. */
    }
?>