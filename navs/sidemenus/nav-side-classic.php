<?php 
    use Game\Character\Enums\FriendStatus;
    use Game\Account\Enums\Privileges;
    use Game\Components\Modals\Enums\ModalButtonType;
?>

<div class="col-2 px-3 border border-grey">
                    <div class="d-flex flex-column flex-shrink-0 bg-body-tertiary">
                        <a href="/game" class="pb-3 text-white text-decoration-none">
                            <img src="img/logos/logo-banner-no-bg.webp" class="mt-2 w-100">
                        </a>

                        <hr style="width: 35%; opacity: .25; align-self: center;" />

                        <div class="d-flex flex-column" style="min-width:16px;">
                            <ul class="nav nav-flush flex-column mb-auto" id="menu">
                                <li class="border w-100">
                                    <a href="#menu-header-character" id="menu-anchor-character" name="menu-anchor-character" class="nav-link bg-primary active text-lg-start text-center" data-bs-toggle="collapse" aria-expanded="true">
                                        <i class="fs-5 bi <?php echo $char_menu_icon; ?> d-lg-inline w-100 text-center text-lg-start"></i>
                                        <span class="d-none d-xxl-inline text-start">Character</span>
                                    </a>
                                    
                                    <ul id="menu-header-character" class="nav collapse nav-flush flex-column" data-bs-parent="#menu" aria-expanded="true">
                                        <li>
                                            <a href="?page=sheet" id="menu-sub-sheet" name="menu-sub-sheet" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">account_circle</span>
                                                <span class="d-none d-lg-inline"> Sheet</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="?page=inventory" id="menu-sub-inventory" name="menu-sub-inventory" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">deployed_code</span>
                                                <span class="small d-none d-lg-inline"> Inventory</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="?page=" id="menu-sub-skills" name="menu-sub-skills" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">book_2</span>
                                                <span class="d-none d-lg-inline">Skills</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="?page=" id="menu-sub-spells" name="menu-sub-spells" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">book</span>
                                                <span class="d-none d-lg-inline">Spells</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="?page=" id="menu-sub-train" name="menu-sub-trail" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">fitness_center</span>
                                                <span class="d-none d-lg-inline">Train</span>
                                            </a>
                                        </li>
                                        <script>
                                            function saveChar() {
                                                preventDefault();
                                                $.ajax("/?page=save").done(
                                                    function(data) {
                                                        $("#output").innerHTML = data;
                                                    }
                                                ).fail(
                                                    function() {
                                                        alert('fail');
                                                    }
                                                );
                                            }
                                        </script>

                                        <li>
                                            <a href="#" id="menu-sub-save" name="menu-sub-save" class="nav-link d-lg-inline text-center" onclick=saveChar()>
                                                <span class="material-symbols-sharp">save</span>
                                                <span class="d-none d-lg-inline">Save</span>
                                            </a>
                                        </li>

                                    </ul>
                                </li>

                                <li class="border w-100">
                                    <a href="#menu-header-familiar" id="menu-anchor-familiar" name="menu-anchor-familiar" class="nav-link text-lg-start text-center" data-bs-toggle="collapse">
                                        <span class="material-symbols-sharp d-lg-inline w-100 text-center text-lg-start">pets</span>
                                        <span class="d-none d-xxl-inline text-start">Familiar</span>
                                    </a>
                                
                                    <ul id="menu-header-familiar" class="collapse nav flex-column text-start"  data-bs-parent="#menu" aria-expanded="false">
                                        <li>
                                            <a href="?page=eggs" id="menu-sub-eggs" name="menu-sub-eggs" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">egg</span>
                                                <span class="d-none d-lg-inline">Hatchery</span>
                                            </a>

                                        </li>
                                    </ul>
                                </li>

                                <li class="border w-100">
                                    <a href="#menu-header-travel" id="menu-anchor-location" name="menu-anchor-location" class="nav-link text-lg-start text-center" data-bs-toggle="collapse">
                                        <i class="fs-5 bi bi-signpost-split-fill d-lg-inline w-100 text-center text-lg-start"></i>
                                        <span class="d-none d-xxl-inline text-start">Location</span>
                                    </a>
                                
                                    <ul id="menu-header-travel" name="menu-header-travel" class="collapse nav flex-column text-start" data-bs-parent="#menu" aria-expanded="false">
                                        <li>
                                            <a href="?page=hunt" id="menu-sub-hunt" name="menu-sub-hunt" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">cruelty_free</span>
                                                <span class="d-none d-lg-inline">Hunt</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-map" name="menu-sub-map" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">map</span>
                                                <span class="d-none d-lg-inline">Map</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-explore" name="menu-sub-explore" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">travel_explore</span>
                                                <span class="d-none d-lg-inline">Explore</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-zone" name="menu-sub-zone" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">move_location</span>
                                                <span class="d-none d-lg-inline">Zone</span>
                                            </a>
                                        </li>
                                        <li>
                                            <?php
                                                $rest_disabled = '';
                                                if ($character->stats->get_hp() === $character->stats->get_maxHP()) {
                                                    $rest_disabled = 'disabled';
                                                } else {
                                                    $rest_disabled = '';
                                                }
                                            ?>
                                            <a href="?page=" id="menu-sub-rest" name="menu-sub-rest" class="nav-link <?php echo $rest_disabled; ?> d-lg-inline text-center">
                                                <span class="material-symbols-sharp">nights_stay</span>
                                                <span class="d-none d-lg-inline">Rest</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="border w-100">
                                    <a href="#menu-header-dungeon" id="menu-anchor-dungeon" name="menu-anchor-dungeon" class="nav-link text-lg-start text-center" data-bs-toggle="collapse">
                                        <i class="fs-4 bi bi-bricks d-lg-inline w-100 text-center text-lg-start"></i>
                                        <span class="d-none d-xxl-inline text-start fs-6">Dungeon</span>
                                    </a>

                                    <ul id="menu-header-dungeon" name="menu-header-dungeon" class="collapse nav flex-column text-start" data-bs-parent="#menu" aria-expanded="false">
                                        <li>
                                            <a href="?page=" id="menu-sub-floor" name="menu-sub-floor" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">floor</span>
                                                <span class="d-none d-lg-inline">Floor <?php echo $character->get_floor(); ?></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" id="menu-sub-reset" name="menu-sub-reset" class="nav-link d-lg-inline text-center" data-bs-toggle="modal" data-bs-target="#reset-modal" >
                                                <span class="material-symbols-sharp text-danger">restart_alt</span>
                                                <span class="d-none d-lg-inline text-danger">Reset</span>
                                            </a>

                                            <?php
                                                $modal_body = 'This will reset your dungeon progress from floor ' .
                                                              $cur_floor . ' to floor 1 and return your ' .
                                                              'dungeon multiplier back to 1x<br /><br /><strong>' .
                                                              'Are you sure? This cannot be reversed!</strong>';
                                                echo generate_modal('reset-dungeon', 'danger', 'Reset Dungeon Progress', $modal_body, ModalButtonType::YESNO);

                                            ?>
                                        </li>
                                    </ul>
                                </li>

                                <li class="border w-100">
                                    <a href="#menu-header-quests" id="menu-anchor-quests" name="menu-anchor-quests" class="nav-link text-lg-start text-center" data-bs-toggle="collapse">
                                        <i class="fs-4 bi bi-clipboard-fill d-lg-inline w-100 text-center text-lg-start"></i>
                                        <span class="d-none d-xxl-inline text-start">Quests</span>
                                    </a>

                                    <ul id="menu-header-quests" name="menu-header-quests" class="collapse nav flex-column text-start" data-bs-parent="#menu">
                                        <li>
                                            <a href="?page=" id="menu-sub-active" name="menu-sub-active" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">pending_actions</span>
                                                <span class="d-none d-lg-inline">Active</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-completed" name="menu-sub-completed" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">inventory</span>
                                                <span class="d-none d-lg-inline">Completed</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=achievements" id="menu-sub-achievements" name="menu-sub-achievements" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">military_tech</span>
                                                <span class="d-none d-lg-inline">Achievements</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-awards" name="menu-sub-awards" class="nav-link d-lg-inline text-center">
                                                <span class="material-symbols-sharp">trophy</span>
                                                <span class="d-none d-lg-inline">Awards</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>    

                        
                        <hr style="width: 35%; opacity: .25; align-self: center;">
                        <div id="bottom-menu" name="bottom-menu" class="d-flex align-items-center ms-3 pb-3 fixed-bottom">
                            <a href="#offcanvas-summary" class="d-flex align-items-center text-decoration-none" id="dropdownUser1" data-bs-toggle="offcanvas" aria-expanded="false" role="button" aria-controls="offcanvas-summary">    
                                <span><img src="img/avatars/<?php echo $character->get_avatar(); ?>" alt="avatar" width="50" height="50" class="rounded-circle" /></span>
                            </a>
                            
                            <a href="#" class="text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-none d-md-inline mx-1 ms-3 fs-6">Account</span>
                            </a>
                        
                            <ul class="dropdown-menu dropdown-menu text-small shadow">
                                <li>
                                    <a class="dropdown-item" href="?page=profile">Profile</a>
                                    <ul class="dropdown-menu dropdown-menu text-small shadow">
                                        <li>
                                            <a class="dropdown-item" href="?page=profile">Profile</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?page=friends">Friends
                                    <?php
                                        $posts = 0;
                                        $posts = get_friend_counts(FriendStatus::REQUEST_RECV);
                                        $pill_bg  = 'bg-danger';

                                        if (!$posts) {
                                            $pill_bg = 'bg-primary';
                                        }
                                    ?>
    <span class="badge <?php echo $pill_bg; ?> rounded-pill"> <?php echo $posts; ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?page=mail">Mail
                                        <?php
                                            $unread_mail = check_mail('unread');
                                            $pill_bg = 'bg-danger';
        
                                            if ($unread_mail == 0) {
                                                    $pill_bg = 'bg-primary';
                                            }
                                        ?>
<span class="badge <?php echo $pill_bg; ?> rounded-pill"> <?php echo $unread_mail; ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?page=settings">Settings</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <?php
                                    $privileges = Privileges::name_to_value($account->get_privileges());
                                            
                                    if ($privileges > Privileges::ADMINISTRATOR->value) {
                                        $href = $_ENV['ADMIN_PANEL'];
                                        echo "<li>\n\t\t\t\t\t\t\t\t\t";
                                    
                                        echo "<a class=\"dropdown-item\" href=\"$href\">Administrator</a>";
                                        echo "\n\t\t\t\t\t\t\t\t</li>\n";
                                    }
                                ?>
                                
                                <li>
                                    <a class="dropdown-item" href="/select">Characters</a>
                                </li>
                                        
                                <li>
                                    <a class="dropdown-item" href="/logout">Sign out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>