<?php
    declare(strict_types = 1);
    require_once 'bootstrap.php';
    use Game\Character\Character;

    session_start();

    $character = new Character($_SESSION['account-id'], $_SESSION['character-id']);
    $character->load();

    check_csrf($_GET['csrf-token']);
    
    if (isset($_GET['action']) && $_GET['action'] == 'hud') {
        $payload = [
            'player' => $character->stats->jsonSerialize(),
            'monster' => $character->get_monster()->stats->jsonSerialize()
        ];

        echo json_encode($payload);
        exit(0);
    }
?>