<?php
    declare(strict_types = 1);
    session_start();
    require 'vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'functions.php';

    require_once 'classes/class-character.php';
    require_once 'classes/class-account.php';

    $account = null;

    if  (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 1) {
        $account = new Account($_SESSION['email']);
        $account->load();
        

        
        if (isset($_REQUEST)) {
            $action = null;
            $slot = null;

            foreach ($_REQUEST as $key => $value) {
               $matches = null;

               if (preg_match('/^select-(load|delete|new)-(\d+)/', $key, $matches)) {
                    $action = $matches[1];
                    $slot   = $matches[2];
                }
            }

            switch ($action) {
                case 'load':
                    $char_slot = "char_slot$slot";

                    /* Get character ID in that slot */
                    $sql_query = "SELECT `$char_slot` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
                    $char_id   = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc()["$char_slot"];

                    $_SESSION['focused-slot'] = $slot;
                    $_SESSION['character-id'] = $char_id;
                    header('Location: /game');
                    exit();
                case 'new':
                    break;
                case 'delete':
                    $char_slot = "char_slot$slot";

                    /* Get character ID in that slot */
                    $sql_query = "SELECT `$char_slot` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
                    $char_id   = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc()["$char_slot"];
            
                    /* Clear that slot */
                    $sql_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `$char_slot` = NULL WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $_SESSION['account-id'] ]);
                    
                    /* Delete that character */
                    $sql_query = "DELETE FROM {$_ENV['SQL_CHAR_TBL']} WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $char_id ]);
                    
                    $log->warning("Character Deleted", [ 
                        'AccountID' => $_SESSION['account-id'],
                        'CharacterID' => $chaR_id,
                        'Slot' => $slot
                    ]);
                    break;
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
        
    </head>
        
    <body class="main-font" data-bs-theme="dark">
        <div class="container mt-5">
            <div class="d-flex justify-content-center">
                <?php
                    for ($i=1; $i<4; $i++) {
                        $char_slot = "get_charSlot$i";
                        $char_id = $account->$char_slot();

                        $card_html = Character::genSelectCard($char_id, $i);
                        echo $card_html;
                    }
                ?>
            </div>
        </div>

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

        <?php include "html/footers.html"; ?>
    </body>
</html>