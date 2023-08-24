<script>
                        $(document).ready(function(){ 
                            if (location.search.match(/\?failed_login/)) {
                                gen_toast('error-login-toast', 'danger', 'bi-dash-circle', 'Error', 'Invalid login credentials!');
                            } else if (location.search.match(/\?register_success/)) {
                                gen_toast('success-register-toast', 'success', 'bi-check', 'Success', 'Account and Character successfully created, you can now log in');
                            } else if (location.search.match(/\?do_register/)) {
                                document.getElementById('register-tab').click();
                                document.getElementById('register-email').value = location.search.split('&')[1].replace('email=', '');
                                gen_toast('account-not-exist', 'success', 'bi-check', 'Success', 'No account associated with this email, register?');
                            } else if (location.search.match(/\?logged_out/)) {
                                gen_toast('logged-out', 'success', 'bi-check', 'Logged Out', 'Successfully logged out!');
                            } else if (location.search.match(/\?gooft/)) {
                                gen_toast('test-popup', 'warning', 'bi-balloon " style="font-size: 72px;"','Warning', '<marquee>Aw snap ya mighta gooft</marquee>');
                            } else if (location.search.match(/\?account_exists/)) {
                                gen_toast('account-exists', 'danger', 'bi-dash-circle', 'Account Exists', 'An account already exists with that email');
                            }
                            
                            document.getElementById('login-email').focus();
                        });
                    </script>
                    <ul class="nav nav-tabs" id="login-box" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true" onclick=tgl_active(this)>
                            <i class="fa-sm bi bi-diamond-fill" style="font-size: 10px;"></i> Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false" onclick=tgl_active(this)>
                                <i class="fa-sm bi bi-diamond"  style="font-size: 10px;"></i> Register</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false" onclick=tgl_active(this)>
                                <i class="bi bi-xs bi-diamond" style="font-size: 10px;"></i> Contact</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="login-box-content">
                        <p></p>
                        <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
                            <form id="login-form" name="login-form" action="/" method="POST">
                                <div class="border">
                                    <div class="d-flex bg-body-secondary border">
                                        <div class="p-2 flex-grow-1"><h6><i class="bi bi-person-fill-gear"></i> Account</h6></div>
                                    </div>
                                </div>
                                <div class="mb-3 input-group">
                                    <span class="input-group-text" id="icon-email"><i class="bi bi-envelope-plus"></i></span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="email" class="form-control" aria-label="Email" aria-describedby="icon-email" id="login-email" name="login-email" placeholder="Email" required>
                                        <label for="login-email">E-mail</label>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="icon-password"><i class="bi bi-key"></i></span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Password" aria-label="Password" aria-describedby="icon-password" required>
                                        <label for="login-password">Password</label>
                                    </div>
                                </div>
                                <button class="btn btn-primary mb-3" id="login-submit" name="login-submit" value="1">
                                    <i class="bi bi-door-open-fill"></i> Login
                                </button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="1">
                            <form id="register-form" name="register-form" action="/" method="POST">
                                <div class="border">
                                    <div class="d-flex bg-body-secondary border">
                                        <div class="p-2 flex-grow-1"><h6><i class="bi bi-person-fill-gear"></i> Account</h6></div>
                                    </div>

                                    <div>
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="register-icon-email"><i class="bi bi-envelope-plus"></i></span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="email" class="form-control" aria-label="Email" aria-describedby="register-icon-email" id="register-email" name="register-email" placeholder="Email" required>
                                                <label for="login-email">E-mail</label>
                                            </div>
                                        </div>

                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="register-icon-password"><i class="bi bi-key"></i></span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="password" class="form-control" id="register-password" name="register-password" placeholder="Password" aria-label="Password" aria-describedby="register-icon-password" required>
                                                <label for="login-password">Password</label>
                                            </div>
                                        </div>

                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="register-icon-password"><i class="bi bi-key"></i><sup style="margin-left: -12px; margin-top: -8px;">x2</sup></span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="password" class="form-control" id="register-password-confirm" name="register-password-confirm" placeholder="Password (Confirm)" aria-label="Password" aria-describedby="register-icon-password-confirm" required>
                                                <label for="login-password">Password (Confirm)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border">
                                    <div class="d-flex bg-body-secondary border">
                                        <div class="p-2 flex-grow-1"><h6><i class="bi bi-emoji-laughing-fill"></i> Character</h6></div>
                                    </div>
                                
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="register-icon-character">
                                            <i class="bi bi-credit-card-2-front"></i>
                                        </span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="text" class="form-control" id="register-character-name" name="register-character-name" placeholder="character-name" required>
                                            <label for="register-character-name">Character Name</label>
                                        </div>
                                    </div>
                                    
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="register-icon-character">
                                            <i class="bi bi-droplet"></i>
                                        </span>
                                        <select class="form-select form-select form-control" style="font-size: 1.00rem;" aria-label=".form-select" id="race-select" name="race-select" required>
                                            <option value="Select a Race" disabled selected>Select a Race</option>
                                            <?php
                                                foreach (Races::cases() as $race_sel) {
                                                    if ($race_sel->name == 'Default') {
                                                        continue;
                                                    }
                                                    echo "<option value=\"" . $race_sel->name . "\">" . $race_sel->name . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-text" id="register-icon-avatar">
                                            <i class="bi bi-person-bounding-box"></i>
                                        </span>
                                        <select class="form-select form-select form-control" aria-label=".form-select" id="avatar-select" name="avatar-select" style="font-size: 1.00rem" required>
                                            <option value="Select Avatar" disabled selected>Select Avatar</option>
                                            <?php
                                                $images = scandir('img/avatars');
                                                for ($i=2; $i<count($images); $i++) {
                                                    $split = explode('.', $images[$i]);
                                                    $title = explode('avatar-', $split[0]);
                                                    $pic_title = $title[1];
                                                    echo preg_replace('/avatar\-/', ' ', $pic_title);
                                                    print "<option value=\"$pic_title\">$pic_title</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="justify-content-center align-items-center d-flex">
                                        <div class="border shadow mb-1" id="avatar-image-cont" name="avatar-image-cont">
                                            <!-- avatar img placeholder -->
                                        </div>
                                    </div>

                                    <div class="border">
                                        <div class="d-flex bg-body-secondary border">
                                        <div class="p-2 flex-grow-1"><h6><i class="bi bi-dice-5-fill"></i> Stats</h6></div>
                                        <div class="p-2 fw-bold"><i class="bi bi-capslock-fill"></i> AP: </div>
                                        <div class="p-2" name="stats-remaining-ap" id="stats-remaining-ap">10</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-heart-arrow"></i> str:</div>
                                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick="stat_adjust('str-minus');">&minus;</a></div>
                                        <div class="p-2" id="stats-str-cur">10</div>                                        
                                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick="stat_adjust('str-plus');">&plus;</a>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-shield"></i> def:</div>
                                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick="stat_adjust('def-minus');">&minus;</a></div>
                                        <div class="p-2" id="stats-def-cur">10</div>
                                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick="stat_adjust('def-plus');">&plus;</a></div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-stars"></i> int:</div>
                                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick="stat_adjust('int-minus');">&minus;</a></div>
                                        <div class="p-2" id="stats-int-cur">10</div>
                                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick="stat_adjust('int-plus');">&plus;</a></div>
                                    </div>
                                </div>
                                
                                <input type="text" id="str-ap" name="str-ap" hidden></input>
                                <input type="text" id="def-ap" name="def-ap" hidden></input>
                                <input type="text" id="int-ap" name="int-ap" hidden></input>
                                
                                <p></p>
                                
                                <button class="btn btn-success mb-3" id="register-submit" name="register-submit" value="1">
                                    <i class="bi bi-clipboard-plus-fill"></i> Register
                                </button>

                                <script>
                                    $("#register-submit").on("click", function (e) {
                                        document.querySelector("#str-ap").value = document.querySelector("#stats-str-cur").innerHTML;
                                        document.querySelector("#def-ap").value = document.querySelector("#stats-def-cur").innerHTML;
                                        document.querySelector("#int-ap").value = document.querySelector("#stats-int-cur").innerHTML;

                                        if (parseInt(document.querySelector("#stats-remaining-ap").innerHTML) > 0) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            gen_toast('error-ap-toast', 'warning', 'bi-balloon', 'Unassigned Attribute Points', 'Ensure all remaining attribute points are applied');
                                        }

                                        if (document.querySelector("#race-select").selectedIndex === 0 
                                            || document.querySelector("#avatar-select").selectedIndex === 0 ) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            
                                        }
                                    });
                                </script>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                        .3..
                    </div>
                </div>

                <script type="text/javascript">
                    $("#avatar-select").on("change", function(e) {
                        let chosen_pic  = document.querySelector('#avatar-select').value;
                        let target_div  = document.querySelector('#avatar-image-cont');
                        let pic_path    = "img/avatars/avatar-" + chosen_pic;
                        let html_string = "<img src=\"" + pic_path + ".png\" style=\"width: 256px; height: auto;\" alt=\"" + chosen_pic + "\">";

                        target_div.innerHTML = html_string;
                    });
                </script>

                <div aria-live="polite" aria-atomic="true" class="position-relative">
                    <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>

                    </div>
                </div>