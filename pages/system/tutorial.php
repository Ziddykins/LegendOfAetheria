<?php
    require_once SYSTEM_DIRECTORY . '/bootstrap.php';
    use Game\Account\Account;
    use Game\Character\Character;
    use Game\OpenAI\NPC\Tutorial\Frank;

    $account   = new Account($_SESSION['email']);
    $character = new Character($account->get_id(), $_SESSION['character-id']);

    $frank = new Frank($account->get_id(), $character->get_id());

    $frank->generateTutorial(['hurr']);