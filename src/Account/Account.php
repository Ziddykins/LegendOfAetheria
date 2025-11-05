<?php
namespace Game\Account;

use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Account\Enums\Privileges;

/**
 * Account class represents a user account in the Legend of Aetheria game.
 * 
 * Manages user authentication, session data, character slots, and account privileges.
 * Uses PropSuite trait for dynamic property management and database synchronization.
 * 
 * @package Game\Account
 * 
 * @method mixed load_(int $account_id) Loads account data from database
 * @method mixed new_() Creates new account in database
 * 
 * @method int|null get_id() Gets the account ID
 * @method string get_email() Gets the account email address
 * @method string get_password() Gets the hashed password
 * @method string get_dateRegistered() Gets registration date
 * @method bool get_verified() Gets email verification status
 * @method string get_verificationCode() Gets email verification code
 * @method Privileges|null get_privileges() Gets account privilege level
 * @method string get_lastLogin() Gets last login timestamp
 * @method string get_loggedOn() Gets current session login timestamp
 * @method int get_failedLogins() Gets failed login attempt count
 * @method string get_ipAddress() Gets current IP address
 * @method int get_credits() Gets premium credits balance
 * @method string get_sessionID() Gets current session ID
 * @method string get_ipLock() Gets IP lock status
 * @method string get_ipLockAddr() Gets IP lock address
 * @method bool get_banned() Gets account ban status
 * @method bool get_muted() Gets account mute status
 * @method bool get_loggedIn() Gets logged in status
 * @method int get_eggsOwned() Gets number of eggs owned
 * @method int get_eggsSeen() Gets number of unique eggs seen
 * @method string get_jwtSecret() Gets JWT authentication secret
 * @method Settings|null get_settings() Gets account settings object
 * @method int|null get_charSlot1() Gets character slot 1 ID
 * @method int|null get_charSlot2() Gets character slot 2 ID
 * @method int|null get_charSlot3() Gets character slot 3 ID
 * @method int|null get_focusedSlot() Gets currently focused character slot
 * 
 * @method void set_email(string $email) Sets account email address
 * @method void set_password(string $password) Sets hashed password
 * @method void set_verified(bool $verified) Sets email verification status
 * @method void set_verificationCode(string $code) Sets email verification code
 * @method void set_privileges(Privileges $privileges) Sets account privilege level
 * @method void set_lastLogin(string $datetime) Sets last login timestamp
 * @method void set_loggedOn(string $datetime) Sets current login timestamp
 * @method void set_failedLogins(int $count) Sets failed login attempt count
 * @method void set_ipAddress(string $ip) Sets IP address
 * @method void set_sessionID(string $session) Sets session ID
 * @method void set_ipLock(string $status) Sets IP lock status
 * @method void set_ipLockAddr(string $address) Sets IP lock address
 * @method void set_banned(bool $banned) Sets account ban status
 * @method void set_muted(bool $muted) Sets account mute status
 * @method void set_loggedIn(bool $loggedIn) Sets logged in status
 * @method void set_jwtSecret(string $secret) Sets JWT authentication secret
 * @method void set_settings(Settings $settings) Sets account settings object
 * @method void set_charSlot1(int $characterId) Sets character slot 1 ID
 * @method void set_charSlot2(int $characterId) Sets character slot 2 ID
 * @method void set_charSlot3(int $characterId) Sets character slot 3 ID
 * @method void set_focusedSlot(int $slot) Sets focused character slot
 * 
 * @method void add_credits(int $amount) Adds credits to account balance
 * @method void sub_credits(int $amount) Subtracts credits from account balance
 * @method void add_failedLogins(int $count) Increments failed login counter
 * @method void add_eggsOwned(int $count) Increments eggs owned counter
 * @method void add_eggsSeen(int $count) Increments eggs seen counter
 */
class Account {
    use PropSuite;
    
    /** @var int|null Account unique identifier */
    private ?int $id = null;
    
    /** @var string Account email address */
    private string $email = '';
    
    /** @var string Hashed password */
    private string $password = '';
    
    /** @var string Account registration date */
    private string $dateRegistered = '';
    
    /** @var bool Email verification status */
    private bool $verified = false;
    
    /** @var string Email verification code */
    private string $verificationCode = '';
    
    /** @var Privileges|null Account privilege level */
    private ?Privileges $privileges = null;
    
    /** @var string Last login timestamp */
    private string $lastLogin = '';
    
    /** @var string Current session login timestamp */
    private string $loggedOn = '';
    
    /** @var int Number of consecutive failed login attempts */
    private int $failedLogins = 0;
    
    /** @var string Current IP address */
    private string $ipAddress = '';
    
    /** @var int Premium credits balance */
    private int $credits = 0;
    
    /** @var string Current session ID */
    private string $sessionID = '';
    
    /** @var string IP lock status */
    private string $ipLock = '';
    
    /** @var string IP lock address */
    private string $ipLockAddr = '';
    
    /** @var bool Account ban status */
    private bool $banned = false;
    
    /** @var bool Account mute status */
    private bool $muted = false;
    
    /** @var bool Current logged in status */
    private bool $loggedIn = false;
    
    /** @var int Number of eggs owned */
    private int $eggsOwned = 0;
    
    /** @var int Number of unique eggs seen */
    private int $eggsSeen = 0;
    
    /** @var string JWT secret for authentication */
    private string $jwtSecret = '';

    /** @var Settings|null Account settings object */
    private ?Settings $settings = null;

    /** @var int|null Character slot 1 ID */
    private ?int $charSlot1 = null;
    
    /** @var int|null Character slot 2 ID */
    private ?int $charSlot2 = null;
    
    /** @var int|null Character slot 3 ID */
    private ?int $charSlot3 = null;

    /** @var int|null Currently focused character slot */
    private ?int $focusedSlot = null;

    /**
     * Constructs a new Account instance.
     * 
     * If an email is provided, checks if the account exists and loads its data.
     * 
     * @param string|null $email Optional email address to load existing account
     */
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
        }

        if (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        }

        return $this->propSync($method, $params, PropType::ACCOUNT);
    }
    
    /**
     * Checks if an account exists with the given email address.
     * 
     * @param string $email Email address to check
     * @return int Account ID if exists, -1 if not found
     */
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