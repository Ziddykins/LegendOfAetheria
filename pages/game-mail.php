<?php
    require 'classes/class-mail.php';
    
    $account   = get_user($_SESSION['email'], 'account');
    $character = get_user($account['id'], 'character');
    
    $user_mailbox = new MailBox($account['id']);
    $user_mailbox->set_focused_folder(MailFolderType::INBOX);
    $user_mailbox->populate_focused_folder();
    $inbox_count = $user_mailbox->focusedFolder->get_message_count();
?>

<div class="container text-white">
    <div class="row pt-5">
        <div class="col">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active" id="list-mail-inbox" data-bs-toggle="list" href="#list-inbox" role="tab" aria-controls="list-inbox">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-arrow-up" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4.5a.5.5 0 0 1-1 0V5.383l-7 4.2-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h5.5a.5.5 0 0 1 0 1H2a2 2 0 0 1-2-1.99V4Zm1 7.105 4.708-2.897L1 5.383v5.722ZM1 4v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1Z"/>
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.354-5.354 1.25 1.25a.5.5 0 0 1-.708.708L13 12.207V14a.5.5 0 0 1-1 0v-1.717l-.28.305a.5.5 0 0 1-.737-.676l1.149-1.25a.5.5 0 0 1 .722-.016Z"/>
                </svg> Inbox (<?php echo $inbox_count; ?>)
                </a>
                <a class="list-group-item list-group-item-action" id="list-mail-outbox" data-bs-toggle="list" href="#list-outbox" role="tab" aria-controls="list-outbox">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-arrow-down" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4.5a.5.5 0 0 1-1 0V5.383l-7 4.2-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h5.5a.5.5 0 0 1 0 1H2a2 2 0 0 1-2-1.99V4Zm1 7.105 4.708-2.897L1 5.383v5.722ZM1 4v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1Z"/>
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.354-1.646a.5.5 0 0 1-.722-.016l-1.149-1.25a.5.5 0 1 1 .737-.676l.28.305V11a.5.5 0 0 1 1 0v1.793l.396-.397a.5.5 0 0 1 .708.708l-1.25 1.25Z"/>
                </svg> Outbox
                </a>
                <a class="list-group-item list-group-item-action" id="list-mail-deleted" data-bs-toggle="list" href="#list-deleted" role="tab" aria-controls="list-deleted">
                    <i class="bi bi-envelope-dash"></i> Deleted
                </a>
                <a class="list-group-item list-group-item-action" id="list-mail-drafts" data-bs-toggle="list" href="#list-drafts" role="tab" aria-controls="list-drafts">
                    <i class="bi bi-envelope-paper"></i> Drafts
                </a>
            </div>
        </div>
        <div class="col-8">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="list-inbox" role="tabpanel" aria-labelledby="list-mail-inbox">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo $_SESSION['name']; ?>'s Messages</h3>
                        </div>
                            <?php
                                for ($i=0; $i<$inbox_count - 1; $i++) {                                
                                    $subject  = $user_mailbox->focusedFolder->envelopes[$i]->subject;
                                    $sender   = $user_mailbox->focusedFolder->envelopes[$i]->sender;
                                    $msg_frag = substr($user_mailbox->focusedFolder->envelopes[$i]->message, 0, 25);
                                    $date     = $user_mailbox->focusedFolder->envelopes[$i]->date;

                                    echo '<div class="list-group">';
                                    echo '    <a href="#" class="list-group-item list-group-item-action mb-1 ';
                                    if ($i == 0) {
                                        echo 'active';
                                    }
                                    echo '" aria-current="true">';
                                    echo '        <div class="d-flex w-100 justify-content-between">';
                                    echo '            <h6 class="mb-1">' . $subject . '</h6>';
                                    echo '            <small>' . $date . '</small>';
                                    echo '        </div>';
                                    echo '        <p class="mb-1">' . $sender . '</p>';
                                    echo '        <small>' . $msg_frag . '</small>';
                                    echo '   </a>';
                                    echo '</div>';
                                    echo '';
                                }
                            ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="list-outbox" role="tabpanel" aria-labelledby="list-mail-outbox">
                    
                </div>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active" aria-current="true">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">List group item heading</h5>
                                <small>3 days ago</small>
                            </div>
                            <p class="mb-1">Some placeholder content in a paragraph.</p>
                            <small>And some small print.</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" aria-current="true">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">List group item heading</h5>
                                <small>3 days ago</small>
                            </div>
                            <p class="mb-1">Some placeholder content in a paragraph.</p>
                            <small>And some small print.</small>
                        </a>
                    </div>
                </div>
                <div class="tab-pane fade" id="list-deleted" role="tabpanel" aria-labelledby="list-mail-deleted">
                o look some message things
                </div>
                <div class="tab-pane fade" id="list-drafts" role="tabpanel" aria-labelledby="list-mail-drafts">
                get out
                </div>
            </div>
        </div>
    </div>
</div>
