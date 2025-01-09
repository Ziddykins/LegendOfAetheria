<?php
    $account   = new Account($_SESSION['account-id']);
    $character = new Character($_SESSION['character-id']);
    #$character = table_    to_obj($account->get_id(), 'character');
    $monster   = null;
    
    if (!$character->get_monster()) {
        $monster   = $monster_pool->random_monster();
        $mon_name  = $monster->get_name();
        $mon_hp    = $monster->get_hp();
        $mon_maxHP = $monster->get_maxHP();
        $mon_mp    = $monster->get_mp();
        $mon_maxMP = $monster->get_maxMP();
        $mon_str   = $monster->get_strength();
        $mon_int   = $monster->get_intelligence();
        $mon_def   = $monster->get_defense();
        $character->set_monster($monster);
    } else {
        $monster = $character->get_monster();
    }
    $character->get_inventory()->addItem("Rubber Dong", 10, 1);

    echo '<pre>';
    print_r($character);
    exit();
?>

<div class="row row-cols-4 border border-1">
    <div class="col pt-3">
        <img src="img/enemies/enemy-kobold.webp" width="100" height="100" class="rounded-circle"/>
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
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Attack</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Normal</a></li>
                            <!-- Uses 2x energy does up to 2x? dmg -->
                            <li><a class="dropdown-item" href="#">Heavy</a></li>
                            <!-- if enchanted, special -->
                            <li><a class="dropdown-item" href="#">*Special*</a></li>
                        </ul>
                    </div>
                    <div class="d-grid col column-gap-2">    
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Spells</button>
                        <ul class="dropdown-menu">
                            <!-- Iterate through spellbook of learned spells and populate list items -->
                            <li><a class="dropdown-item" href="#">Burn</a></li>
                            <li><a class="dropdown-item" href="#">Burn a bit more</a></li>
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
