<?php
    use Game\Monster\Enums\Scope;
    use Game\Monster\Monster;

    require_once "functions.php";
    

    [$mon_name, $mon_avatar, $mon_hp, $mon_maxHP, $mon_mp, $mon_maxMP, $mon_str, $mon_int, $mon_def] = [null, null, null, null, null, null, null, null, null];
    $monster = $character->get_monster();
    $mon_loaded = 0;

    if ($monster != null && $monster != "") {
        $mon_loaded = 1;
    }

    if (isset($_POST['hunt-new-monster']) && $_POST['hunt-new-monster'] == 1) {
        check_csrf($_POST['csrf-token']);
        if (!$mon_loaded) {
            $monster = new Monster(Scope::PERSONAL);
            
            $monster->new();
            $monster->load(Scope::PERSONAL);
            $monster->random_monster();
            $character->set_monster($monster);
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
        $mon_avatar = '/img/enemies/' . str_replace(' ', '', $monster->get_name()) . '.png';
    }
?>
    <script src="node_modules/smooth-scrollbar/dist/smooth-scrollbar.js" type="text/javascript"></script>

    <div class="d-flex pt-3">
        <div class="container border border-1">
            <div class="row">
                <div id="monster-stats" name="monster-stats" class="col pt-3 lh-1">
                    <?php if ($mon_loaded): ?>
                        <?php echo $monster->get_name(); ?>
                        <?php echo '<br><hr>'; ?>
                        <div class="d-flex justify-content-evenly">
                            <img class="rounded-circle me-2" src="/img/enemies/<?php echo str_replace(' ', '', $monster->get_name()) . '.png';?>" width="150" height="150" />

                            <div class="d-grid align-items-start">
                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">HP</span>
                                    <span id="player-hp" name="player-hp" class="flex-grow-1 text-center"><?php echo $monster->stats->get_hp(); echo ' / '; echo $monster->stats->get_maxHp(); ?></span>
                                </span>

                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">MP</span>
                                    <span class="flex-grow-1 text-center"><?php echo $monster->stats->get_ep(); echo ' / '; echo $monster->stats->get_maxEp(); ?></span>
                                </span>

                                <span class="d-grid align-items-center small">
                                    <span class="flex-grow-1 text-center">EP</span>                                    
                                    <span class="flex-grow-1 text-center"><?php echo $monster->stats->get_mp(); echo ' / '; echo $monster->stats->get_maxMp(); ?></span>
                                    
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
                <div class="col pt-3 lh-1">
                    <?php if ($mon_loaded): ?>
                        <?php echo $character->get_name(); ?>
                        <?php echo '<br><hr>'; ?>
                        <div class="d-flex justify-content-evenly">
                            <img class="rounded-circle me-2" src="/img/avatars/<?php echo $character->get_avatar(); ?>" width="150" height="150" />
                            <div class="d-grid align-items-start">
                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">HP</span>
                                    <span id="player-hp" name="player-hp" class="flex-grow-1 text-center"><?php echo $character->stats->get_hp(); echo ' / '; echo $character->stats->get_maxHp(); ?></span>
                                </span>

                                <span class="d-grid align-items-center small mb-3">
                                    <span class="flex-grow-1 text-center">MP</span>
                                    <span class="flex-grow-1 text-center"><?php echo $character->stats->get_ep(); echo ' / '; echo $character->stats->get_maxEp(); ?></span>
                                </span>

                                <span class="d-grid align-items-center small">
                                    <span class="flex-grow-1 text-center">EP</span>                                    
                                    <span class="flex-grow-1 text-center"><?php echo $character->stats->get_mp(); echo ' / '; echo $character->stats->get_maxMp(); ?></span>
                                    
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
                                <li class="dropdown-item" data-loa-atk="1">Attack</li>
                                <li class="dropdown-item" data-loa-atk="2">Heavy</li>
                                <li class="dropdown-item" data-loa-atk="3">Special</li>
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
                                <li class="dropdown-item" data-loa-spl="1">Burn</li>
                                <li class="dropdown-item" data-loa-spl="2">Frost</li>
                                <li class="dropdown-item" data-loa-spl="3">Heal</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="d-flex w-100">
                        <button class="btn btn-sm me-2 btn-primary border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1">Entice</button>
                        <button class="btn btn-sm btn-primary border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1">Capture</button>
                    </div>
                </div>

                <div class="row mb-3">
                    <form id="new-mon" name="new-mon" action="/game?page=hunt&action=flee&scope=personal" method="post">
                        <div class="d-flex w-100">
                            <button class="btn btn-sm me-2 btn-warning border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1">Steal</button>
                            <button class="btn btn-sm btn-danger  border-black flex-fill" style="width: calc(100% + 20px);" data-loa-monld="1">Flee</button>
                        </div>
                    </form>
                </div>

                <div class="row mb-3">
                    <form id="new-mon" name="new-mon" action="/game?page=hunt&action=hunt&scope=personal" method="post">
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


    <div class="container-fluid border border-1 mb-1 overflow-hidden" style="max-height: 65.0vh!important;">
        <div class="row">
            <div id="battle-log" name="battle-log" class="col lh-1">
                    
            </div>
        </div>
    </div>
</div>

<script>var mon_loaded = <?php echo $mon_loaded; ?>;</script>
<script>var csrf_token = "<?php echo $_SESSION['csrf-token']; ?>";</script>
<script>var player_hp = <?php echo $character->stats->get_hp(); ?>;</script>
<script src="/js/battle.js"></script>