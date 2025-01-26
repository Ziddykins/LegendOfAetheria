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

        private $focusedSlot;

        public function __construct($email = null) {
            $this->email = $email;
        }
        
        public function __call($method, $params) {
            global $db, $log;

            /* If it's a get, this is true */
            if (!count($params)) {
                $params = null;
            }

            /* Avoid loops with prop_sync triggering itself */
            if ($method == 'prop_sync') {
                return;
            }

            return $this->prop_sync($method, $params, PropSyncType::ACCOUNT);
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
            $sql_query = "SELECT IF(MAX(`id`) IS NULL, 1, MAX(`id`)+1) AS `next_id` FROM {$_ENV['SQL_ACCT_TBL']}";
            return $db->execute_query($sql_query)->fetch_assoc()['next_id'];
        }

        private function getConstructor() {
            return 'email';
        }
    }
?>