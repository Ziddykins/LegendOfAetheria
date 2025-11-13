<?php

require_once "system/constants.php";
require_once "system/bootstrap.php";

use Game\Account\Account;
use Game\Character\Character;
use Game\System\Enums\AbuseType;
use Game\Components\Cards\CharacterSelect\CharacterSelectCard;
$log->debug("WE MADE IT TO SELECT");


if (check_session()) {
    $account   = new Account($_SESSION['email']);
    $character = new Character($account->get_id());

    if (isset($_POST['create-submit']) && $_POST['create-submit'] == 1) {
        if (isset($_POST['slot']) && $_POST['slot'] > 0 && $_POST['slot'] <= 3) {
            $slot = $_POST['slot'];
            $_POST["select-new-$slot"] = 1;
        }
    }
    
    if (isset($_POST)) {
        $action    = null;
        $slot      = null;
        $char_slot = null;

        foreach ($_POST as $key => $value) {
            $matches = null;

            if (preg_match('/^select-(load|delete|new)-(\d+)/', $key, $matches)) {
                $action = $matches[1];
                $slot   = $matches[2];
            }
        }

        $char_slot = "char_slot$slot";

        switch ($action) {
            case 'load':
                /* Get character ID in that slot */
                $sql_query = "SELECT `$char_slot` FROM {$t['accounts']} WHERE `id` = ?";
                $char_id = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc()["$char_slot"];

                $character = new Character($account->get_id(), $char_id);

                $_SESSION['focused-slot'] = (int)$slot;
                $_SESSION['character-id'] = (int)$char_id;
                $_SESSION['name'] = $character->get_name();
                header('Location: /game?page=sheet&sub=character');
                exit();
            case 'new':
                $char_name   = preg_replace('/[^a-zA-Z0-9_-]+/', '', $_POST['create-character-name']);
                $char_race   = validate_race($_POST['race-select']);
                $char_avatar = validate_avatar('avatar-' . $_POST['avatar-select'] . '.webp');

                $str     = $_POST['str-ap'];
                $int     = $_POST['int-ap'];
                $def     = $_POST['def-ap'];
                $next_char_id = getNextTableID($t['characters']);

                if ($str + $def + $int === STARTING_ASSIGNABLE_AP) {
                    /* ya forgin' posts I know it */
                    if (($str < 10 || $def < 10 || $int < 10)) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        write_log(AbuseType::TAMPERING->name, "New character attributes modified", $ip);

                        if (check_abuse(AbuseType::TAMPERING, $account->get_id(), $ip, 2)) {
                            ban_user($account->get_id(), 3600, "Post modifications");
                        }
                    }
                }

                $sql_query = "UPDATE {$t['accounts']} SET `$char_slot` = ? WHERE `id` = ?";
                $db->execute_query($sql_query, [ $next_char_id, $account->get_id() ]);

                $character = new Character($account->get_id());
                $character->new($slot);

                $character->set_name($char_name);
                $character->set_race($char_race);
                $character->set_avatar($char_avatar);

                $character->stats->set_int($int);
                $character->stats->set_str($str);
                $character->stats->set_def($def);

                header('Location: /select');
                exit();
            case 'delete':
                /* Get character ID in that slot */
                $sql_query = "SELECT `$char_slot` FROM {$t['accounts']} WHERE `id` = ?";
                $char_id = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc()["$char_slot"];

                /* Clear that slot */
                $sql_query = "UPDATE {$t['accounts']} SET `$char_slot` = NULL WHERE `id` = ?";
                $db->execute_query($sql_query, [ $_SESSION['account-id'] ]);

                /* Delete that character */
                $sql_query = "DELETE FROM {$t['characters']} WHERE `id` = ?";
                $db->execute_query($sql_query, [ $char_id ]);

                $log->info(
                    "Character Deleted",
                    [
                        'AccountID'   => $_SESSION['account-id'],
                        'CharacterID' => $char_id,
                        'Slot'        => $slot
                    ]
                );

                header('Location: /select');
                exit();
        }
    }
} else {
    $log->debug("WE DIIIED cuz session. in select.");
    header('Location: /?no_login');
    exit();
}
?>

<?php include('html/opener.html'); ?>

<head>
    <?php include('html/headers.html'); ?>
    <link rel="stylesheet" href="css/select.css">
</head>

<body class="main-font" data-bs-theme="dark">
    <div class="d-flex align-items-center min-vh-100" style="min-width: 325px;">
        <div class="container">
            <div class="d-flex justify-content-center">
                <?php

                for ($i = 1; $i < 4; $i++) {
                    $char_slot = "get_charSlot$i";
                    $char_id   = $account->$char_slot();
                    $card = new CharacterSelectCard($char_id, $i);
                    echo $card->render();
                }
                ?>
            </div>
        </div>
    </div>

    <span class="d-grid sticky-bottom w-100">
        <a href="/logout"><button id="signout-button" name="signout-button" type="button" role="button" class="btn btn-sm btn-dark bg-gradient">Sign Out</button></a>
    </span>

<script>
        document.querySelectorAll("button[id^='select-new-']").forEach((button) => {
            button.addEventListener("click", (e) => {
                let slot = button.id.split("-")[2];
                document.getElementById("slot").value = slot;
            });
        });
    </script>
    
    <?php include "snippets/snip-new-char.php"; ?>

    <script>
        document.querySelectorAll("button[id^='select-delete-']").forEach((ahref_btn) => {
            ahref_btn.addEventListener("click", (e) => {
                if (e.target.classList.contains("btn-outline-danger")) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.target.classList.replace("btn-outline-danger", "btn-danger");
                }
            });
        });
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    </script>

    <?php include "html/footers.html"; ?>    

</body>

</html>