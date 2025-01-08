<?php
    $account   = new Account($_SESSION['email']);
    $character = table_to_obj($account['id'], 'character');
    $monster   = null;
    
    if (!$character['monster']) {
        $monster   = $monster_pool->random_monster();
        $name = $monster->get_name();
        $hp = $monster->get_hp();
        $maxHP = $monster->get_maxHP();
        $mp = $monster->get_mp();
        $maxMP = $monster->get_maxMP();
        $str = $monster->get_strength();
        $int = $monster->get_intelligence();
        $def = $monster->get_defense();
    } else {
        $monster = $character->get_monster();
    }
?>

<div class="row row-cols-4 border border-1">
    <div class="col pt-3">
        <img src="img/enemies/enemy-kobold.webp" width="100" height="100" class="rounded-circle"/>
    </div>
    <div class="col">
        <div class="row">
            <div class="col">
                Name: <?php echo $name; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                Health: <?php echo "$hp/$maxHP"; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                Magic : <?php echo "$mp/$maxMP"; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                STR: <?php echo $str; ?>
            </div>
            <div class="col">
                DEF: <?php echo $def; ?>
            </div>
            <div class="col">
                INT: <?php echo $int; ?>
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
