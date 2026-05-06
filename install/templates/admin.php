<?php
declare(strict_types = 1);
global $account;

use Game\Account\Account;
use Game\Account\Enums\Privileges;
use Game\Character\Character;
use Game\Monster\Pool;

;

if (check_session() === true) {
    if ($account->get_privileges() >= Privileges::ADMINISTRATOR) {
        $account = new Account($_SESSION['email']);
        $account->load();

        $character = new Character($account->get_id());
        $character->set_id($_SESSION['character-id']);
        $character->load();
    }
}
