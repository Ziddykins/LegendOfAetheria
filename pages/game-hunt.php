<?php

    use Game\Monster\Enums\Scope;
    use Game\Monster\Pool;
    require_once "functions.php";

    $monster_pool = new Pool;
    load_monster_sheet($monster_pool);

    [$mon_name, $mon_hp, $mon_maxHP, $mon_mp, $mon_maxMP, $mon_str, $mon_int, $mon_def] = [null, null, null, null, null, null, null, null];
    $monster = $character->get_monster();

    if ($monster === null) {
        $monster   = $monster_pool->random_monster(Scope::PERSONAL, $character->get_id());
        $mon_name  = $monster->get_name();
        $mon_hp    = $monster->get_hp();
        $mon_maxHP = $monster->get_maxHP();
        $mon_mp    = $monster->get_mp();
        $mon_maxMP = $monster->get_maxMP();
        $mon_str   = $monster->get_str();
        $mon_int   = $monster->get_int();
        $mon_def   = $monster->get_def();
        $avatar = 'img/enemies/' . $monster->get_name() . '.webp';
        $character->set_monster($monster);
    } else {
        $monster = $character->monster;
    }

    $mon_avatar = 'img/enemies/' . preg_replace('/ /', '', $monster->get_name()) . '.png';
    $character->inventory->addItem("Rubber Dong", 10, 1);

?>

<div class="row row-cols-4 border border-1">
    <div class="col pt-3">
        <img src="<?php echo $mon_avatar; ?>" width="100" height="100" class="rounded-circle"/>
    </div>
    <div class="col">
        <div class="row">
            <div class="col">
                Name: <?php echo $mon_name; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                Health: <?php echo "$mon_hp/$mon_maxHP"; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                Magic : <?php echo "$mon_mp/$mon_maxMP"; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                STR: <?php echo $mon_str; ?>
            </div>
            <div class="col">
                DEF: <?php echo $mon_def; ?>
            </div>
            <div class="col">
                INT: <?php echo $mon_int; ?>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="row">
            <div class="col">
                Our Stats
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="row">
            <div class="col border border-1 pt-3">
                <div class="row row-cols-2">
                    <div class="d-grid col column-gap-2">
                        <div class="btn-group" role="group" aria-label="Attack">
                            <button id="hunt-attack-btn" name="hunt-attack-btn" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Attack</button>
                            <button id="hunt-attack-dropdown" name="hunt-attack-dropdown type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Attack</button>
                        </div>

                        
                        <ul class="dropdown-menu">
                            <li><a id="attack-normal" name="attack-normal" class="dropdown-item" href="/page=hunt&action=attack&type=normal">Normal</a></li>
                            <!-- Uses 2x energy does up to 2x? dmg -->
                            <li><a id="attack-heavy" name="attack-heavy" class="dropdown-item" href="#">Heavy</a></li>
                            <!-- if enchanted, special -->
                            <li><a id="attack-special" name="attack-special" class="dropdown-item" href="#">*Special*</a></li>
                        </ul>
                    </div>
                    <div class="d-grid col column-gap-2"> 
                        <div class="btn-group" role="group" aria-label="Spells">
                            <button id="hunt-spell-dropdown" name="hunt-spell-dropdown" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Spells</button>
                            <button>lol</button>
                        </div>
                        <ul class="dropdown-menu">
                            <!-- Iterate through spellbook of learned spells and populate list items -->
                            <li><a id="spell-burn" name="spell-burn" class="dropdown-item" href="#">Burn</a></li>
                            <li><a id="spell-frost" name="spell-burn" class="dropdown-item" href="#">Burn a bit more</a></li>
                        </ul>
                    </div>
                </div>
                
                <p></p>
                
                <div class="row">
                    <div class="d-grid col column-gap-2">
                        <button type="button" class="btn btn-primary">Entice</button>
                    </div>
                    <div class="d-grid col column-gap-2">
                        <button class="btn btn-primary" id="contact-submit" name="contact-submit" value="1">Capture</button>
                    </div>
                </div>
                
                <p></p>
                
                <div class="row">
                    <div class="d-grid col column-gap-2">
                        <button class="btn btn-warning">Steal</button>
                    </div>
                    <div class="d-grid col column-gap-2">
                        <button class="btn btn-danger">Flee</button>
                    </div>
                </div>
                <p></p>
            </div>
            
        </div>


<div class="row border border-1 pt-3 sticky-bottom">
    <div class="col">
        <div class="list-group">
            <div class="row">
                <div class="d-grid col column-gap-2">
                    <button class="btn btn-success">Hunt</button>
                </div>
                <div class="d-grid col column-gap-2">
                    <button class="btn btn-secondary">Global</button>
                </div>
            </div>
            <p></p>
        </div>
    </div>
<!--merge-fuggup, if broken add closing div-->
