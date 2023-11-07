<?php
    $current_egg = null;

    if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];

        if ($action === 'generate') {
            if ($familiar->get_rarity() !== '') {
                generate_egg($familiar);
                $egg_name = null;

                if (isset($_REQUEST['egg-name'])) {
                    $egg_name = $_REQUEST['egg-name'];
                    $familiar->set_name($egg_name);
                }
                $familiar->saveFamiliar();
            }
            echo '<pre>';
            print_r($familiar);
            echo '</pre>';
            exit();
        }
    }
?>

<div class="container text-center">
    <div class="row pt-5">
        <h3>Eggs</h3>
        <div class="col">
             <?php
                /* No egg received yet */
                if ($familiar->get_name() === '!Unset!') {
                    echo $familiar->getCard('empty');
                } else {
                    echo "You already have an egg :O\n";
                    echo $familiar->getCard();
                }
            ?>
        </div>
    </div>
</div>
