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

    $account = new Account($_SESSION['email']);

    $sql_query = "SELECT `char_slot1`, `char_slot2`, `char_slot3` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
    $characters = $db->execute_query($sql_query, [$account->get_id()])->fetch_assoc();

    echo '<pre>';
    print_r($characters);
    exit();
    
?>
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#char-select-modal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="char-select-modal" tabindex="-1" aria-labelledby="char-select-modal-label" aria-hidden="true">
    <div class="modal-dialog">
    <?php
        for ($i=1; $i<4; $i++) {
            if ($i == 1) {
                $character = new Character($account->get_id(), 0, $i);
                if ($character->get_slot() > 1) {
                    $character->set_name('Empty');
                    $character->set_avatar('avatar-unknown.jpg');
                }

                $modal_html =  '<div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="char-select-modal-label">Slot ' . $i . '</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col">




                                        <div class="lead text-center">' . $character->get_name() . '</div>
                                        </div>
                                        <div class="col">
                                            <img src="' . $character->get_avatar() . '" class="img img-rounded float-left" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            hey
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Delete</button>
                                <button type="button" class="btn btn-sm btn-primary">Load</button>
                            </div>
                        </div>';
            } else {
                // Handle other cases if needed
            }
            echo $modal_html;
        }
    ?>
    </div>
</div>
                
<script>
    document.getElementById("select-delete-s1").addEventListener("click", (e) => {
        if (e.target.classList.contains("btn-outline-danger")) {
            e.preventDefault();
            e.stopPropagation();
            e.target.classList.replace("btn-outline-danger", "btn-danger");
        }
    });
</script>