<?php
    class Account {
        use HandlePropsAndCols;
        use HandlePropSync;

        private $id;
        private $email;
        public $password;
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
        private $banned;
        private $muted;
        private $loggedIn;

        private $charSlot1;
        private $charSlot2;
        private $charSlot3;

        private $inventory;

        private $focusedSlot;

        public function __construct($email) {
            global $log;
            $this->email = $email;

            $foundID = $this->checkIfExists($email);

            if (!$foundID) {
                $this->createAccount($email);
                $this->focusedSlot = 0;
            }

            $this->loadAccount($email);
        }

        public function __call($method, $params) {
            global $db, $log;

            if (!count($params)) {
                $params = null;
            }

            if ($method == 'prop_sync') {
                return;
            }

            return $this->prop_sync($method, $params, PropSyncType::ACCOUNT);
        }

        private function createAccount($email): int {
            global $db, $log;

            $new_id = $this->getNextId();
            $this->email = $email;
            
            $sql_query = "INSERT INTO {$_ENV['SQL_ACCT_TBL']} (`id`, `email`) VALUES (?, ?)";
            $db->execute_query($sql_query, [$new_id, $email]);

            return $new_id;
        }

        /**
         * Load account data from the database and populate the object properties.
         *
         * @param int $id The unique identifier of the account.
         * @return int Returns 0 if successful, otherwise it will exit the script.
         */
        private function loadAccount($email): int {
            global $db, $log;

            $sql_query = "SELECT * FROM {$_ENV['SQL_ACCT_TBL']} WHERE `email` = ?";
            $result = $db->execute_query($sql_query, [$email])->fetch_assoc();

            foreach ($result as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);
                $this->$key = $value;
            }
            $log->info("Load Character completed $email");
            return 0;
        }

        public static function checkIfExists($email) {
            global $db, $log;
            $sql_query = "SELECT `id` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `email` = ?";
            $result = $db->execute_query($sql_query, [$email])->fetch_assoc();

            if ($result && $result['id'] > 0) {
                return $result['id'];
            }

            return 0;
        }

        private function getNextID() {
            global $db;
            $sql_query = "SELECT IF(MAX(id) IS NULL, 1, MAX(id)+1) AS `next_id` FROM {$_ENV['SQL_ACCT_TBL']}";
            return $db->execute_query($sql_query)->fetch_assoc()['next_id'];
        }


    }
?>