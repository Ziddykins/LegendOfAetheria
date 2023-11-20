<?php
    $current_egg = null;

    if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];

        if ($action === 'generate') {
            $rarity_name = $familiar->get_rarity();

            $roll = random_float(0, 12);
            generate_egg($familiar, $roll);
            $egg_name = null;

            if (isset($_REQUEST['egg-name'])) {
                $egg_name = $_REQUEST['egg-name'];
                $familiar->set_name($egg_name);
            }

            echo '<pre>';
            print_r($familiar);
            echo '</pre>';
            $familiar->saveFamiliar();
        }
    }
?>
<!--<script>
    $(document).ready(
        function() {
            $("#footer").append("<! Dynamically loaded content: <?php echo __FILE__; ?>  --");
            $("#footer").append('<script type="text/javascript" src="js/egg_timer.js"></script>');
        }
    );
</script>-->
<div class="container text-center">
    <div class="row pt-5">
        <h3>Eggs</h3>
        <div class="col">
            <?php
                $temp_file_name = '.' . session_id() . '_tmp_egg'; 
                /* No egg received yet */
                if ($familiar->get_rarity() === 'NONE') {        
                    $html = $familiar->getCard('empty');
                    file_put_contents($temp_file_name, $html);
                    include($temp_file_name);
                    unlink($temp_file_name);
                } else {
                    $html = $familiar->getCard();
                    file_put_contents($temp_file_name, $html);
                    include($temp_file_name);
                    unlink($temp_file_name);
                }
            ?>
        </div>
    </div>
</div>
