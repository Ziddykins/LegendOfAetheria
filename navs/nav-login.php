<ul class="nav nav-tabs mb-3" id="login-box" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true" onclick=tgl_active_signup(this)>
                                    <i class="fa-sm bi bi-diamond-fill" style="font-size: 10px;"></i> Login
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false" onclick=tgl_active_signup(this)>
                                    <i class="fa-sm bi bi-diamond"  style="font-size: 10px;"></i> Register
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false" onclick=tgl_active_signup(this)>
                                    <i class="bi bi-xs bi-diamond" style="font-size: 10px;"></i> Contact
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status-tab-pane" type="button" role="tab" aria-controls="status-tab-pane" aria-selected="false" onclick=tgl_active_signup(this)>
                                    <i class="bi bi-xs bi-diamond" style="font-size: 10px;"></i> Status
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mb-3" id="login-box-content">
                            
                            <div class="tab-pane fade show active mt-3" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
                                <form id="login-form" name="login-form" action="/" method="POST">
                                    <div class="border">
                                        <div class="d-flex text-bg-tertiary bg-gradient border shadow">
                                            <div class="p-2">
                                                <h6><i class="bi bi-person-fill-gear"></i> Account</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-text" id="icon-email"><i class="bi bi-envelope-plus"></i></span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="email" class="form-control" aria-label="Email" aria-describedby="icon-email" id="login-email" name="login-email" placeholder="Email" required />
                                            <label for="login-email">E-mail</label>
                                        </div>
                                    </div>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="icon-password"><i class="bi bi-key"></i></span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Password" aria-label="Password" aria-describedby="icon-password" required />
                                            <label for="login-password">Password</label>
                                        </div>
                                    </div>

                                    <div class="vstack gap-1">                                    
                                        <button class="btn btn-primary" id="login-submit" name="login-submit" value="1">
                                            <i class="bi bi-door-open-fill"></i> Login
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="1">
                                <form id="register-form" name="register-form" action="/" method="POST">
                                    <div class="mb-2">
                                        <div class="d-flex text-bg-tertiary bg-gradient border shadow">
                                            <div class="p-2 flex-grow-1">
                                                <h6><i class="bi bi-person-fill-gear"></i> Account</h6>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="input-group">
                                                <span class="input-group-text" id="register-icon-email">
                                                    <i class="bi bi-envelope-plus"></i>
                                                </span>

                                                <div class="form-floating flex-grow-1">
                                                    <input type="email" class="form-control" aria-label="Email" aria-describedby="register-icon-email" id="register-email" name="register-email" placeholder="Email" required>
                                                    <label for="login-email">E-mail</label>
                                                </div>
                                            </div>

                                            <div class="input-group">
                                                <span class="input-group-text" id="register-icon-password">
                                                    <i class="bi bi-key"></i>
                                                </span>
                                                <div class="form-floating flex-grow-1">
                                                    <input type="password" class="form-control" id="register-password" name="register-password" placeholder="Password" aria-label="Password" aria-describedby="register-icon-password" required>
                                                    <label for="login-password">Password</label>
                                                </div>
                                            </div>

                                            <div class="input-group">
                                                <span class="input-group-text" id="register-icon-password">
                                                    <i class="bi bi-key"></i>
                                                    <sup style="margin-left: -14px; margin-top: -8px;">x2</sup>
                                                </span>
                                                <div class="form-floating flex-grow-1">
                                                    <input type="password" class="form-control" id="register-password-confirm" name="register-password-confirm" placeholder="Password (Confirm)" aria-label="Password" aria-describedby="register-icon-password-confirm" required>
                                                    <label for="login-password">Password (Confirm)</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="d-flex text-bg-tertiary bg-gradient border shadow">
                                            <div class="p-2 flex-grow-1">
                                                <h6><i class="bi bi-emoji-laughing-fill"></i> Character</h6>
                                            </div>
                                        </div>
                                    
                                        <div class="input-group">
                                            <span class="input-group-text" id="register-icon-character">
                                                <i class="bi bi-credit-card-2-front"></i>
                                            </span>

                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="register-character-name" name="register-character-name" placeholder="character-name" required>
                                                <label for="register-character-name">Character Name</label>
                                            </div>
                                        </div>
                                        
                                        <div class="input-group">
                                            <span class="input-group-text" id="register-icon-character">
                                                <i class="bi bi-droplet"></i>
                                            </span>
                                            <select class="form-select form-select form-control" style="font-size: 1.20rem; font-weight: lighter;" aria-label=".form-select" id="race-select" name="race-select" required>
                                                <option value="Select a Race" disabled selected>Select a Race</option>
                                                <?php
                                                    use Game\Character\Enums\Races;
                                                    foreach (Races::cases() as $race_sel) {
                                                        if ($race_sel->name == 'Default') {
                                                            continue;
                                                        }
                                                        echo "\n                                                <option value=\"" . $race_sel->name . "\">" . $race_sel->name . "</option>\n";
                                                    }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="input-group">
                                            <span class="input-group-text" id="register-icon-avatar">
                                                <i class="bi bi-person-bounding-box"></i>
                                            </span>

                                            <select class="form-select form-select form-control" aria-label=".form-select" id="avatar-select" name="avatar-select" style="font-size: 1.20rem; font-weight: lighter;" required>
                                                <option value="select avatar" disabled selected>Select Avatar</option>
                                                <?php
                                                    $images = scandir('img/avatars');
                                                    for ($i=2; $i<count($images); $i++) {
                                                        $split = explode('.', $images[$i]);
                                                        $title = explode('avatar-', $split[0]);
                                                        $pic_title = $title[1];
                                                        $avatar_text =  preg_replace('/avatar\-/', ' ', $pic_title);
                                                        echo "<option value=\"$pic_title\">$pic_title</option>\n\t\t\t\t\t\t\t\t\t\t\t\t";
                                                    }
                                                    echo "";
                                                ?>

                                                </select>
                                        </div>
                                        
                                        <div id="avatar-img" name="avatar-img" class="justify-content-center align-items-center d-flex invisible">
                                            <div class="border shadow" id="avatar-image-cont" name="avatar-image-cont">
                                                <!-- avatar img placeholder -->
                                            </div>
                                        </div>

                                        <script>
                                            $("#avatar-select").change(function(event) {
                                                document.getElementById("avatar-img").classList.remove("invisible");
                                            });
                                        </script>
                                    </div>

                                    <div class="mb-2 mt-2">    
                                        <div class="border">
                                            <div class="d-flex text-bg-tertiary bg-gradient border shadow align-content-start">
                                                <div class="p-2 flex-grow-1">
                                                    <h6><i class="bi bi-dice-5-fill"></i> Stats</h6>
                                                </div>
                                                
                                                <div class="p-2 fw-bold">
                                                    <i class="bi bi-capslock-fill"></i>
                                                    AP: 
                                                </div>
                                                
                                                <div class="p-2" name="stats-remaining-ap" id="stats-remaining-ap">
                                                    10
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex border">
                                            <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-heart-arrow"></i> str:</div>
                                            <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick="stat_adjust('str-minus');">&minus;</a></div>
                                            <div class="p-2" id="stats-str-cur">10</div>                                        
                                            <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick="stat_adjust('str-plus');">&plus;</a>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex border">
                                            <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-shield"></i> def:</div>
                                            <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick="stat_adjust('def-minus');">&minus;</a></div>
                                            <div class="p-2" id="stats-def-cur">10</div>
                                            <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick="stat_adjust('def-plus');">&plus;</a></div>
                                        </div>

                                        <div class="d-flex border">
                                            <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-stars"></i> int:</div>
                                            <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick="stat_adjust('int-minus');">&minus;</a></div>
                                            <div class="p-2" id="stats-int-cur">10</div>
                                            <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick="stat_adjust('int-plus');">&plus;</a></div>
                                        </div>
                                    </div>
                                    
                                    <input type="text" id="str-ap" name="str-ap" hidden />
                                    <input type="text" id="def-ap" name="def-ap" hidden />
                                    <input type="text" id="int-ap" name="int-ap" hidden />
                                    
                                    
                                    
                                    <div class="vstack gap-1 mb-3">
                                        <button class="btn btn-success mb-3" id="register-submit" name="register-submit" value="1">
                                            <i class="bi bi-clipboard-plus-fill"></i> Register
                                        </button>
                                    </div>

                                    <script>
                                        $("#register-submit").on("click", function (e) {
                                            document.querySelector("#str-ap").value = document.querySelector("#stats-str-cur").innerHTML;
                                            document.querySelector("#def-ap").value = document.querySelector("#stats-def-cur").innerHTML;
                                            document.querySelector("#int-ap").value = document.querySelector("#stats-int-cur").innerHTML;
                                            let password_field = document.getElementById('register-password').value;
                                            let password_confirm = document.getElementById('register-password-confirm').value;

                                            if (password_field !== password_confirm) {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                gen_toast('error-pw-mismatch', 'warning', 'bi-key', 'Password Mis-match', 'Ensure passwords match');
                                            };

                                            if (parseInt(document.querySelector("#stats-remaining-ap").innerHTML) > 0) {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                gen_toast('error-ap-toast', 'warning', 'bi-dice-5-fill', 'Unassigned Attribute Points', 'Ensure all remaining attribute points are applied');
                                            };

                                            if (document.querySelector("#race-select").selectedIndex == 0) {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                gen_toast('error-race-toast', 'warning', 'bi-droplet', 'Select Race', 'You must choose a race first');
                                            };

                                            if (document.querySelector("#avatar-select").selectedIndex == 0 ) {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                gen_toast('error-avatar-toast', 'warning', 'bi-person-bounding-box', 'Select Avatar', 'You must choose an avatar first');
                                            };
                                        });
                                    </script>
                                </form>
                            </div>
                        
                            <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="2">
                                <div class="container">
                                    <div class="row">
                                        <div class="col border border-success">
                                            <form id="contact-form" name="contact-form" action="/?contact_form_submitted=1" method="POST">
                                                <div class="border">
                                                    <div class="d-flex bg-success-subtle bg-gradient">
                                                        <div class="p-2 flex-grow-1"><h6><i class="bi bi-envelope-heart"></i> Contact Me</div>
                                                    </div>
                                                </div>

                                                <div class="input-group">
                                                    <span class="input-group-text" id="icon-name"><i class="bi bi-envelope-plus"></i></span>
                                                    <div class="form-floating flex-grow-1">
                                                        <input type="email" class="form-control" aria-label="Email" aria-describedby="icon-email" id="contact-email" name="contact-email" placeholder="Email" required>
                                                        <label for="contact-email">E-mail<span class="form-text text-danger">*</span></label>
                                                    </div>
                                                </div>

                                                <div class="input-group">
                                                    <span class="input-group-text" id="icon-message"><i class="bi bi-chat-dots-fill"></i></span>
                                                    <div class="form-floating flex-grow-1">
                                                        <textarea class="form-control" id="contact-message" name="contact-message" placeholder="Message" aria-label="Message" aria-describedby="contact-message" style="height: 200px;" required></textarea>
                                                        <label for="contact-message">Message</label>
                                                    </div>
                                                </div>
                                                <p>
                                                    <small class="form-text text-danger fw-bold" style="font-size: 10px;">* Required</small>
                                                </p>

                                                <button class="btn btn-primary mb-3" id="contact-submit" name="contact-submit" value="1">
                                                    🤍 Submit
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade text-center" id="status-tab-pane" role="tabpanel" aria-labelledby="status-tab" tabindex="3">
                                <div class="d-grid gap-2">
                                    <a href="<?php echo "https://" . htmlentities($_SERVER['HTTP_HOST']) . "/";?>">
                                        <div class="btn-group mb-3" role="group" aria-label="basic outlined example">
                                            <button type="button" class="btn btn-sm btn-success bg-gradient text-center text-white text-shadow fw-bolder font-monospace border border-black" style="text-shadow: black 0.45px 0.75px 0.2px;">Working</button>
                                            <button type="button" class="btn btn-sm text-bg-dark bg-gradient text-center fw-bolder font-monospace border border-black" style="text-shadow: black 0.45px 0.75px 0.2px;">&nbsp;&nbsp;Main Game&nbsp;&nbsp;</button>
                                        </div>
                                    </a>

                                    <a href="https://github.com/ziddykins/legendofaetheria">
                                        <div class="btn-group mb-3" role="group" aria-label="basic outlined example">
                                            <button type="button" class="btn btn-sm btn-success bg-gradient text-center text-shadow fw-bolder font-monospace border border-black" style="text-shadow: black 0.45px 0.75px 0.2px;">Working</button>
                                            <button type="button" class="btn btn-sm text-bg-dark bg-gradient text-center fw-bolder font-monospace border border-black" style="text-shadow: black 0.45px 0.75px 0.2px;">&nbsp;Repository&nbsp;&nbsp;</button>
                                        </div>
                                    </a>
                                    
                                    <a href="https://github.com/Ziddykins/LegendOfAetheria/blob/master/install/AutoInstaller.pl">
                                        <div class="btn-group mb-3" role="group" aria-label="basic outlined example">
                                            <button type="button" class="btn btn-sm btn-danger bg-gradient text-center text-shadow fw-bolder font-monospace border border-black" style="text-shadow: black 0.45px 0.75px 0.2px;">Unstable</button>
                                            <button type="button" class="btn btn-sm text-bg-dark bg-gradient text-center fw-bolder font-monospace border border-black" style="text-shadow: black 0.45px 0.75px 0.2px;">Autoinstaller</button>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <script type="text/javascript">
                            $("#avatar-select").on("change", function(e) {
                                let chosen_pic  = document.querySelector('#avatar-select').value;
                                let target_div  = document.querySelector('#avatar-image-cont');
                                let pic_path    = "img/avatars/avatar-" + chosen_pic;
                                let html_string = "<img src=\"" + pic_path + ".webp\" style=\"width: 256px; height: auto;\" alt=\"" + chosen_pic + "\">";

                                target_div.innerHTML = html_string;
                            });
                        </script>
                    
