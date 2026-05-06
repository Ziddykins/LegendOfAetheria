<?php
    use Game\Mail\MailBox\MailBox;
    use Game\Mail\Folder\Enums\FolderType;

    $user_outbox = new MailBox($character->get_id());
    $user_outbox->setFocusedFolder(FolderType::OUTBOX);
    $user_outbox->populateFocusedFolder();
?>
<div class="row">
    <div class="col">
        <h3><?php echo fix_name_header($character->get_name()); ?> Outbox</h3>
    </div>

    <div class="list-group">
        <?php echo $user_outbox->focusedFolder->getFolderHTML(); ?>
    </div>
</div>