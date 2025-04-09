<?php

use Game\Account\Account;
use Game\Character\Character;
use Game\Character\Enums\FriendStatus;
    global $db, $log;
    $account = new Account($_SESSION['email']);
    $account->load();

    $character = new Character($account->get_id());
    $character->set_id($_SESSION['character-id']);
    $character->load();

    $header_charname = $character->get_name() . "'s";
        
    $requests = $requested = $friends = $blocked = Array();
    
    /* Populate global $requests array with incoming requests */
    $recvreqs = $db->execute_query(
        "SELECT * FROM {$_ENV['SQL_FRND_TBL']} WHERE `sender_id` = ? or `recipient_id` = ?",
        [ $account->get_email(), $account->get_email() ]
    );
    
    if ($recvreqs->num_rows) {
        while ($row = $recvreqs->fetch_assoc()) {
            $status_in  = friend_status($row['email_1']);
            $status_out = friend_status($row['email_2']);

            if ($status_in == FriendStatus::REQUEST_RECV) {
                array_push($requests, $row);
            } elseif ($status_in == FriendStatus::MUTUAL) {
                array_push($friends, $row);
            } elseif ($status_out == FriendStatus::REQUEST_SENT) {
                array_push($requested, $row);
            }
        }
    }

    if (substr($character->get_name(), -1, 1) == "s") {
        $header_charname = $character->get_name() . "'";
    }
    
    if (isset($_POST['page']) && $_POST['page']  == 'friends') {
        if (isset($_POST['action'])) {
            [$email, $focused_email, $requested_email] = [null, null, null];
            $posts = ['friends-request-send', 'email', 'focused-request'];

            foreach ($posts as $post) {
                if (isset($_POST[$post])) {
                    if (!check_valid_email($_POST[$post])) {
                        $log->error('Invalid email', ['email' => $_POST[$post]]);
                    } else {
                        // If the current post is one of these, set the corresponding variable to the sent value
                        // Otherwise, set the value to itself (no change)
                        $email           = $post == 'email'                ? $_POST[$post] : $email;
                        $focused_email   = $post == 'focused-request'      ? $_POST[$post] : $focused_email;
                        $requested_email = $post == 'friends-request-send' ? $_POST[$post] : $requested_email;
                    }
                }
            }

        switch ($_POST['action']) {
            case 'cancel_request':

                break;
            case 'accept_request':

                break;
            case 'send_request':
                
                break;
            }
        }
    }
?>
