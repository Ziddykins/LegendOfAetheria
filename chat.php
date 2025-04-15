<?php
    require_once "bootstrap.php";

    $request = file_get_contents('php://input');
    $req_obj = json_decode($request);
    
    header('Content-Type: application/json');

    switch ($req_obj->action) {
        case 'get_msgs':
            $count = $req_obj->count;
            $room  = $req_obj->room;
            get_messages($room, intval($count));
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
            
            if ($result == false) {
                http_response_code(400);
                echo '{"status":"error - couldnt add message"}';
                exit();
            } else {
                echo '{"status":"success"}';
                exit();
            }
        case 'get_online':
            $sql_query = "SELECT COUNT(`id`) AS `online` FROM {$t['accounts']} WHERE `session_id` IS NOT NULL";
            echo json_encode($db->execute_query($sql_query)->fetch_assoc());
            exit();
        default:
            echo '{"status":"error - invalid action or data supplied"}';
            exit();
    }

    function add_message(int $char_id, string $room, string $message, string $nickname): void {
        global $db, $t;
        $sql_query = "INSERT INTO {$t['chat']} (`character_id`, `room`, `message`, `nickname`) VALUES (?, ?, ?, ?)";
        
        if (!$db->execute_query($sql_query, [$char_id, $room, $message, $nickname])) {
            http_response_code(400);
            echo '{"status": "' . $db->error . '"}';
            exit();
        }
        
        echo '{"status":"success"}';
        exit();
    }
    function get_messages(string $room = '!main', int $count = 100): void {
        global $db, $t;
        $messages = [];
        
        $sql_query = "SELECT * FROM {$t['chat']} WHERE `room` = ? ORDER BY `id` DESC LIMIT ?";
        $messages = $db->execute_query($sql_query, [$room, $count])->fetch_all(MYSQLI_ASSOC);

        if (!$messages) {
            http_response_code(400);
            echo '{"status":"' . $db->error . '"}';
        }
        
        echo json_encode($messages);
        exit();
    }