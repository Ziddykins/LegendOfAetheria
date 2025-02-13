<?php
    use Game\Monster\Enums\Scope;
    use Game\System\System;
    use Game\Monster\Monster;

    require_once "functions.php";
    

    [$mon_name, $mon_avatar, $mon_hp, $mon_maxHP, $mon_mp, $mon_maxMP, $mon_str, $mon_int, $mon_def] = [null, null, null, null, null, null, null, null, null];
    $monster = $character->get_monster();
    $mon_loaded = 0;

    if ($monster != null && $monster != "") {
        $mon_loaded = 1;
    }

    if (isset($_POST['hunt-new-monster']) && $_POST['hunt-new-monster'] == 1) {
        //check_csrf($_POST['csrf-token']);
        if ($mon_loaded) {
            //modal, flee or kill
        } else {
            $monster = new Monster(Scope::PERSONAL);
            
            $monster->new();
            $monster->load(Scope::PERSONAL);
            $monster->random_monster(Scope::PERSONAL, $monster->get_id());            
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
        $mon_avatar = 'img/enemies/' . $monster->get_name() . '.webp';
    }
?>
    <div class="d-flex pt-3">
        <div class="container border border-1">
            <div class="row">
                <div class="col pt-3">
                    <?php
                        if ($mon_loaded) {
                            echo $monster->get_name();
                            echo '<br><hr><br>';
                            echo "HP : $mon_hp / $mon_maxHP<br>";
                            echo "MP : $mon_mp / $mon_maxMP<br>";
                            echo "STR: $mon_str - DEF: $mon_def - INT: $mon_int<br>";
                        } else {
                            echo '-- No Monster --';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="container border border-1 ">
            <div class="row">
                <div class="col">
                    <?php if ($mon_loaded): ?>
                        <?php echo $character->get_name(); ?>
                        <?php echo '<br><hr><br>'; ?>
                        <span class="text-danger">HP :</span>  <?php echo $mon_hp; echo ' / '; echo $mon_maxHP; ?>
                        <span class="text-primary">MP :</span> <?php echo $mon_mp; echo ' / '; echo $mon_maxMP; ?>
                    <?php else: ?>
                    
                            
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    
        <div class="container border border-1 w-25">
            <div class="col pt-3">
                <div class="row">
                    <div class="d-flex w-100">
                        <div class="btn-group mb-3 me-1" role="group" aria-label="attack">
                            <button id="hunt-attack-btn" name="hunt-attack-btn" type="button" class="btn btn-sm btn-primary mb-1 flex-fill">
                                Attack
                            </button>
                    
                            <button id="hunt-attack-drop" name="hunt-attack-drop" type="button" class="btn btn-sm btn-primary mb-1 dropdown-toggle dropdown-toggle-split flex-fill disabled" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 20px;">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                        
                            <ul id="attack-drop-menu" class="dropdown-menu">
                                <li class="dropdown-item" data-loa-atk="1">Attack</li>
                                <li class="dropdown-item" data-loa-atk="2">Heavy</li>
                                <li class="dropdown-item" data-loa-atk="3">Special</li>
                            </ul>
                        </div>

                        <div class="btn-group mb-3" role="group" aria-label="spells">
                            <button id="hunt-spells-btn" name="hunt-spells-btn" type="button" class="btn btn-sm btn-primary mb-1 flex-fill disabled">
                                Spells
                            </button>
                    
                            <button id="hunt-spells-drop" name="hunt-spells-drop" type="button" class="btn btn-sm btn-primary mb-1 dropdown-toggle dropdown-toggle-split flex-fill disabled" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 20px;">
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
                        <button class="btn btn-sm btn-primary border-black flex-fill disabled" style="width: calc(100% + 20px);">Entice</button>
                        <button class="btn btn-sm btn-primary border-black flex-fill disabled" style="width: calc(100% + 20px);">Capture</button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="d-flex w-100">
                        <button class="btn btn-sm btn-warning border-black flex-fill disabled" style="width: calc(100% + 20px);">Steal</button>
                        <button class="btn btn-sm btn-danger  border-black flex-fill disabled" style="width: calc(100% + 20px);">Flee</button>
                    </div>
                </div>

                <div class="row mb-3">
                    <form id="new-mon" name="new-mon" action="/game?page=hunt&action=hunt&scope=personal" method="post">
                        <div class="d-flex w-100">
                            <button id="hunt-new-monster" name="hunt-new-monster" class="btn btn-sm border-black btn-success flex-fill" style="width: calc(100% + 20px);" type="submit" value="1">Hunt</button>
                            <button id="hunt-global-btn" name="hunt-global-btn" class="btn btn-sm border-black btn-secondary flex-fill disabled" style="width: calc(100% + 20px);">Global</button>
                            <input id="csrf-token" name="csrf-token" type="hidden" value="<?php echo $_SESSION['csrf-token']; ?>" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid border border-1">
        <div class="row">
            <div id="battle-log" name="battle-log" class="col">

            </div>
        </div>
    </div>
</div>


    <script src="/js/battle.js"></script>