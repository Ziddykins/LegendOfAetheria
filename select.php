<?php
declare(strict_types=1);
session_start();
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require_once "bootstrap.php";

$account = null;

if (isset($_REQUEST['create-submit']) && $_REQUEST['create-submit'] == 1) {
    if (isset($_REQUEST['slot']) && $_REQUEST['slot'] > 0 && $_REQUEST['slot'] <= 3) {
        $slot = $_REQUEST['slot'];
        $_REQUEST["select-new-$slot"] = 1;
    }
}

if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 1) {
    $account = new Account($_SESSION['email']);
    $account->load();

    if (isset($_REQUEST)) {
        $action    = null;
        $slot      = null;
        $char_slot = null;

        foreach ($_REQUEST as $key => $value) {
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
                $sql_query = "SELECT `$char_slot` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
                $char_id = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc()["$char_slot"];

                $_SESSION['focused-slot'] = $slot;
                $_SESSION['character-id'] = $char_id;
                header('Location: /game');
                exit();
            case 'new':
                $char_name    = $_REQUEST['create-character-name'];
                $char_race    = validate_race($_REQUEST['race-select']);
                $char_avatar = validate_avatar('avatar-' . $_REQUEST['avatar-select'] . '.webp');
                $str     = $_REQUEST['str-ap'];
                $int     = $_REQUEST['int-ap'];
                $def     = $_REQUEST['def-ap'];
                $next_char_id = getNextTableID($_ENV['SQL_CHAR_TBL']);

                if ($str + $def + $int === MAX_ASSIGNABLE_AP) {
                    /* ya forgin' posts I know it */
                    if (($str < 10 || $def < 10 || $int < 10)) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        write_log(AbuseTypes::POSTMODIFY->name, "New character attributes modified", $ip);

                        if (check_abuse(AbuseTypes::POSTMODIFY, $account->get_id(), $ip, 2)) {
                            ban_user($account->get_id(), 3600, "Post modifications");
                        }
                    }
                }

                $sql_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `$char_slot` = ? WHERE `id` = ?";
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
                $sql_query = "SELECT `$char_slot` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
                $char_id = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc()["$char_slot"];

                /* Clear that slot */
                $sql_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `$char_slot` = NULL WHERE `id` = ?";
                $db->execute_query($sql_query, [ $_SESSION['account-id'] ]);

                /* Delete that character */
                $sql_query = "DELETE FROM {$_ENV['SQL_CHAR_TBL']} WHERE `id` = ?";
                $db->execute_query($sql_query, [ $char_id ]);

                $log->warning(
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
        <div class="container mt-5">
            <div class="d-flex justify-content-center">
                <?php
                for ($i = 1; $i < 4; $i++) {
                    $char_slot = "get_charSlot$i";
                    $char_id   = $account->$char_slot();

                    $card_html = Character::genSelectCard($char_id, $i);
                    echo $card_html;
                }
                ?>
            </div>
        </div>
    </div>
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
        document.querySelectorAll("a[id^='select-delete-']").forEach((ahref_btn) => {
            ahref_btn.addEventListener("click", (e) => {
                if (e.target.classList.contains("btn-outline-danger")) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.target.classList.replace("btn-outline-danger", "btn-danger");
                }
            });
        });
        //const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        //const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    </script>
            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>
                    <!-- Toast placeholder -->
                </div>
            </div>

<?php include "html/footers.html"; ?>    

</body>

</html>