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

            if ($status_in == FriendStatus::REQUEST) {
                array_push($requests, $row);
            } else if ($status_in == FriendStatus::MUTUAL) {
                array_push($friends, $row);
            } else if ($status_out == FriendStatus::REQUESTED) {
                array_push($requested, $row);
            }
        }
    }

    if (substr($character->get_name(), -1, 1) == "s") {
        $header_charname = $character->get_name() . "'";
    }
    
    if (isset($_POST['page']) && $_POST['page']
    
    
    
    
    
    == 'friends') {
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
                if (friend_status($email) === FriendStatus::REQUESTED) {
                    $sql_query = "DELETE FROM {$_ENV['SQL_FRND_TBL']} WHERE email_1 = ? AND email_2 = ?";
                    $db->execute_query($sql_query, [ $account->get_email(), $email ]);
                    $log->info("Sent friend request deleted", ['To' => $email, 'From' => $account->get_email()]);
                }
                break;
            case 'accept_request':
                if (isset($_POST['btn-accept']) && $_POST['btn-accept'] == "1") {
                    accept_friend_req($focused_email);
                    $log->info('Friend  accepted', ['email_1' => $account->get_email(), 'email_2' => $focused_email]);
                }
                break;
            case 'send_request':
                if (isset($_POST['friends-send-request']) && $_POST['friends-send-request'] == "1") {
                    if (isset($_POST['friends-request-send'])) {
                        switch (friend_status($requested_email)) {
                            case FriendStatus::NONE:
                                if (check_valid_email($requested_email)) {
                                    if ($requested_email != $account->get_email()) {
                                        $sql_query = "INSERT INTO tbl_friends (email_1, email_2) VALUES (?,?)";
                                        $db->execute_query($sql_query, [$account->get_email(), $requested_email]);
                                        $log->info(
                                            'Friend request sent',
                                            ['email_1' => $account->get_email(), 'email_2' => $requested_email]
                                        );
                                    } else {
                                        header('Location: /game?page=friends&error=self_add');
                                        exit();
                                    }
                                } else {
                                    header('Location: /game?page=friends&error=invalid_email');
                                    exit();
                                }
                                break;
                            default:
                                header('Location: /game?page=friends&error=already_friend');
                                exit();
                        }
                    }
                }
                break;
        }
        }
    }
?>

<div class="container ">
    <div class="row pt-5">
        <div class="col">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list"
                    href="#list-home" role="tab" aria-controls="list-home">
                    Friends
                </a>
                <a class="list-group-item list-group-item-action" id="list-friendreqs-header" data-bs-toggle="list"
                    href="#friends-request" role="tab" aria-controls="friends-request">
                    Requests
                    <?php if (count($requests) > 0): ?>
                    <span
                        class="badge rounded-pill bg-<?php echo count($requests) ? 'danger' : 'primary'; ?>"><?php echo count($requests); ?></span>
                    <?php endif; ?>
                </a>
                <a class="list-group-item list-group-item-action" id="list-friendreqd-header" data-bs-toggle="list"
                    href="#friends-requested" role="tab" aria-controls="friends-requested">
                    Requested
                </a>
                <a class="list-group-item list-group-item-action" id="list-settings-list" data-bs-toggle="list"
                    href="#list-settings" role="tab" aria-controls="list-settings">
                    Blocked
                </a>
            </div>
        </div>
        <div class="col-8">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo $header_charname; ?> Friends</h3>
                        </div>
                    </div>
                    <?php
                        for ($i=0; $i<count($friends); $i++) {
                            $temp_account   = new Account($friends[$i]['email_1']);
                            $temp_account->load();
                            $online = $temp_account->get_sessionID();
                            $sender_account = new Character($temp_account->get_id());
                            $sender_account->set_id(intval($temp_account->get_charSlot1()));
                            $sender_account->load();
                            
                          
                            $online_indicator = '<p class="badge bg-secondary"><i class="bi bi-lightbulb"></i> Offline</p>';
                            $msg_color = 'btn-secondary';
                            
                            if ($online) {
                                $online_indicator = '<p class="badge bg-success"><i class="bi bi-lightbulb-fill"></i> Online</p>';
                                $msg_color = 'btn-success';
                            }

                            echo   '<div class="row mb-3">
                                        <div class="card" style="max-width: 400px;">
                                            <div class="row g-0">
                                                <form id="friend-' . $friends[$i]['id'] . '" name="friend-' . $friends[$i]['id'] . '" action="/game?page=friends&action=block_user" method="POST">
                                                    <div class="col-2 pt-2 pb-2">
                                                        <img src="/img/avatars/' . $sender_account->get_avatar() . '" class="img-fluid rounded" alt="friend-' . $i . '-avatar">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">' . $online_indicator . ' - ' . $temp_account->get_email() . '
                                                        <div class="btn-group">
                                                            <button class="btn ' . $msg_color . ' btn-block btn-sm">Message</button>
                                                            <button class="btn btn-warning btn-block btn-sm">Remove</button>
                                                            <button class="btn btn-danger btn-block btn-sm">Block</button>
                                                        </div>
                                                            
                                                        <p class="card-text"><small class="text-body-secondary">Friends since ' . $friends[$i]["created"] . '</small></p>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>';
                        }
                    ?>

                </div>
                <div class="tab-pane fade" id="friends-request" role="tabpanel" aria-labelledby="list-friendreqs-header">
                    <div class="row mb-3">
                        <div class="col">
                            <h3><?php echo $header_charname; ?> Requests</h3>
                        </div>
                    </div>

                    <?php
                        for ($i=0; $i<count($requests); $i++) {
                            $temp_account   = new Account($requests[$i]['email_1']);
                            $temp_account->load();
                            $sender_account = new Character($temp_account->get_id());
                            $sender_account->set_id(intval($temp_account->get_charSlot1()));
                            $sender_account->load();
                            
                            echo '<form id="accept-request-' . $sender_account->get_accountID() . '" name="accept-request-'. $sender_account->get_accountID(). '" action="/game?page=friends&action=accept_request" method="POST">';
                            echo '<div class="row mb-3">
                                    <div class="card" style="max-width: 540px;">
                                        <div class="row g-0">
                                            <div class="col-md-4 pt-2 pb-2">
                                                <img src="/img/avatars/' . $sender_account->get_avatar() . '" class="img-fluid rounded" alt="Incoming-request-avatar">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <h5 class="card-title">' . $requests[$i]['email_1'] . '</h5>
                                                    <p class="card-text">
                                                        sent you a friend request
                                                    </p>
                                                    <div class="row">
                                                        <div class="btn-group">
                                                            <button id="btn-accept" name="btn-accept" class="btn btn-primary btn-block rounded" value="1">Accept&nbsp;</button>&nbsp;
                                                            <button id="btn-decline" name="btn-decline" class="btn btn-warning btn-block rounded" value="1">Decline</button>&nbsp;
                                                            <button id="btn-block" name="btn-block" class="btn btn-danger btn-block rounded" value="1">&nbsp;Block&nbsp;</button>                                                            
                                                            <input type="hidden" id="focused-request" name="focused-request" value="' . $temp_account->get_email() . '">
                                                        </div>
                                                    </div>
                                                    <p class="card-text"><small class="text-body-secondary">Sent at ' . $requests[$i]['created'] . '</small></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>';
                        }
                        if (!count($requests)) {
                            echo '<div class="row">
                                    <div class="col">
                                        <p class="lead">No new requests.</p>
                                    </div>
                                </div>';
                        }
                    ?>
                </div>
                <div class="tab-pane fade" id="friends-requested" role="tabpanel" aria-labelledby="list-friendreqd-header">
                    <form id='form-send-friends-request' name='form-send-friends-request' action='/game?page=friends&action=send_request' method='POST'>
                        <div class="row">
                            <div class="col">
                                <h3><?php echo $header_charname; ?> Requested</h3>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="friends-request-send" class="col-sm-2 col-form-label">Enter email:</label>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-8">
                                <input id="friends-request-send" name="friends-request-send" class="form-control">
                            </div>
                            <div class="col-2">
                                <button class="btn btn-primary form-control" id="friends-send-request" name="friends-send-request" value="1">Request</button>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col text-bg-primary border text-center">
                            Pending Sent Requests
                        </div>
                    </div>
               
                    <div class="row border">
                        <div class="col">
                            <table class="table table-hover text-center">
                                <thead>
                                    <th scope="col" class="border">Id</th>
                                    <th scope="col" class="border">Email</th>
                                    <th scope="col" class="border">Sent</th>
                                    <th scope="col" class="border">Cancel</th>
                                </thead>
                                <tbody>
                                    <?php
                                        for ($i=0; $i<=count($requested)-1; $i++) {
                                            echo '<tr>
                                                      <th scope="row">' . $i . '</th>
                                                      <td>' . $requested[$i]['email_2'] . '</td>
                                                      <td>' . $requested[$i]['created'] . '</td>
                                                      <td><a href="/game?page=friends&action=cancel_request&email=' . $requested[$i]['email_2'] . '">X</a>
                                                  </tr>';
                                        }
                                        if (!count($requested)) {
                                            echo '<tr class="border">
                                                    <td colspan="4" align="center" valign="middle">None</td>
                                                </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
                    <form id="settings-account" name="settings-account" action="/set_settings.php" method="POST">
                        <div class="row">
                            <div class="col">
                                <h3><?php echo $header_charname; ?> Blocked</h3>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
