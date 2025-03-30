<?php
    use Game\Character\Enums\FriendStatus;

    
    $mutual_friends = get_friend_counts(FriendStatus::MUTUAL, 0, true);
?>
                    <div class="container border border-secondary p-5">
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
                                <input class="form-control" list="address-book-list" id="to-field" placeholder="Type to search contacts...">
                                <datalist id="address-book-list">
                                    <?php
                                        if ($mutual_friends) {
                                            foreach ($mutual_friends as $mf) {
                                                echo '<option value="' . $mf . '">';
                                            }
                                        } else {
                                            echo '<option value="Empty">';
                                        }
                                    ?>
                                </datalist>
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
                                </span>
                                <textarea id="message-field" name="message-field" rows="5" class="form-control mb-3"></textarea>                                </div>
                                <div class="d-grid gap-1">
                                    <button id="send-mail" name="send-mail" class="btn btn-primary" onclick=send_click()>Send Mail</button>
                                    <button id="cancel-mail" name="cancel-mail" class="btn btn-secondary" onclick=close_click()>Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="/js/mail.js" type="text/javascript"></script>