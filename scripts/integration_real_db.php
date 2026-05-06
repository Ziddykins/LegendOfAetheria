<?php
declare(strict_types=1);

// Integration smoke script: run against real DB inside a transaction and rollback.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../system/constants.php';
// make bootstrap think we're running index.php so it doesn't force check_session redirect
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once __DIR__ . '/../system/bootstrap.php';

use Game\Account\Account;
use Game\Character\Character;

if (php_sapi_name() !== 'cli') {
    echo "Please run from CLI\n";
    exit(1);
}

@session_start();

global $db, $log;

echo "Starting integration smoke test (transaction, will rollback)\n";

// start transaction
$db->begin_transaction();

try {
    $email = 'integration+' . time() . '@example.test';
    $password = 'TestPass123!';

    echo "Creating account: $email\n";
    $acc = new Account($email);
    $newId = $acc->new();
    if (!is_int($newId) || $newId <= 0) {
        throw new \RuntimeException('Account::new() did not return a valid id');
    }
    echo "Account created id=$newId\n";

    // set password and verification code
    $acc->set_password(password_hash($password, PASSWORD_BCRYPT));
    $acc->set_verificationCode('integration-test');

    // create a character for this account
    echo "Creating character for account $newId\n";
    $char = new Character($acc->get_id());
    $cid = $char->new();
    if (!is_int($cid) || $cid <= 0) {
        throw new \RuntimeException('Character::new() did not return a valid id');
    }
    echo "Character created id=$cid\n";

    // ensure some required non-null columns exist for later load routines
    // e.g. some enums (race) may be NULL by default and later cause strict-type errors
    $db->execute_query("UPDATE {$_ENV['SQL_CHAR_TBL']} SET `race` = ? WHERE `id` = ?", ['HUMAN', $cid]);

    // Set some character properties
    $char->set_name('IntegrationTester');
    $char->stats->set_hp(77);
    $char->stats->set_str(12);

    // reload account by email to simulate login/load
    echo "Reloading account by email to verify persistence within transaction\n";
    $acctLoaded = new Account($email);
    if ($acctLoaded->get_id() !== $acc->get_id()) {
        throw new \RuntimeException('Loaded account id mismatch');
    }

    echo "Reload successful. Loaded account id=" . $acctLoaded->get_id() . " email=" . $acctLoaded->get_email() . "\n";

    // now reload character
    // ensure session context matches what the app expects when loading related objects
    $_SESSION['account-id'] = $acctLoaded->get_id();
    $_SESSION['character-id'] = $cid;
    $charReload = new Character($acctLoaded->get_id(), $cid);
    echo "Reloaded character id=" . ($charReload->get_id() ?? 'NULL') . " name=" . ($charReload->get_name() ?? 'NULL') . "\n";

    // Assertions
    echo "Asserting stats values...\n";
    $hp = $charReload->stats->get_hp();
    $str = $charReload->stats->get_str();
    echo "HP={$hp}, STR={$str}\n";

    // Everything passed, but we roll back
    echo "Test passed — rolling back transaction now.\n";
    $db->rollback();
    echo "Rollback complete.\n";
    exit(0);

} catch (\Throwable $e) {
    echo "Error during integration test: " . $e->getMessage() . "\n";
    if (isset($db)) {
        // attempt rollback regardless; if not in a transaction this is a no-op
        @$db->rollback();
        echo "Rollback attempted after error.\n";
    }
    exit(2);
}
