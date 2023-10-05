<?php



?>

<div class="container text-white">
    <div class="row pt-5">
        <div class="col">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active bg-dark text-white" id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home">
                    Account
                </a>
                <a class="list-group-item list-group-item-action bg-dark text-white" id="lislt-profile-list" data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="list-profile">
                    Character
                </a>
                <a class="list-group-item list-group-item-action bg-dark text-white" id="list-messages-list" data-bs-toggle="list" href="#list-messages" role="tab" aria-controls="list-messages">
                    Messages
                </a>
                <a class="list-group-item list-group-item-action bg-dark text-white" id="list-settings-list" data-bs-toggle="list" href="#list-settings" role="tab" aria-controls="list-settings">
                    hi
                </a>
            </div>
        </div>
        
        <div class="col-8 pt-2">
            <div class="row text-center">
                <div class="col">
                    <h3><?php echo $_SESSION['name']; ?>'s Profile</h3>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                    <div class="mb-3 row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <span><?php echo $account['email']; ?></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Date Registered</label>
                        <div class="col-sm-10">
                            <span><?php echo $account['date_registered']; ?></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Privileges</label>
                        <div class="col-sm-10">
                            <span><?php print $account['privileges'] ?></span>
                        </div>
                    </div>
                    
                    <form id="profile-password-change" name="profile-password-change" action="/game?page=profile" method="POST">
                        <div class="mb-3 row">
                            <label for="profile-old-password" class="col-sm-2 col-form-label">Old Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="profile-old-password" name="profile-old-password">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="profile-new-password" class="col-sm-2 col-form-label">New Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="profile-new-password" name="profile-new-password">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="profile-confirm-password" class="col-sm-2 col-form-label">Confirm Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="profile-confirm-password" name="profile-confirm-password">
                            </div>
                        </div>

                        

                        <button id="profile-apply" name="profile-apply" class="btn btn-primary" value="1">Apply</button>
                        <button id="profile-discard" name="profile-discard" class="btn btn-danger">Discard</button>
                        <script type="text/javascript">
                            $("#profile-apply").on("click", function(e) {
                                let new_pass = document.querySelector("#profile-new-password").value;
                                let pass_confirm = document.querySelector("#profile-confirm-password").value;
                                if (new_pass !== pass_confirm) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    gen_toast('error-nologin-toast', 'danger', 'bi-key', 'Password Mis-match', 'Please ensure passwords match');
                                }
                            });
                        </script>
                    </form>
                </div>

                <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                    o look some more settings
                </div>

                <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
                    o look some message things
                </div>

                <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
                    get out
                </div>
            </div>
        </div>
    </div>
</div>