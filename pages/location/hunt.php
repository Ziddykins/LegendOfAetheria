<?php
    use Game\Monster\Enums\MonsterScope;
    use Game\Monster\Monster;

    require_once "functions.php";
    
    $character->stats->set_ep(10000);
    [$mon_name, $mon_avatar, $mon_hp, $mon_maxHP, $mon_mp, $mon_maxMP, $mon_str, $mon_int, $mon_def] = [null, null, null, null, null, null, null, null, null];
    $monster = $character->get_monster();
    $mon_loaded = 0;

    if ($monster->stats->get_hp() <= 0) {
        $monster = null;
    }

    if ($monster != null && $monster != "") {
        $mon_loaded = 1;
    }

    if (isset($_POST['hunt-new-monster']) && $_POST['hunt-new-monster'] == 1) {
        check_csrf($_POST['csrf-token']);
        if (!$mon_loaded) {
            $monster = new Monster(MonsterScope::PERSONAL);
            
            $monster->new();
            $monster->load(MonsterScope::PERSONAL);
            $monster->random_monster($character->get_level());
            $character->set_monster($monster);
            $mon_loaded = 1;
        }
    }

    if (isset($_POST['flee-monster']) && $_POST['flee-monster'] == 1) {
        check_csrf($_POST['csrf-token']);
        if ($mon_loaded) {
            $character->set_monster(null);
        }
    }



    if ($mon_loaded) {
        $mon_name  = $monster->get_name();
        $mon_hp    = $monster->stats->get_hp();
        $mon_maxHP = $monster->stats->get_maxHP();
        $mon_mp    = $monster->stats->get_mp();
        $mon_maxMP = $monster->stats->get_maxMP();
        $mon_str   = $monster->stats->get_str();
        $mon_int   = $monster->stats->get_int();
        $mon_def   = $monster->stats->get_def();
        $mon_dl    = $monster->get_dropLevel();
        $mon_avatar = '/img/enemies/' . str_replace(' ', '', $monster->get_name()) . '.png';
    }
?>
    <link rel="stylesheet" href="/css/battle-animations.css">
    <div class="d-flex pt-3">
        <div class="container border border-1">
            <div class="row">
                <div id="monster-stats" name="monster-stats" class="col pt-3 lh-1">
                    <?php if ($mon_loaded): ?>
                        <?php echo $monster->get_name(); ?>
                        <?php echo '<br><hr>'; ?>
                        <div class="d-flex justify-content-evenly">
                            <img class="rounded-circle me-2" src="/img/enemies/<?php echo str_replace(' ', '', $monster->get_name()) . '.png';?>" width="150" height="150" data-entity="monster" />

                            <div class="d-grid align-items-start">
                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">HP</span>
                                    <span id="monster-hp" name="monster-hp" class="flex-grow-1 text-center"><?php echo "$mon_hp / $mon_maxHP"; ?></span>
                                </span>

                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">MP</span>
                                    <span id="monster-mp" name="monster-mp" class="flex-grow-1 text-center"><?php echo "$mon_mp / $mon_maxMP"; ?></span>
                                </span>

                                <span c7lass="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">Drop Lv.</span>                                    
                                    <span class="flex-grow-1 text-center"><?php echo $mon_dl; ?></span>
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                            echo '-- No Monster --';
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="container border border-1 ">
            <div class="row">
                <div class="col pt-3 lh-1 text-center">
                    <?php if ($mon_loaded): ?>
                        <?php echo $character->get_name(); ?>
                        <?php echo '<br><hr>'; ?>
                        <div class="d-flex justify-content-evenly">
                            <img class="rounded-circle me-2 mb-3" src="/img/avatars/<?php echo $character->get_avatar(); ?>" width="150" height="150" />
                            <div class="d-grid align-items-start">
                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">HP</span>
                                    <span id="player-hp" name="player-hp" class="flex-grow-1 text-center"><?php echo $character->stats->get_hp(); echo ' / '; echo $character->stats->get_maxHP(); ?></span>
                                </span>

                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">MP</span>
                                    <span id="player-mp" name="player-mp" class="flex-grow-1 text-center"><?php echo $character->stats->get_mp(); echo ' / '; echo $character->stats->get_maxMP(); ?></span>
                                </span>

                                <span class="d-grid align-items-center small">
                                    <span class="flex-grow-1 text-center">EP</span>                                    
                                    <span id="player-ep" name="player-ep" class="flex-grow-1 text-center"><?php echo $character->stats->get_ep(); echo ' / '; echo $character->stats->get_maxEP(); ?></span>
                                    
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        // lol
                    <?php endif; ?> 
                </div>
            </div>
        </div>
        
    
        <div class="container border border-1 w-25">
            <div class="col pt-3">
                <div class="row">
                    <div class="d-flex">
                        <div class="btn-group mb-3 me-1" role="group" aria-label="attack">
                            <button id="hunt-attack-btn" name="hunt-attack-btn" type="button" class="btn btn-sm btn-primary mb-1 flex-fill" data-loa-monld="1">
                                Attack
                            </button>

                            <button id="hunt-attack-drop" name="hunt-attack-drop" type="button" class="btn btn-sm btn-primary mb-1 dropdown-toggle dropdown-toggle-split flex-fill" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 20px;" data-loa-monld="1">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                        
                            <ul id="attack-drop-menu" class="dropdown-menu">
                                <li class="dropdown-item" data-loa-atk="1" data-attack-type="physical">Attack</li>
                                <li class="dropdown-item" data-loa-atk="2" data-attack-type="heavy">Heavy</li>
                                <li class="dropdown-item" data-loa-atk="3" data-attack-type="special">Special</li>
                            </ul>
                        </div>

                        <div class="btn-group mb-3" role="group" aria-label="spells">
                            <button id="hunt-spells-btn" name="hunt-spells-btn" type="button" class="btn btn-sm btn-primary mb-1 flex-fill" data-loa-monld="1">
                                Spells
                            </button>
                    
                            <button id="hunt-spells-drop" name="hunt-spells-drop" type="button" class="btn btn-sm btn-primary mb-1 dropdown-toggle dropdown-toggle-split flex-fill" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 20px;" data-loa-monld="1">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                        
                            <ul id="spells-drop-menu" class="dropdown-menu">
                                <li class="dropdown-item" data-loa-spl="1" data-spell-type="burn">Burn</li>
                                <li class="dropdown-item" data-loa-spl="2" data-spell-type="frost">Frost</li>
                                <li class="dropdown-item" data-loa-spl="3" data-spell-type="heal">Heal</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="d-flex w-100">
                        <button class="btn btn-sm me-2 btn-primary border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1">
                            Entice
                        </button>
                        
                        <button class="btn btn-sm btn-primary border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1">
                            Capture
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="d-flex w-100">
                        <button class="btn btn-sm me-2 btn-warning border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1" onclick=update_hud()>
                            Steal
                        </button>
                        
                        <button id="flee-monster" name="flee-monster" class="btn btn-sm btn-danger  border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1">
                            Flee
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <form id="new-mon" name="new-mon" action="/game?page=hunt&sub=location&action=hunt&scope=personal" method="post">
                        <div class="d-flex w-100">
                            <button id="hunt-new-monster" name="hunt-new-monster" class="btn btn-sm me-2 border-black btn-success flex-fill" type="submit" value="1" data-loa-monld="0">Hunt</button>
                            <button id="hunt-global-btn" name="hunt-global-btn" class="btn btn-sm border-black btn-secondary flex-fill" data-loa-monld="0">Global</button>
                            <input id="csrf-token" name="csrf-token" type="hidden" value="<?php echo $_SESSION['csrf-token']; ?>" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid border border-1 mb-1 overflow-hidden" style="max-height: 65.0vh!important; height: 65.0vh;">
        <div class="d-flex" style="height: 65.0vh;">
            <div id="battle-log" name="battle-log" class="lh-1 flex-fill flex-row-reverse h-100">
                    
            </div>
        </div>
    </div>
</div>

<script>var mon_loaded = <?php echo $mon_loaded; ?>;</script>
<script>var csrf_token = "<?php echo $_SESSION['csrf-token']; ?>";</script>
<script src="/js/battle.js"></script>