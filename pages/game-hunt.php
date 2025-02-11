<?php
    use Game\Monster\Enums\Scope;
    use Game\Monster\Pool;
    require_once "functions.php";

    $monster_pool = new Pool;
    load_monster_sheet($monster_pool);

    [$mon_name, $mon_avatar, $mon_hp, $mon_maxHP, $mon_mp, $mon_maxMP, $mon_str, $mon_int, $mon_def] = [null, null, null, null, null, null, null, null, null];
    $monster = $character->get_monster();
    $mon_loaded = 0;

    if (isset($_POST['hunt-new-monster']) && $_POST['hunt-new-monster'] == 1) {
        $mon_loaded = 1;

        if ($monster === false || $monster === null) {
            $monster   = $monster_pool->random_monster(Scope::PERSONAL, $character->get_id());
            $monster->new(Scope::PERSONAL);
            $monster->load();
            $character->set_monster($monster);

            
        } else {
            $monster = $character->monster;
        }
    }
    $mon_name  = $monster->get_name();
    $mon_hp    = $monster->get_hp();
    $mon_maxHP = $monster->get_maxHP();
    $mon_mp    = $monster->get_mp();
    $mon_maxMP = $monster->get_maxMP();
    $mon_str   = $monster->get_str();
    $mon_int   = $monster->get_int();
    $mon_def   = $monster->get_def();
    $mon_avatar = 'img/enemies/' . $monster->get_name() . '.webp';
?>
    <div class="d-flex pt-3">
        <div class="container border border-1">
            <div class="row">
                <div class="col pt-3">
                    <?php
                        if ($mon_loaded) {
                            echo $monster->get_name();
                            echo '<br><hr><br>';
                            echo "HP: $mon_hp / $mon_maxHP<br>";
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
                    <?php
                        if ($mon_loaded) {
                            echo $character->get_name();
                            echo '<br><hr><br>';
                        } else {
                            echo '-- our stats --';
                        }
                    ?>
                </div>
            </div>
        </div>
        
    
        <div class="container border border-1 w-25">
            <div class="col pt-3">
                <div class="row">
                    <div class="d-flex w-100">
                        <div class="btn-group mb-3 me-1" role="group" aria-label="attack">
                            <button id="hunt-attack-btn" name="hunt-attack-btn" type="button" class="btn btn-sm btn-primary mb-1 flex-fill disabled">
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
                        <button id="hunt-new-monster" name="hunt-new-monster" class="btn btn-sm border-black btn-success flex-fill" type="submit" style="width: calc(100% + 20px);" value="1">Hunt</button>
                        <button id="hunt-global-btn" name="hunt-global-btn" class="btn btn-sm border-black btn-secondary flex-fill disabled" style="width: calc(100% + 20px);">Global</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container w-100 border border-1">
        <div class="row">
            <div class="col">
                woo
            </div>
        </div>
    </div>
</div>


    <script src="/js/battle.js"></script>