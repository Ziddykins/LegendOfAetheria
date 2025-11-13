<ul class="nav nav-tabs mb-3" id="login-box" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true" onclick=tgl_active_signup(this)>
            <i class="fa-sm bi bi-diamond-fill" style="font-size: 10px;"></i> Login
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false" onclick=tgl_active_signup(this)>
            <i class="fa-sm bi bi-diamond" style="font-size: 10px;"></i> Register
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

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#admin-tab-pane" type="button" role="tab" aria-controls="admin-tab-pane" aria-selected="false" onclick=tgl_active_signup(this)>
            <i class="bi bi-xs bi-diamond" style="font-size: 10px;"></i> Admin
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
                <span class="input-group-text" id="login-icon-password">
                    <i class="bi bi-key"></i>
                </span>
                <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Password" aria-label="Password" aria-describedby="icon-password password-addon" required />
                <span class="input-group-text" data-loa="pw_toggle">Show</span>
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
                        <h6>
                            <i class="bi bi-person-fill-gear"></i> Account
                            <i id="random" class="bi bi-shuffle float-end me-4"></i>
                            <i id="debug" class="bi bi-diamond-half float-end me-4"></i>
                        </h6>

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
                        <input type="password" class="form-control" id="register-password" name="register-password" placeholder="Password" aria-label="Password" aria-describedby="icon-password password-addon" required />
                        <span class="input-group-text" data-loa="pw_toggle">Show</span>
                    </div>

                    <div class="input-group">
                        <span class="input-group-text" id="register-icon-password-confirm">
                            <i class="bi bi-key"></i>
                            <sup style="margin-left: -14px; margin-top: -8px;">x2</sup>
                        </span>
                        <input type="password" class="form-control" id="register-password-confirm" name="register-password-confirm" placeholder="Password (confirm)" aria-label="Password" aria-describedby="icon-password password-addon" required />
                        <span class="input-group-text" data-loa="pw_toggle">Show</span>
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
                        for ($i = 2; $i < count($images); $i++) {
                            if (preg_match('/unknown/', $images[$i])) {
                                continue;
                            }

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
                $("#register-submit").on("click", function(e) {
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

                    if (document.querySelector("#avatar-select").selectedIndex == 0) {
                        e.preventDefault();
                        e.stopPropagation();
                        gen_toast('error-avatar-toast', 'warning', 'bi-person-bounding-box', 'Select Avatar', 'You must choose an avatar first');
                    };
                });
            </script>
        </form>
    </div>

    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="2">
        <form id="contact-form" name="contact-form" action="/" method="POST">
            <div class="border">
                <div class="d-flex text-bg-tertiary bg-gradient border shadow">
                    <div class="p-2">
                        <h6><i class="bi bi-person-fill-gear"></i> Contact Me</h6>
                    </div>
                </div>
            </div>

            <div class="input-group">
                <span class="input-group-text" id="icon-email"><i class="bi bi-envelope-plus"></i></span>
                <div class="form-floating flex-grow-1">
                    <input type="email" class="form-control" aria-label="Email" aria-describedby="icon-email" id="contact-email" name="contact-email" placeholder="Email" required />
                    <label for="contact-email">E-mail</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text"><i class="bi bi-chat"></i></span>
                <div class="form-floating flex-grow-1">
                    <textarea class="form-control" id="contact-message" style="height: 50px;"></textarea>
                    <label for="contact-message">Message</label>
                </div>
            </div>

            <div class="vstack gap-1">
                <button class="btn btn-primary" id="contact-submit" name="contact-submit" value="1">
                    <i class="bi bi-door-open-fill"></i> Contact
                </button>
            </div>
        </form>
    </div>

    <div class="tab-pane fade text-center" id="status-tab-pane" role="tabpanel" aria-labelledby="status-tab" tabindex="3">
        <div class="d-grid gap-2">
            <a href="<?php echo "https://" . htmlentities($_SERVER['HTTP_HOST']) . "/"; ?>">
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

    <div class="tab-pane fade" id="admin-tab-pane" role="tabpanel" aria-labelledby="admin-tab" tabindex="4">
        <form id="admin-form" name="admin-form" action="/" method="POST">
            <div class="border">
                <div class="d-flex text-bg-danger opacity-50 bg-gradient border shadow">
                    <div class="p-2">
                        <h6><i class="bi bi-person-fill-gear"></i> Administrator Portal</h6>
                    </div>
                </div>
            </div>

            <div class="input-group">
                <span class="input-group-text" id="icon-email"><i class="bi bi-envelope-plus"></i></span>
                <div class="form-floating flex-grow-1">
                    <input type="email" class="form-control" aria-label="Email" aria-describedby="icon-email" id="admin-email" name="admin-email" placeholder="Email" required />
                    <label for="admin-email">E-mail</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="admin-icon-password">
                    <i class="bi bi-key"></i>
                </span>
                <input type="password" class="form-control" id="admin-password" name="admin-password" placeholder="Password" aria-label="Password" aria-describedby="icon-password password-addon" required />
                <span class="input-group-text" data-loa="pw_toggle">Show</span>
            </div>

            <div class="vstack gap-1">
                <button class="btn btn-primary" id="admin-submit" name="admin-submit" value="1">
                    <i class="bi bi-door-open-fill"></i> Login
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $("#avatar-select").on("change", function(e) {
        let chosen_pic = document.querySelector('#avatar-select').value;
        let target_div = document.querySelector('#avatar-image-cont');
        let pic_path = "img/avatars/avatar-" + chosen_pic;
        let html_string = "<img src=\"" + pic_path + ".webp\" style=\"width: 256px; height: auto;\" alt=\"" + chosen_pic + "\">";

        target_div.innerHTML = html_string;
    });

    function genString(length) {
        return Math.random().toString(26).substring(2, length + 2);
    }

    document.getElementById("random").addEventListener("click", function() {

    });

    document.getElementById("debug").addEventListener("click", function() {
        const email = document.getElementById("register-email");
        const pw = document.getElementById("register-password");
        const pw2 = document.getElementById("register-password-confirm");
        const charname = document.getElementById("register-character-name");
        const race = document.getElementById("race-select");
        const avatar = document.getElementById("avatar-select");

        email.value = "test" + genString(5) + "@example.com";
        pw.value = genString(10);
        pw2.value = pw.value;
        charname.value = "TestChar" + genString(5);
        race.selectedIndex = parseInt(Math.random() * race.options.length)
        avatar.selectedIndex = parseInt(Math.random() * avatar.options.length)

        var stats = ["str", "def", "int"];

        for (let i = 0; i < 10; i++) {
            let which = stats[Math.floor(Math.random() * 3)];
            stat_adjust(which + '-plus').click();
        }
    });

    document.querySelectorAll("[data-loa=pw_toggle]").forEach((e) => {
        e.addEventListener("click", (ev) => {
            var cur_ele_textbox = ev.target.previousElementSibling;
            var cur_type = cur_ele_textbox.type;

            if (cur_type == 'password') {
                ev.target.textContent = 'Hide';
                cur_ele_textbox.type = 'text';
            } else {
                ev.target.textContent = 'Show';
                cur_ele_textbox.type = 'password';
            }
        });
    });
</script>