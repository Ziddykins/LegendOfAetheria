<?php
    use Game\Mail\MailBox\MailBox;
    use Game\Mail\Folder\Enums\FolderType;
    
    $user_inbox = new MailBox($character->get_id());
    $user_inbox->setFocusedFolder(FolderType::INBOX);
    $user_inbox->populateFocusedFolder();
    $user_inbox_count = $user_inbox->focusedFolder->getMessageCount();
?>
            <div class="row">
                <div class="col">
                    <h3><?php echo fix_name_header($character->get_name()); ?> Inbox</h3>
                </div>

                <div class="list-group overflow-hidden">
                    <?php echo $user_inbox->focusedFolder->getFolderHTML(); ?>
                </div>
            </div>