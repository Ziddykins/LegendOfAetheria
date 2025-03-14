<?php
    declare(strict_types = 1);
    session_start();

    use Game\Account\Account;
    use Game\Character\Enums\FriendStatus;
    use Game\Mail\Folder\Enums\FolderType;

    require_once "bootstrap.php";

    if (check_session()) {
        $rawData = file_get_contents('php://input');
        $message = json_decode($rawData, true);

        $sender_aid    = $message['s_aid'];
        $sender_cid    = $message['s_cid'];
        $sender_sid    = $message['s_sid'];
        $sender_csrf   = $message['s_csrf'];
        $recipient     = $message['to'];
        $subject       = $message['subject'];
        $msg_body      = $message['message'];
        $important     = $message['important'];
        $friend_status = FriendStatus::name_to_enum($recipient);

        if (check_valid_email($recipient)) {
            $recipient_aid = Account::checkIfExists($message['to']);

            if ($recipient_aid) {
                if ($sender_sid === session_id()) {
                    if ($sender_csrf == $_SESSION['csrf-token']) {
                        if (friend_status($recipient) === FriendStatus::MUTUAL) {
                            $sql_query = <<<SQL
                                INSERT INTO {$_ENV['SQL_MAIL_TBL']}
                                    (
                                        `folder`,
                                        `to`,
                                        `from`,
                                        `subject`,
                                        `message`,
                                        `status`,
                                        `r_aid`,
                                        `s_aid`,
                                        `r_cid`,
                                        `s_cid`
                                    )
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            SQL;

                            $db->execute_query($sql_query,
                                [
                                    FolderType::INBOX->name,
                                    $recipient,
                                    $_SESSION['email'],
                                    $subject,
                                    $msg_body,
                                    $important
                                ]
                            );
                            echo '{"mail_status": "Email sent!"}';
                            exit();
                        } else {
                            http_response_code(405);
                            echo '{"mail_status": "Recipient is not a mutal friend, cannot send emails to them"}';
                            exit();
                        }
                    } else {
                        http_response_code(400);
                        echo '{"mail_status": "CSRF Token Mis-match"}';
                        exit();
                    }
                } else {
                    http_response_code(401);
                    echo '{"mail_status": "Session has expired, please re-login"}';
                    exit();
                }
            } else {
                http_response_code(400);
                echo '{"mail_status": "Recipient does not exist"}';
                exit();
            }
        } else {
            http_response_code(400);
            echo '{"mail_status": "Malformed email address"}';
            exit();
        }
    } else {
        http_response_code(401);
        echo "Not logged in";
    }
?>