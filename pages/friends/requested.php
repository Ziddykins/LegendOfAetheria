                    
<?php
    if (friend_status($email) === FriendStatus::REQUEST_SENT) {
        $sql_query = "DELETE FROM {$_ENV['SQL_FRND_TBL']} WHERE email_1 = ? AND email_2 = ?";
        $db->execute_query($sql_query, [ $account->get_email(), $email ]);
        $log->info("Sent friend request deleted", ['To' => $email, 'From' => $account->get_email()]);
    }

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
?>

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