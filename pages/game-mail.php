<?php

use Game\Account\Account;
use Game\Character\Character;
use Game\Mail\MailBox\MailBox;
use Game\Mail\Folder\Enums\FolderType;
use Game\Mail\Envelope\Enums\EnvelopeStatus;

$account   = new Account($_SESSION['email']); 
$character = new Character($account->get_id(), $_SESSION['character-id']); 

$user_mailbox = new MailBox($account->get_id());
$user_mailbox->setFocusedFolder(FolderType::INBOX);
$user_mailbox->populateFocusedFolder();
$inbox_count = $user_mailbox->focusedFolder->getMessageCount();


?>

<div class="container text-white">
    <div class="row pt-5 mb-3">
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
                            <h3><?php echo $_SESSION['name']; ?>'s Messages</h3>
                        </div>

                        <?php
                            for ($i=$inbox_count - 1; $i>=0; $i--) {
                                $subject    = $user_mailbox->focusedFolder->envelopes[$i]->subject;
                                $sender     = $user_mailbox->focusedFolder->envelopes[$i]->sender;
                                $msg_frag   = $user_mailbox->focusedFolder->envelopes[$i]->message;
                                $date       = $user_mailbox->focusedFolder->envelopes[$i]->date;
                                $flagstring = $user_mailbox->focusedFolder->envelopes[$i]->status;
                                
                                $status_int = EnvelopeStatus::value_from_flagstring($flagstring);
                                $important  = $status_int & EnvelopeStatus::IMPORTANT->value;
                                $read       = $status_int & EnvelopeStatus::READ->value;
                                $replied    = $status_int & EnvelopeStatus::REPLIED->value;
                                $favorite   = $status_int & EnvelopeStatus::FAVORITE->value;

                                $status_line = EnvelopeStatus::get_status_line($flagstring);

                                echo '<div class="list-group">';
                                echo '    <a href="#" id="env-id-' . $i . '" class="list-group-item list-group-item-action mb-1 text-truncate ';

                                if ($i == 0) {
                                    echo 'active';
                                }

                                if (!$read) {
                                    echo ' text-bg-dark bg-gradient';
                                }

                                echo '" aria-current="true">';
                                echo '        <div class="d-flex w-100 justify-content-between">';
                                echo '            <h6 id="env-sub-' . $i . '" class="mb-1">' . $subject . '</h6>';
                                echo '            <small id="env-date-' . $i . '">' . $date . '</small>';
                                echo '        </div>';
                                echo '        <div class="d-flex w-100 justify-content-between">';
                                echo '            <span id="env-from-' . $i . '" class="mb-1">' . $sender . '</span>';
                                echo "            $status_line";
                                echo '        </div>';
                                echo '        <small id="env-frag-' . $i . '" class="col text-truncate">' . $msg_frag . '</small>';
                                echo '   </a>';
                                echo '</div>';
                                echo '';
                            }
                        ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="list-outbox" role="tabpanel" aria-labelledby="list-mail-outbox">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo $_SESSION['name']; ?>'s Messages</h3>
                        </div>

                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action active" aria-current="true">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Sub</h5>
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
                </div>
            
                <div class="tab-pane fade" id="list-deleted" role="tabpanel" aria-labelledby="list-mail-deleted">
                    o look some message things
                </div>
                
                <div class="tab-pane fade" id="list-drafts" role="tabpanel" aria-labelledby="list-mail-drafts">
                    get out
                </div>
                
                <div class="tab-pane fade" id="list-compose" role="tabpanel" aria-labelledby="list-mail-compose">
                    <div class="container border border-secondary">
                        <div class="row text-bg-dark bg-gradient mb-3 align-items-center">
                            <div class="col">
                                <div class="lead p-2">
                                    Compose Message
                                </div>
                            </div>

                            <div class="col">
                                <div id="mail-close" name="mail-close" class="btn btn-close float-end" onclick=close_click()>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="to-field" class="col-form-label">To:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                            </div>

                            <div class="col-sm-10 pe-5">
                                <input class="form-control" list="address-book-list" id="address-book" placeholder="Type to search contacts...">
                                <datalist id="address-book-list">
                                    

                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="subject-field" class="col-form-label">Subject:</label>
                            </div>

                            <div class="col-sm-10 pe-5  ">
                                <input id="subject-field" name="subject-field" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col">
                                <label for="message-field" class="col-form-label">Message:</label>
                            </div>

                            <div class="col-sm-10 pe-5">
                                    <span class="col mx-auto text-center">
                                        <input id="important-field" name="important-field" type="checkbox" value="0" class="form-check-input">
                                        <label for="important-field" class="form-check-label small">Important</label>
                                    </div>
                                    <textarea id="message-field" name="message-field" rows="5" class="form-control mb-3"></textarea>
                                </div>
                                <div class="d-grid gap-1">
                                    <button id="send-mail" name="send-mail" class="btn btn-primary" onclick=send_click()>Send Mail</button>
                                    <button id="cancel-mail" name="cancel-mail" class="btn btn-secondary" onclick=close_click()>Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="/js/mail.js" type="text/javascript"></script>
            
        
    