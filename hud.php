<?php
    declare(strict_types = 1);
    require_once "vendor/autoload.php";
    require_once "system/constants.php";
    require_once "system/bootstrap.php";

    use Game\Character\Character;

    $character = new Character($_SESSION['account-id'], $_SESSION['character-id']);

    check_csrf($_GET['csrf-token']);
    
    if (isset($_GET['action']) && $_GET['action'] == 'get') {
        $payload = [
            'player' => $character->stats->jsonSerialize(),
        ];

        if ($character->get_monster()) {
            $payload['monster'] = $character->get_monster()->stats->jsonSerialize();
        };

        echo json_encode($payload);
        exit(0);
    }
?>