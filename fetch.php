<?php
    require_once 'bootstrap.php';
    use Game\Character\Character;

    $character    = new Character($_SESSION['account-id'], $_SESSION['character-id']);


    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        $request = json_decode(file_get_contents('php://input'), true);

        if (isset($request['target'])) {
            switch ($request['target']) {
                case 'bank':
                    handle_bank_request($request['payload']);
                    break;
                default:
                    echo json_encode(['error' => 'Invalid target']);
                    break;
                }
        }
    } else {
        echo json_encode(['error' => 'Invalid Access']);
    }

    function handle_bank_request($payload) {
        global $character;

        $current_gold = $character->get_gold();
        $banked_gold = $character->bank->get_gold();
        $current_spindels = $character->get_spindels();
        $banked_spindels = $character->bank->get_spindels();
        $action = $payload['action'];
        $amount = $payload['amount'];
        $which  = $payload['which'];

        $response = null;

        // lol, variable variables converted back into a variable.
        // will either be 'current_gold' or 'current_spindels'
        // and add_gold/add_spindels etc.
        $currency = "current_$which";
        $currency = $$currency;
        $banked = "banked_$which";
        $banked = $$banked;
        $add = "add_$which";
        $sub = "sub_$which";

        if ($action === 'deposit') {
            if ($currency >= $amount) {
                $character->$sub($amount);
                $character->bank->$add($amount);
                $response = json_encode(['status' => 'success', 'message' => 'Successfully deposited']);
            } else {
                http_response_code(400);
                $response = json_encode(['status' => 'error', 'message' => "You don't have enough $which to deposit that much!"]);
            }
        } elseif ($action === 'withdraw') {
            if ($banked >= $amount) {
                $character->$add($amount);
                $character->bank->$sub($amount);
                $response = json_encode(['status' => 'success', 'message' => 'Successfully withdrawn']);
            } else {
                http_response_code(400);
                $response = json_encode(['status' => 'error', 'message'=> "You don't have enough banked $which to withdraw that much!"]);
            }
        }

        echo $response;
    }


    exit();