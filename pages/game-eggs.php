<?php
    $current_egg = null;

    if (isset($_REQUEST['generate-egg']) && $_REQUEST['generate-egg'] === 1) {
        if ($familiar->get_rarity() === '') {
            generate_egg($familiar);
            $familiar->saveFamiliar();
        }
    }

    
?>

<div class="container">
    <div class="row pt-5">
        <div class="col">
<?php
    echo print_r($familiar);
                if ($familiar->get_rarity()) {
                    echo "You don't have any eggs. Each new player is granted one egg. Don't eat it.\n";
                    echo $familiar->getCard('current');
                } else {
                    echo "You already have an egg :O\n";
                    echo $familiar->getCard();
                }
            ?>
        </div>
        
        <div class="col-8 pt-2">
            <div class="row text-center">
                <div class="col">
                    <h3><?php echo $_SESSION['name']; ?>'s Profile</h3>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="list-eggs" role="tabpanel" aria-labelledby="list-eggs-list">

                </div>

                <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">

                </div>

                <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
                
                </div>

                <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">

                </div>
            </div>
        </div>
    </div>
</div>
