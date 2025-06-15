<?php
    declare(strict_types = 1);

    use Game\Account\Account;
    use Game\Character\Character;

    $account   = new Account($_SESSION['email']);
    $character = new Character($account->get_id(), $_SESSION['character-id']);

    $request = file_get_contents("php://input");
    $req_obj = json_decode($request, true);

    $response = [
        'status' => null,
        'message' => null,
    ];

    if (isset($req_obj['save_description']) && $req_obj['save_description'] == 1) {
        $description = $req_obj['data'];

        if (strlen($description) > 2048) {
            $response['status'] = 'error';
            $response['message'] = 'Description too long';
            http_response_code(400);
            echo json_encode($response);            
            exit();
        } else {
            $character->set_description($description);
            $response['status'] = 'success';
            $response['message'] = 'Description saved successfully';
            echo json_encode($response);
            exit();
        }
    }

    if (isset($_POST['save']) && $_POST['save'] == 'ip_lock') {
        if (isset($_POST['status']) && $_POST['status'] == 'on') {
            $ip = $_POST['ip'];

            if (strlen($ip) >= 7 && strlen($ip) <= 15) {
                if (preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
                    $account->set_ipLock('True');
                    $account->set_ipLockAddr($ip);
                    echo "Successfully turned on IP Lock";
                } else {
                    http_response_code(400);
                    echo "IP address invalid format";
                    exit();
                }
            } else {
                http_response_code(400);
                echo "IP address invalid length";
                exit();
            }
        } else {
            $account->set_ipLock('False');
            $account->set_ipLockAddr('off');
            echo "Successfully turned off IP Lock";
        }
    }
?>