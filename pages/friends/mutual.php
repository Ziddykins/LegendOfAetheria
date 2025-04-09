<?php
    use Game\Account\Account;
    use Game\Character\Character;
    use Game\Character\Enums\FriendStatus;
?>

<div class="row">
    <div class="col">
        <h3>
            <?php echo fix_name_header($character->get_name()); ?> Friends
        </h3>
    </div>
</div>

<?php
    for ($i = 0; $i < count($friends); $i++) {
        $temp_account = new Account($friends[$i]['email_1']);
        $temp_account->load();
        $online         = $temp_account->get_sessionID();
        $sender_account = new Character($temp_account->get_id());
        $sender_account->set_id(intval($temp_account->get_charSlot1()));
        $sender_account->load();

        $online_indicator = '<p class="badge bg-secondary"><i class="bi bi-lightbulb"></i> Offline</p>';
        $msg_color        = 'btn-secondary';

        if ($online) {
            $online_indicator = '<p class="badge bg-success"><i class="bi bi-lightbulb-fill"></i> Online</p>';
            $msg_color        = 'btn-success';
        }

        echo '<div class="row mb-3">
            <div class="card" style="max-width: 400px;">
                <div class="row g-0">
                    <form id="friend-' . $friends[$i]['id'] . '" name="friend-' . $friends[$i]['id'] . '"
                        action="/game?page=friends&action=block_user" method="POST">
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
        
                                <p class="card-text"><small class="text-body-secondary">
                                    Friends since ' . $friends[$i]["created"] . '</small></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
    }
?>
</div>