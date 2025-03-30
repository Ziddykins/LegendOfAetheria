<?php

use Game\Account\Account;
use Game\Character\Character;
use Game\Character\Enums\FriendStatus;
use Game\Mail\MailBox\MailBox;
use Game\Mail\Folder\Enums\FolderType;

$account   = new Account($_SESSION['email']); 
$character = new Character($account->get_id(), $_SESSION['character-id']); 
$character->load();

$user_outbox = new MailBox($character->get_id());
$user_outbox->setFocusedFolder(FolderType::OUTBOX);
$user_outbox->populateFocusedFolder();
$user_outbox_count = $user_outbox->focusedFolder->getMessageCount();

$user_inbox = new MailBox($character->get_id());
$user_inbox->setFocusedFolder(FolderType::INBOX);
$user_inbox->populateFocusedFolder();
$user_inbox_count = $user_inbox->focusedFolder->getMessageCount();



if ($mutual_friends['count'] > 0) {
    $mutual_friends = $mutual_friends['emails'];
}

?>

<div class="container text-white">
    <div class="row pt-5 mb-3">
        <div class="col">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active" id="list-mail-inbox" data-bs-toggle="list" href="#list-inbox" role="tab" aria-controls="list-inbox">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-arrow-up" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4.5a.5.5 0 0 1-1 0V5.383l-7 4.2-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h5.5a.5.5 0 0 1 0 1H2a2 2 0 0 1-2-1.99V4Zm1 7.105 4.708-2.897L1 5.383v5.722ZM1 4v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1Z"/>
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.354-5.354 1.25 1.25a.5.5 0 0 1-.708.708L13 12.207V14a.5.5 0 0 1-1 0v-1.717l-.28.305a.5.5 0 0 1-.737-.676l1.149-1.25a.5.5 0 0 1 .722-.016Z"/>
                </svg> Inbox (<?php echo $user_inbox_count; ?>)
                </a>

                <a class="list-group-item list-group-item-action" id="list-mail-outbox" data-bs-toggle="list" href="#list-outbox" role="tab" aria-controls="list-outbox">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-arrow-down" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4.5a.5.5 0 0 1-1 0V5.383l-7 4.2-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h5.5a.5.5 0 0 1 0 1H2a2 2 0 0 1-2-1.99V4Zm1 7.105 4.708-2.897L1 5.383v5.722ZM1 4v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1Z"/>
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.354-1.646a.5.5 0 0 1-.722-.016l-1.149-1.25a.5.5 0 1 1 .737-.676l.28.305V11a.5.5 0 0 1 1 0v1.793l.396-.397a.5.5 0 0 1 .708.708l-1.25 1.25Z"/>
                </svg> Outbox (<?php echo $user_outbox_count; ?>)
                </a>

                <a class="list-group-item list-group-item-action" id="list-mail-deleted" data-bs-toggle="list" href="#list-deleted" role="tab" aria-controls="list-deleted">
                    <i class="bi bi-envelope-dash"></i> Deleted
                </a>

                <a class="list-group-item list-group-item-action" id="list-mail-drafts" data-bs-toggle="list" href="#list-drafts" role="tab" aria-controls="list-drafts">
                    <i class="bi bi-envelope-paper"></i> Drafts
                </a>

                <div class="d-grid">
                    <button id="list-mail-compose" class="bg-dark bg-gradient list-group-item list-group-item-action" role="button" data-bs-toggle="list" href="#list-compose" role="tab" aria-controls="list-compose">
                        Compose
                    </button>
                    <div id="status" class="small"></div>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="list-inbox" role="tabpanel" aria-labelledby="list-mail-inbox">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo fix_name_header($character->get_name()); ?> Inbox</h3>
                        </div>

                        <div class="list-group">
                            <?php echo $user_inbox->focusedFolder->getFolderHTML(); ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-outbox" role="tabpanel" aria-labelledby="list-mail-outbox">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo fix_name_header($character->get_name()); ?> Outbox</h3>
                        </div>

                        <div class="list-group">
                            <?php echo $user_outbox->focusedFolder->getFolderHTML(); ?>
                        </div>
                    </div>
                </div>
            
                <div class="tab-pane fade" id="list-deleted" role="tabpanel" aria-labelledby="list-mail-deleted">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo fix_name_header($character->get_name()); ?> Deleted</h3>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="list-drafts" role="tabpanel" aria-labelledby="list-mail-drafts">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo fix_name_header($character->get_name()); ?> Drafts</h3>
                        </div>
                    </div>
                </div>
                
                
                    
            </div>

