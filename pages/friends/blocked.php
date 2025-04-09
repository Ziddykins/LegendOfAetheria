<?php
    if (check_session()) {
        $blocked = [];
    }
?>
<form id="settings-account" name="settings-account" action="/set_settings.php" method="POST">
                        <div class="row">
                            <div class="col">
                                <h3><?php echo $header_charname; ?> Blocked</h3>
                            </div>
                        </div>
                    </form>