<?php
namespace Game\Account;

use Game\Traits\PropManager\PropManager;

use Game\Traits\PropManager\Enums\PropType;

class Account {
    use PropManager;
    
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
    private $banned;
    private $muted;
    private $loggedIn;
    private $eggsOwned;
    private $eggsSeen;

    private $settings;

    private $charSlot1;
    private $charSlot2;
    private $charSlot3;

    private $focusedSlot;

    public function __construct($email = null) {
        if ($email) {
            $this->email = $email;
            $id = $this->checkIfExists($email);
            
            if ($id) {
                $this->id = $id;
                $this->load();
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
            return $this->propSync($method, $params, PropType::ACCOUNT);
        }
    }
    
    public static function checkIfExists($email): int {
        global $db, $log;
        $sqlQuery = "SELECT `id` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `email` = ?";
        $result = $db->execute_query($sqlQuery, [$email])->fetch_assoc();

        if ($result && $result['id'] > 0) {
            return $result['id'];
        }

        return -1;
    }
}