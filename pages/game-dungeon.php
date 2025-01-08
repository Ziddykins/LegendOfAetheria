<?php
    global $log;

    if ($_REQUEST['action'] === 'reset') {
        $sql_query = 'UPDATE ' . $_ENV['SQL_CHAR_TBL'] . ' SET floor = 1 WHERE id = ' . $account->get_id();
        $db->query($sql_query);

        echo 'Reset';
    } else if ($_REQUEST['action'] === 'challenge') {
        /* TODO: Implement - maybe manipulate hunt layout */
    } else {
        echo 'Unknown action! Click <a href="#">here</a> to continue fighting on floor ' . $character->get_floor();
    }
?>
