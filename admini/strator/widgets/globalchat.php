<?php
    require_once WEBROOT . '/bootstrap.php';
    global $db;
    $messages = [];
    $sql_query = <<<SQL
        SELECT * FROM {$t['chat']}
        WHERE `when` BETWEEN (NOW() - INTERVAL 7 DAY) AND NOW()
        ORDER BY `when` ASC
    SQL;
    $messages = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);
    $chat_html = gen_globalchat_html($messages);
?>
<div class="card direct-chat direct-chat-primary" style="position: relative; left: 0px; top: 0px;">
    <div class="card-header ui-sortable-handle" style="cursor: move;">
        <h3 class="card-title">Global Chat</h3>
        <div class="card-tools">
            <span title="3 New Messages" class="badge badge-primary">3</span>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="bi bi-plus-lg"></i>
            </button>
            <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                <i class="bi bi-dash-lg"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="direct-chat-messages">
            <?php echo $chat_html; ?>
        </div>
    </div>
    <div class="card-footer">
        <form action="#" method="post">
            <div class="input-group">
                <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                <span class="input-group-append">
                    <button type="button" class="btn btn-primary">Send</button>
                </span>
            </div>
        </form>
    </div>
</div>