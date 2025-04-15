<?php
    use Game\Character\Enums\FriendStatus;

    $data = get_friend_counts(FriendStatus::MUTUAL, true)['ids'];
    $arr = [];

    foreach ($data as $id) {
        array_push($arr, $id['character_id']);
    }

    $id_list = join(', ', $arr);
        
    $sql_query = "SELECT `name` FROM {$t['characters']} WHERE `id` IN ($id_list)";
    $results = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);
?>
                    <div class="container border border-secondary ps-3 pe-3 w-50">
                        <div class="row mb-3 mt-3 align-items-center">
                            <div class="col">
                                <div class="lead">
                                    Compose Message
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
                                        
                                        if ($results) {
                                            $count = 0;
                                            foreach ($results as $char) {
                                                if ($count++ < 10) {
                                                    echo "<option value=\"" . htmlentities($char['name']) . "\">";
                                                }
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