<?php
    $account         = get_user($_SESSION['email'], 'account');
    $character       = get_user($account['id'], 'character');
    $header_charname = $character['name'] . "'s";
    
    /* :3 */
    $requests = $requested = $friends = $blocked = Array();
    
    /* Populate global $requests array with incoming requests */
    $recvreqs = $db->query("SELECT * FROM tbl_friends WHERE email_2 = '" . $account['email'] . "' OR email_1 = '" . $account['email'] . "'");
    
    if ($recvreqs->num_rows) {
       while ($row = $recvreqs->fetch_assoc()) {
            $status_in  = friend_status($row['email_1']);
            $status_out = friend_status($row['email_2']);

//            $log->warning("Checking friend status for ". $row['email_1']." and ". $row['email_2'] ." - ". $status->name);

            if ($status_in == FriendStatus::REQUEST) {
                array_push($requests, $row);
            } else if ($status_in == FriendStatus::MUTUAL) {
                array_push($friends, $row);
            } else if ($status_out == FriendStatus::REQUESTED) {
                array_push($requested, $row);
            }
        }
    }

    if (substr($character['name'], -1, 1) == "s") {
        $header_charname = $character['name'] . "'";
    }
    
    if ($_REQUEST['page'] == 'friends') {
        if (isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] == 'cancel_request') {
                $sql_query = "DELETE FROM `" . $_ENV['SQL_FRND_TBL'] . "` " .
                             "WHERE email_1 = ? AND email_2 = ?";
                             
                if (friend_status($_REQUEST['email']) === FriendStatus::REQUESTED) {
                    $prepped = $db->prepare($sql_query);
                    $prepped->bind_param("ss", $account['email'], $_REQUEST['email']);
                    $prepped->execute();
                }
            } else if ($_REQUEST['action'] == 'accept_request') {
                if (isset($_POST['btn-accept']) && $_POST['btn-accept'] == "1") {
                    accept_friend_req($_POST['focused-request']);
                    $log->info('Friend request accepted', 
                        [ 'email_1' => $account['email'], 'email_2' => $_POST['focused-request'] ]);
                }
            } else if ($_REQUEST['action'] == 'send_request') {
                if (isset($_POST['friends-send-request']) && $_POST['friends-send-request'] == "1") {
                    if (isset($_POST['friends-request-send'])) {
                        $requested_email = filter_var($_POST['friends-request-send'], FILTER_SANITIZE_EMAIL);
               
                        if (friend_status($requested_email) == FriendStatus::NONE) {
                            $sql_query = "INSERT INTO tbl_friends (email_1, email_2) VALUES (?,?)";
                            $prepped = $db->prepare($sql_query);
                            $prepped->bind_param("ss", $account['email'], $requested_email);
                            $prepped->execute();
                            $log->warning('Friend request sent', [ 'email_1' => $account['email'], 'email_2' => $requested_email ]);
                        }
                    }
                }
            }
        }
    }

    function friend_status($email) {
        global $db, $account;
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        $sql_query    = "SELECT * FROM tbl_friends WHERE email_1 = '" . $account['email'] . "' AND email_2 LIKE '%$email%'";
        $results_us   = $db->query($sql_query);
        $count_one    = $results_us->num_rows;
     
        $sql_query    = "SELECT * FROM tbl_friends WHERE email_2 = '". $account['email'] . "' AND email_1 LIKE '%$email%'";
        $results_them = $db->query($sql_query);
        $count_two    = $results_them->num_rows;
        

        switch (true) {
            case ($count_one && $count_two):
                return FriendStatus::MUTUAL;
            case ($count_one && !$count_two):
                if (substr($results_us->fetch_assoc()['email_2'], 0, 3) == '¿b¿') {
                    return FriendStatus::BLOCKED;
                }
                return FriendStatus::REQUESTED;
            case ($count_two && !$count_one):
                if (substr($results_them->fetch_assoc()['email_2'], 0, 3) == '¿b¿') {
                    return FriendStatus::BLOCKED_BY;
                }
                return FriendStatus::REQUEST;
            default:
                return FriendStatus::NONE;
        }

        return FRIEND_STATUS_ERROR;
    }

    function accept_friend_req($email) {
        global $db, $log, $account;
        
        if (friend_status($email) === FriendStatus::REQUEST) {
            $sql_query = 'INSERT INTO tbl_friends (email_1, email_2) VALUES (?,?)';
            $prepped = $db->prepare($sql_query);
            $prepped->bind_param("ss", $account['email'], $email);
            $prepped->execute();
            
            $log->info('Friend request accepted', 
                [
                    'email_1' => $account['email'], 
                    'email_2' => $email
                ]
            );
        }
    }
    
    function block_user($email) {
        
    }
?>

<div class="container ">
    <div class="row pt-5">
        <div class="col">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home">
                    Friends
                </a>
                <a class="list-group-item list-group-item-action" id="list-friendreqs-header" data-bs-toggle="list" href="#friends-request" role="tab" aria-controls="friends-request">
                    Requests
                    <?php if (count($requests) > 0) {?>
                        <span class="badge rounded-pill bg-<?php echo count($requests) ? 'danger' : 'primary'; ?>"><?php echo count($requests); ?></span>
                    <?php } ?>
                </a>
                <a class="list-group-item list-group-item-action" id="list-friendreqd-header" data-bs-toggle="list" href="#friends-requested" role="tab" aria-controls="friends-requested">
                    Requested
                </a>
                <a class="list-group-item list-group-item-action" id="list-settings-list" data-bs-toggle="list" href="#list-settings" role="tab" aria-controls="list-settings">
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
                        for ($i=0; $i<count($friends)-1; $i++) {
                            $temp_account   = get_user($friends[$i]['email_1'], 'account');
                            $sender_account = get_user($temp_account['id'], 'character');
                            $check_user     = file_exists(escapeshellcmd('/var/lib/php/sessions/sess_' . $temp_account['session_id']));
                            
                            $log->info('Account online status: ', [ 'email' => $temp_account['email'], 'check_user' => $check_user ]);
                            $online_indicator = '<p class="badge bg-secondary"><i class="bi bi-lightbulb"></i> Offline</p>';
                            $msg_color = 'btn-secondary';
                            
                            if ($check_user) {
                                $online_indicator = '<p class="badge bg-success"><i class="bi bi-lightbulb-fill"></i> Online</p>';
                                $msg_color = 'btn-success';
                            }

                            echo   '<div class="row mb-3">
                                        <div class="card" style="max-width: 400px;">
                                            <div class="row g-0">
                                                <form id="friend-' . $friends[$i]['id'] . '" name="friend-' . $friends[$i]['id'] . '" action="/game?page=friends&action=block_user" method="POST">
                                                    <div class="col-2 pt-2 pb-2">
                                                        <img src="/img/avatars/' . $sender_account['avatar'] . '" class="img-fluid rounded" alt="friend-' . $i . '-avatar">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">' . $online_indicator . ' - ' . $temp_account['email'] . '
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
                            $temp_account   = get_user($requests[$i]['email_1'], 'account');
                            $sender_account = get_user($temp_account['id'], 'character');
                            echo '<form id="accept-request-' . $sender_account['account_id'] . '" name="accept-request-'. $sender_account['account_id']. '" action="/game?page=friends&action=accept_request" method="POST">';
                            echo '<div class="row mb-3">
                                    <div class="card" style="max-width: 540px;">
                                        <div class="row g-0">
                                            <div class="col-md-4 pt-2 pb-2">
                                                <img src="/img/avatars/' . $sender_account['avatar'] . '" class="img-fluid rounded" alt="Incoming-request-avatar">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <h5 class="card-title">' . $requests[$i]['email_1'] . '</h5>
                                                    <p class="card-text">
                                                        sent you a friend request
                                                    </p>
                                                    <div class="row">
                                                        <div class="btn-group">
                                                            <button id="btn-accept" name="btn-accept" class="btn btn-primary btn-block" value="1">Accept</button>
                                                            <button id="btn-decline" name="btn-decline" class="btn btn-warning btn-block" value="1">Decline</button>
                                                            <button id="btn-block" name="btn-block" class="btn btn-danger btn-block" value="1">Block</button>                                                            
                                                            <input type="hidden" id="focused-request" name="focused-request" value="' . $temp_account['email'] . '">
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

                        <div class="row mb-3">
                            <div class="col-8">
                                l
                                <input id="friends-request-send" name="friends-request-send" class="form-control">
                            </div>
                            <div class="col-2">
                                <button class="btn btn-primary form-control" id="friends-send-request" name="friends-send-request" value="1">Request</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-3">
                        <div class="col">
                            <table class="table table-hover">
                                <thead>
                                    <th scope="col">Id</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Sent</th>
                                    <th scope="col">Cancel</th>
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
