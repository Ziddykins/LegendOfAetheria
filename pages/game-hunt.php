<?php

    $account   = get_user($_SESSION['email'], 'account');
    $character = get_user($account['id'], 'character');

    print_r($account);
    print_r($character);
    
?>
<div class="row row-cols-4 border border-1 text-white">
    <div class="col pt-3">
        <img src="img/enemies/enemy-kobold.png" style="width: 25%;"/>
    </div>
    <div class="col">
        <div class="row">
            <div class="col">
                Name: TestEnemy
            </div>
        </div>
        <div class="row">
            <div class="col">
                Health: 100/100
            </div>
        </div>
        <div class="row">
            <div class="col">
                Resist: 492
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
                    <div class="col">    
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Spells</button>
                        <ul class="dropdown-menu">
                            <!-- Iterate through spellbook of learned spells and populate list items -->
                            <li><a class="dropdown-item" href="#">Burn</a></li>
                            <li><a class="dropdown-item" href="#">Burn a bit more</a></li>
                        </ul>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary">Entice</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary" id="contact-submit" name="contact-submit" value="1">Capture</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button class="btn btn-warning">Steal</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-danger">Flee</button>
                    </div>
                </div>
            </div>
        </div>


<div class="row border border-1 pt-3 sticky-bottom">
    <div class="col">
        <div class="list-group">
            list-group-item list-group-item-action list-group-item-secondary
    </div>
</div>
