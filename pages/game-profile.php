<?php
    $header_charname = $character->get_name();
    
    if (substr($character->get_name(), -1, 1) == "s") {
        $header_charname = $character->get_name() . "'";
    }
    $header_charname = $character->get_name() . "'s";
    
    if (substr($character->get_name(), -1, 1) == "s") {
        $header_charname = $character->get_name() . "'";
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
                                <h3><?php echo $header_charname; ?> Account</h3>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="account-email" class="col-form-label fw-bold">Email:</label>
                            <div class="col">
                                <input type="text" class="form-control" id="account-email" name="account-email" value="<?php echo $account->get_email() ?>" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                        <label for="account-registered" class="col-form-label fw-bold">Date Registered:</label>
                            <div class="col">
                                <input type="text" class="form-control" id="account-registered" name="account-registered" value="<?php echo $account->get_dateRegistered(); ?>" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="account-privileges" class="col-form-label fw-bold">Privileges:</label>
                            <div class="col">
                                <input type="text" class="form-control" id="account-privileges" name="account-privileges" value="<?php echo $account->get_privileges(); ?>" disabled>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="character-race" class="col-form-label fw-bold">LoAI Credits</label>
                            <div class="col">
                                <input type="text" class="form-control" id="account-credits" name="account-credits" value="<?php echo $account->get_credits(); ?>" disabled>
                                <small>Credits can be used for some OpenAI generation, such as your character's description below. Each generation costs 1 credit</small>
                            </div>
                        </div>

                        <form id="profile-password-change" name="profile-password-change" action="/game?page=profile" method="POST">
                            <div class="mb-3 row">
                                <label for="profile-old-password" class="col-sm-2 col-form-label fw-bold">Old Password:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="profile-old-password" name="profile-old-password">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="profile-new-password" class="col-sm-2 col-form-label fw-bold">New Password:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="profile-new-password" name="profile-new-password">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="profile-confirm-password" class="col-sm-2 col-form-label fw-bold">Confirm Password:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="profile-confirm-password" name="profile-confirm-password">
                                </div>
                            </div>

                            <button id="profile-apply" name="profile-apply" class="btn btn-primary border border-black" value="1">Apply</button>
                            <button id="profile-discard" name="profile-discard" class="btn btn-danger border border-black">Discard</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                        <div class="container row-cols-2">
                            <div class="mb-3 row">
                                <label for="character-name" class="col-form-label fw-bold">Character Name:</label>
                                <div class="col">
                                    <input type="text" class="form-control" id="character-name" name="character-name" value="<?php echo $character->get_name(); ?>" disabled>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="character-race" class="col-form-label fw-bold">Character Race:</label>
                                <div class="col">
                                    <input type="text" class="form-control" id="character-race" name="character-race" value="<?php echo $character->get_race(); ?>" disabled>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="character-description" class="form-label fw-bold">Character Description:</label>
                                <textarea class="form-control" id="character-description" name="character-description" rows="3" width><?php echo $character->get_description(); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <button  id="generate-description" name="generate-description" class="btn btn-primary border border-black swap-icon">
                                    <i id="generate-icon" name="generate-icon" class="bi bi-magic"></i> AI Generate
                                </button>
                                <button id="save-description" name="save-description" class="btn btn-success border border-black swap-icon">
                                    <i id="save-icon" name="save-icon" class="bi bi-save"></i> Save
                                </button>
                                <button id="clear-description" name="clear-description" class="btn btn-warning border border-black swap-icon">
                                    <i id="clear-icon" name="clear-icon" class="bi bi-x-lg"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
                    o look some message things
                </div>

                <div class="tab-pane fade" id="list-chat" role="tabpanel" aria-labelledby="list-chat-list">
                    <a href="https://ᛠᛐᛈᛠᜀᛈᛔᜀᜀmௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌ.memelife.ca/">https://ᛠᛐᛈᛠᜀᛈᛔᜀᜀmௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌ.memelife.ca/</a>
                <div class="tab-pane fade" id="list-chat" role="tabpanel" aria-labelledby="list-chat-list">
                    <a href="https://ᛠᛐᛈᛠᜀᛈᛔᜀᜀmௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌ.memelife.ca/">https://ᛠᛐᛈᛠᜀᛈᛔᜀᜀmௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌௌ.memelife.ca/</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let original_description = "<?php echo $character->get_description();?>";
    let description_changed  = 0;
    let swap_icons = document.querySelectorAll(".swap-icon");
    let id = null;

    $("#character-description").on("change", 
        function (event) {
            if (document.getElementById("character-description").textContent == original_description) {
                description_changed = 0;
            } else {
                description_changed = 1;
            }
        }
    );


    swap_icons.forEach(
        function(element) {
            element.addEventListener('click', 
                function () {
                    let icon_par = element.parentElement;
                    id = '#' + $(element)[0].children[0].id;

                    if (id == '#clear-icon') {
                        document.getElementById("character-description").textContent = "";
                    } else {
                        let do_ajax = 0;
                        let data    = null;
                        let url     = null;

                        $(id).hide();
                        $(element).prepend('<span id="spinner" class="spinner-border spinner-border-sm">');
                        icon_par.classList.add('disabled');

                        if (id == "#save-icon") {
                            url = "/game?page=save";
                            data = {
                                type: 'character',
                                id: <?php echo $character->get_id(); ?>,
                                data: JSON.stringify(document.getElementById("character-description").textContent)
                            };
                            do_ajax = 1;
                        } else if (id == "#generate-icon") {
                            url = "openai";
                            data = { 
                                characterID: <?php echo $character->get_id(); ?>,
                                accountID: <?php echo $account->get_id(); ?>,
                                generate_description: 1
                            };
                            do_ajax = 1;
                        }

                        if (do_ajax) {
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: data,
                                dataType: "json"
                            }).always(function (response) {
                                    document.querySelector("#character-description").value = response.responseText;
                                    $("#spinner").remove();
                                    $(id).show();
                                    icon_par.classList.remove('disabled');
                                    console.log(icon_par);
                                }
                            );
                        }
                    }
                }
            );
        }
    );

    document.getElementById('profile-apply').addEventListener('click',
        function(e) {
            let new_pass = document.querySelector("#profile-new-password").value;
            let pass_confirm = document.querySelector("#profile-confirm-password").value;

            if (new_pass !== pass_confirm) {
                e.preventDefault();
                e.stopPropagation();
                gen_toast('error-nologin-toast', 'danger', 'bi-key', 'Password Mis-match', 'Please ensure passwords match');
            }
        }
    );
</script>
