<?php
    /* Check if the user has clicked the apply button on the profile tab */
    if (isset($_POST['profile-apply']) && $_POST['profile-apply'] == 1) {
        check_csrf($_POST['csrf-token']);
        $old_password     = $_POST['profile-old-password'];
        $new_password     = $_POST['profile-new-password'];
        $confirm_password = $_POST['profile-confirm-password'];
        $account_email    = $_SESSION['email'];

        /* Old password matches current */
        if (password_verify($old_password, $account->get_password())) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $sql_query = "UPDATE {$t['accounts']} SET `password` = ? WHERE `email` = ?";
                $db->execute_query($sql_query);
        
                session_regenerate_id();
                header('Location: /logout?action=pw_reset&result=pass');
                exit();
            }
        } else {
            header('Location: /game?page=profile&action=pw_reset&result=fail');
            exit();
        }
    }
?>                 
            <div class="container-lg">
                <div class="row">
                    <div class="col-6 p-4 rounded" style="background-color: rgba(5,5,5,.3);">
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
                                <input type="text" class="form-control" id="account-privileges" name="account-privileges" value="<?php echo $account->get_privileges()->name; ?>" disabled>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="character-race" class="col-form-label fw-bold">LoAI Credits</label>
                            <div class="col">
                                <input type="text" class="form-control" id="account-credits" name="account-credits" value="<?php echo $account->get_credits(); ?>" disabled>
                                <small>LoAI Credits can be used for some OpenAI generation, such as your character's description below.</small>
                            </div>
                        </div>

                        <div class="hr fs-3"></div>

                        <form id="profile-password-change" name="profile-password-change" action="/game?page=profile" method="POST">
                            <div class="mb-3 row">
                                <label for="profile-old-password" class="col-form-label fw-bold">Old Password:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="profile-old-password" name="profile-old-password">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="profile-new-password" class="col-form-label fw-bold">New Password:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="profile-new-password" name="profile-new-password">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="profile-confirm-password" class="col-form-label fw-bold">Confirm Password:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="profile-confirm-password" name="profile-confirm-password">
                                </div>
                            </div>

                            <button id="profile-apply" name="profile-apply" class="btn btn-primary border border-black" value="1">Apply</button>
                            <button id="profile-discard" name="profile-discard" class="btn btn-danger border border-black">Discard</button>
                            <input id="csrf-token" name="csrf-token" type="hidden" value="<?php echo $_SESSION['csrf-token']; ?>" />
                        </form>
                    </div>

                    <script>
                        document.getElementById('profile-apply').addEventListener('click',
                            function(e) {
                                let new_pass = document.querySelector("#profile-new-password").value;
                                let pass_confirm = document.querySelector("#profile-confirm-password").value;

                                if (new_pass !== pass_confirm) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    if (typeof gen_toast === "function") {
                                        gen_toast('error-nologin-toast', 'danger', 'bi-key', 'Password Mis-match', 'Please ensure passwords match');
                                    } else {
                                        console.error("gen_toast function is not defined.");
                                    }
                                }
                            }
                        );
                    </script>
                </div>
            </div>