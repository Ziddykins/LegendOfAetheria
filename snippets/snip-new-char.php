<div class="modal fade" id="create-character-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Create New Character
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="create-form" name="create-form" action="/select" method="POST">
                    <div class="d-flex bg-body-secondary border">
                        <div class="p-2 flex-grow-1">
                            <h6>
                                <i class="bi bi-emoji-laughing-fill"></i> Character
                            </h6>
                        </div>
                    </div>
                
                    <div class="input-group">
                        <span class="input-group-text rounded-0" id="create-icon-character">
                            <i class="bi bi-credit-card-2-front"></i>
                        </span>
                        
                        <div class="form-floating flex-grow-1">
                            <input type="text" class="form-control" id="create-character-name" name="create-character-name" placeholder="character-name" required>
                            <label for="create-character-name">Character Name</label>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <span class="input-group-text rounded-0" id="create-icon-character">
                            <i class="bi bi-droplet"></i>
                        </span>
                
                        <select class="form-select form-select form-control" style="font-size: 1.00rem;" aria-label=".form-select" id="race-select" name="race-select" required>
                            <option value="Select a Race" disabled selected>Select a Race</option>
                            <?php
                                use Game\Character\Enums\Races;
                                foreach (Races::cases() as $race_sel) {
                                    if ($race_sel->name == "Default") {
                                        continue;
                                    }
                                    echo "\n<option value=\"" . $race_sel->name . "\">" . $race_sel->name . "</option>\n";
                                }
                            ?>
                        </select>
                    </div>
                
                    <div class="input-group">
                        <span class="input-group-text rounded-0" id="create-icon-avatar">
                            <i class="bi bi-person-bounding-box"></i>
                        </span>
                
                        <select id="avatar-select" name="avatar-select" class="form-select form-select form-control" aria-label=".form-select" style="font-size: 1.00rem" required>
                            <option value="select avatar" disabled selected>select avatar</option>
                            <?php
                                $images = scandir('img/avatars');
                                for ($i=2; $i<count($images); $i++) {
                                    $split = explode(".", $images[$i]);
                                    $title = explode("avatar-", $split[0]);
                                    $pic_title = $title[1];
                                    $avatar_text =  preg_replace("/avatar\-/", " ", $pic_title);
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
                    
                    <div class="border">
                        <div class="d-flex bg-body-secondary border">
                            <div class="p-2 flex-grow-1"><h6><i class="bi bi-dice-5-fill"></i> Stats</h6></div>
                            <div class="p-2 fw-bold"><i class="bi bi-capslock-fill"></i> AP: </div>
                            <div class="p-2" name="stats-remaining-ap" id="stats-remaining-ap">10</div>
                        </div>
                    </div>
                
                    <div class="d-flex border">
                        <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-heart-arrow"></i> str:</div>
                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick='stat_adjust("str-minus")'>&minus;</a></div>
                        <div class="p-2" id="stats-str-cur">10</div>                                        
                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick='stat_adjust("str-plus")'>&plus;</a>
                        </div>
                    </div>
                    
                    <div class="d-flex border">
                        <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-shield"></i> def:</div>
                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick='stat_adjust("def-minus")'>&minus;</a></div>
                        <div class="p-2" id="stats-def-cur">10</div>
                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick='stat_adjust("def-plus")'>&plus;</a></div>
                    </div>
                
                    <div class="d-flex border">
                        <div class="p-2 fw-bold font-monospace flex-grow-1"><i class="bi bi-stars"></i> int:</div>
                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-danger" onclick='stat_adjust("int-minus")'>&minus;</a></div>
                        <div class="p-2" id="stats-int-cur">10</div>
                        <div class="p-2"><a href="#a" class="link-offset-2 link-underline link-underline-opacity-0 fw-bold link-success" onclick='stat_adjust("int-plus")'>&plus;</a></div>
                    </div>
                    
                    <input type="text" id="str-ap" name="str-ap" hidden />
                    <input type="text" id="def-ap" name="def-ap" hidden />
                    <input type="text" id="int-ap" name="int-ap" hidden />
                    <input type="text" id="slot"   name="slot"   hidden />
                    
                    <p></p>
                    
                    
                
            </div>

            <div class="modal-footer">
                <div class="vstack gap-1 mb-3">
                    <button class="btn btn-success mb-3" id="create-submit" name="create-submit" value="1">
                        <i class="bi bi-clipboard-plus-fill"></i> Create
                    </button>
                </div>
                
                <script>
                    $("#create-submit").on("click", function (e) {
                        document.querySelector("#str-ap").value = document.querySelector("#stats-str-cur").innerHTML;
                        document.querySelector("#def-ap").value = document.querySelector("#stats-def-cur").innerHTML;
                        document.querySelector("#int-ap").value = document.querySelector("#stats-int-cur").innerHTML;
            
                        if (parseInt(document.querySelector("#stats-remaining-ap").innerHTML) > 0) {
                            e.preventDefault();
                            e.stopPropagation();
                            gen_toast("error-ap-toast", "warning", "bi-dice-5-fill", "Unassigned Attribute Points", "Ensure all remaining attribute points are applied");
                        };
            
                        if (document.querySelector("#race-select").selectedIndex == 0) {
                            e.preventDefault();
                            e.stopPropagation();
                            gen_toast("error-race-toast", "warning", "bi-droplet", "Select Race", "You must choose a race first");
                        };
            
                        if (document.querySelector("#avatar-select").selectedIndex == 0 ) {
                            e.preventDefault();
                            e.stopPropagation();
                            gen_toast("error-avatar-toast", "warning", "bi-person-bounding-box", "Select Avatar", "You must choose an avatar first");
                        };
                    });
                </script>
            </form>
            </div>
        </div>
    </div>
</div>
