<?php
    declare(strict_types = 1);
    session_start();
    require '../../vendor/autoload.php';
    require_once 'classes/class-openai.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $api_endpoint = 'https://api.openai.com/v1/chat/completions';
    
    $default_headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $_ENV['OPENAI_APIKEY'],
    ];
    
    $chatbot = new OpenAI(
        $_ENV['OPENAI_APIKEY'],
        $api_endpoint
    );

    $chatbot->set_model('gpt-3.5-turbo');
    $model = $chatbot->get_model();

    $data = [
        "model" => $chatbot->get_model(),
        "messages" => [
            [
                "role" => "system",
                "content" =>  "You're a customer support rep for a multi-national conglomerancy focused on cloud computing",
            ],
            [
                "role" => "user",
                "content" => "How do I internet?",
            ],
        ],
    ];

    /*$response = json_decode($chatbot->doRequest(
        HttpMethod::POST,
        $default_headers,
        $data)
    );
     */
    //$chat_response = $response->choices[0]->message->content;

?>
<div class="d-flex container justify-content-evenly border p-3">
    <form id="generate-images" name="generate-images" method="POST" action="?page=administrator">
        <div class="row bg-primary text-white">
            <div class="col">
                <h4>Generate Images</h4>
            </div>
        </div>
        <div class="row mb-3 mt-2 bg-tertiary">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="prompt-icon" name="prompt-icon">
                        <span class="material-symbols-sharp">cognition</span>
                    </span>
                    <div class="form-floating">
                        <input type="text" class="form-control form-control-sm" id="prompt" name="prompt" required>
                        <label for="prompt">Prompt</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3"> 
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="select-ai-type">
                        <span class="material-symbols-sharp">sort_by_alpha</span>
                    </span>

                    <select class="form-select form-control" aria-label="form-select" id="type" name="type" style="font-size: 1.00rem" required>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="select-ai-model">
                        <span class="material-symbols-sharp">sort_by_alpha</span>
                    </span>

                    <select class="form-select form-control" aria-label="form-select" id="model" name="model" style="font-size: 1.00rem" required>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-3"> 
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="generate-images-icon">
                        <span class="material-symbols-sharp">sort_by_alpha</span>
                    </span>

                    <select class="form-select form-control" aria-label="form-select" id="type" name="type" style="font-size: 1.00rem" required>
                        <option value="Select Type" disabled selected>Select Type</option>
                        <option value="avatars">Avatar</option>
                        <option value="eggs">Egg</option>
                        <option value="enemies">Enemy</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="generate-images-icon">
                        <span class="material-symbols-sharp">recent_actors</span>
                    </span>

                    <input type="number" id="count" name="count" value="5">
                </div>
            </div>
        </div>

        <div class="row mb-3 justify-content-center align-content-center d-flex">
            <div class="col text-center">
                <button class="btn btn-success" id="gen-images" name="gen-images" value="1">Generate</button>
            </div>
        </div>
    </form>
</div>


<div class="container justify-content-center">
    <div class="row">
        <div class="col">
