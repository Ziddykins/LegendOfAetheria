<?php
    require_once "bootstrap.php";
    $request = file_get_contents('php://input');
    $req_obj = json_decode($request);

    switch ($req_obj->action) {
        case 'get_msgs':
            $count = $req_obj->count;
            $room  = $req_obj->room;
            $messages = get_messages($room, intval($count));
            echo json_encode($messages);
            break;
        case 'add_msg':
            $char_id  = $req_obj->char_id;
            $room     = $req_obj->room;
            $message  = $req_obj->message;
            $nickname = $req_obj->nickname;

            if ($char_id !== $_SESSION['character-id']) {
                //check_abuse(AbuseType::CHATABUSE);
            } elseif ($room != '!main') {
                //check_abuse(AbuseType::CHATABUSE);
            }

            $message = htmlentities($message);
            $result  = add_message($char_id, $room, $message, $nickname);
            
            if ($result == false) {
                http_response_code(400);
                echo '{"status": "error - couldnt add message"}';
            } else {
                echo '{"status": "success"}';
            }
            break;
        case 'online_count':
            $sql_query = "SELECT COUNT(`id`) AS `online` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `session_id` IS NOT NULL";
            echo json_encode($db->execute_query($sql_query)->fetch_assoc());
            break;
        default:
            echo '{"status": "error - invalid action or data supplied"}';
    }

    function add_message(int $char_id, string $room, string $message, string $nickname): string {
        global $db;
        $sql_query = "INSERT INTO {$_ENV['SQL_CHAT_TBL']} (`character_id`, `room`, `message`, `nickname`) VALUES (?, ?, ?, ?)";
        
        if (!$db->execute_query($sql_query, [$char_id, $room, $message, $nickname]) {
            http_response_code(400);
            return '{"status": "' . $db->error . '"}';
        }
        
        return '{"status": "success"}';
    }
    function get_messages(string $room = '!main', int $count = 100): array {
        global $db;
        $messages = [];
        

        $sql_query = "SELECT * FROM {$_ENV['SQL_CHAT_TBL']} WHERE `room` = ? ORDER BY `id` DESC LIMIT ?";
        $messages = $db->execute_query($sql_query, [$room, $count])->fetch_all(MYSQLI_ASSOC);

        if (!$messages) {
            http_response_code(400);
            return '{"status": "' . $db->error . '"}';
        }
        return json_encode($messages);
    }