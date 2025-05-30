<?php
namespace Game\Account;

use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Account\Enums\Privileges;

class Account {
    use PropSuite;
    
    private int $id;
    private string $email;
    private string $password;
    private string $dateRegistered;
    private bool $verified;
    private string $verificationCode;
    private Privileges $privileges;
    private string $lastLogin;
    private string $loggedOn;
    private int $failedLogins;
    private string $ipAddress;
    private int $credits;
    private string $sessionID;
    private string $ipLock;
    private string $ipLockAddr;
    private bool $banned;
    private bool $muted;
    private bool $loggedIn;
    private int $eggsOwned;
    private int $eggsSeen;
    private string $jwtSecret;

    private Settings $settings;

    private int $charSlot1;
    private int $charSlot2;
    private int $charSlot3;

    private int $focusedSlot;

    public function __construct($email = null) {
        if ($email) {
            $this->email = $email;
            $id = $this->checkIfExists($email);
            
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

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(dump|restore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::ACCOUNT);
        }
    }
    
    public static function checkIfExists($email): int {
        global $db, $log, $t;
        $sqlQuery = "SELECT `id` FROM {$t['accounts']} WHERE `email` = ?";
        $result = $db->execute_query($sqlQuery, [$email])->fetch_assoc();

        if ($result && $result['id'] > 0) {
            return $result['id'];
        }

        return -1;
    }
}