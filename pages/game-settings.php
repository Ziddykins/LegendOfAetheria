<?php
    $ip_lock_checked = '';
    $ip_hidden       = 'invisible';

    if ($account->get_ipLock() === 'True') {
        $ip_lock_checked = 'checked';
        $ip_hidden = '';
    }
?>

<div class="container">
    <div class="row pt-5">
        <div class="col">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active " id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home">
                    Account
                </a>
                <a class="list-group-item list-group-item-action " id="lislt-profile-list" data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="list-profile">
                    Character
                </a>
                <a class="list-group-item list-group-item-action " id="list-messages-list" data-bs-toggle="list" href="#list-messages" role="tab" aria-controls="list-messages">
                    Messages / Mail
                </a>
                <a class="list-group-item list-group-item-action " id="list-chat-list" data-bs-toggle="list" href="#list-chat" role="tab" aria-controls="list-chat">
                    Chat
                </a>
            </div>
        </div>

        <div class="col-8">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                    <div class="row text-center">
                        <div class="col">
                            <h3>
                                <?php echo $_SESSION['name']; ?>'s Account
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="ip-lock-switch" <?php echo $ip_lock_checked; ?>>
                                <label class="form-check-label d-flex" for="ip-lock-switch">
                                    IP lock Account&nbsp;
                                    <div class="small">
                                        <abbr title="An IP lock account can only be accessed by the IP provided in the textbox below" class="initialism">[ ? ]</abbr>
                                    </div>
                                </label>
                            </div>
                            <input id="ip-lock-address" name="ip-lock-address" class="<?php echo $ip_hidden; ?>" type="text" minlength="7" maxlength="15" size="15" pattern="^[0-9]{1,3}\.(?:[0-9]{1,3}\.){2}[0-9]{1,3}$" value="<?php echo $account->get_ipLockAddr(); ?>" />
                        </div>
                        <div class="col">
                            
                        </div>
                    </div>
                    <div class="row text-center">
                        <button id="save-settings" name="save-settings" class="btn btn-primary" type="button" onclick="save_settings('ip_lock')">Save</button>
                        <div id="status-msg" name="status-msg" class="text-center text-success"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
