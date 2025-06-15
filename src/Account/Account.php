<?php
namespace Game\Account;

use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Account\Enums\Privileges;

/**
 * @method load(int $account_id)
 */
class Account {
    use PropSuite;
    
    private ?int $id = null;
    private string $email = '';
    private string $password = '';
    private string $dateRegistered = '';
    private bool $verified = false;
    private string $verificationCode = '';
    private ?Privileges $privileges = null;
    private string $lastLogin = '';
    private string $loggedOn = '';
    private int $failedLogins = 0;
    private string $ipAddress = '';
    private int $credits = 0;
    private string $sessionID = '';
    private string $ipLock = '';
    private string $ipLockAddr = '';
    private bool $banned = false;
    private bool $muted = false;
    private bool $loggedIn = false;
    private int $eggsOwned = 0;
    private int $eggsSeen = 0;
    private string $jwtSecret = '';

    private ?Settings $settings = null;

    private ?int $charSlot1 = null;
    private ?int $charSlot2 = null;
    private ?int $charSlot3 = null;

    private ?int $focusedSlot = null;

    public function __construct($email = null) {
        if ($email) {
            $this->email = $email;
            $id = self::checkIfExists($email);
            
            if ($id > 0) {
                $this->id = $id;
                $this->load($id);
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
        }

        if (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        }

        return $this->propSync($method, $params, PropType::ACCOUNT);
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