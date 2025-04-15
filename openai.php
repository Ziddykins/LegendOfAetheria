<?php
    declare(strict_types = 1);
        
    require_once "bootstrap.php";
    use Game\Account\Account;
    use Game\Character\Character;
    use Game\OpenAI\OpenAI;
    use Game\OpenAI\Enums\HttpMethod;



    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $account   = new Account($_SESSION['email']);
    $character = new Character($_SESSION['account-id'], $_SESSION['character-id']);
    $character->load();

  
    if (isset($_REQUEST['generate_description']) 
            && $_REQUEST['generate_description'] == 1
            && isset($_REQUEST['characterID'])) {

        
        $credits = $account->get_credits();

    
        /*if ($credits < 1) {
            echo "Not Enough Credits\n";
            exit();
        } else {
            $credits--;
            $account->set_credits($credits);
        }(*/

        $sql_query = <<<SQL
            SELECT
                ch.`name`,
                ch.`race`,
                ch.`account_id`,
                ac.`credits`
            FROM {$t['characters']} AS `ch`
                JOIN {$t['accounts']} AS `ac`
                    ON ch.`account_id` = ac.`id`
            WHERE ac.`id` = ?
        SQL;

        $db->execute_query($sql_query, [ $account->get_id() ]);

        $api_endpoint = 'https://api.openai.com/v1/chat/completions';
        
        $chatbot = new OpenAI(
            $_ENV['OPENAI_APIKEY'],
            $api_endpoint
        );

        $chatbot->set_model('gpt-3.5-turbo-1106');
        $chatbot->set_maxTokens(200);

        $prompt = "Generate a character description for a(n) " . $character->get_race() . " named " . $character->get_name();

        $data = [
            "model" => $chatbot->get_model(),
            "messages" => [
                [
                    "role" => "system",
                    "content" =>  "You are a dungeon master who generates highly-detailed character descriptions. Only the description.",
                ],
                [
                    "role" => "user",
                    "content" => (string) $prompt,
                ],
            ],
            "max_tokens" => $chatbot->get_maxTokens()
        ];

        $response = json_decode(
            $chatbot->doRequest(HttpMethod::POST, $data)
        );

        $content       = $response->choices[0]->message->content;
        $finish_reason = $response->choices[0]->finish_reason;
        $description   = "";

        $tmp = preg_split('/\./', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($i=0; $i<count($tmp) - 1; $i++) {
            $description .= $tmp[$i] . ".\n\n";
        }

        $description = trim($description);

        $character->set_description($description);
        $account->sub_credits(10);
        
        echo $description;
    } else {
        echo "Invalid Query";
    }
?>