<?php

use Game\Account\Account;
use Game\Character\Character;

    if (isset($_POST['btn-accept']) && $_POST['btn-accept'] == "1") {
        accept_friend_req($focused_email);
        $log->info('Friend  accepted', ['email_1' => $account->get_email(), 'email_2' => $focused_email]);
    }
?>

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