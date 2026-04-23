<?php
    use Game\Account\Enums\Privileges;
    use Game\AI\OpenAI;
    use Game\AI\Enums\HttpMethod;

    $user_privs = Privileges::name_to_enum($account->get_privileges());

    if ($user_privs->value < Privileges::ADMINISTRATOR->value) {
        echo "F O R B I D D E N";
        exit();
    }
?>

<?php include('html/opener.html'); ?>
    <head>
        <?php include('html/headers.html'); ?>

    </head>
        
    <body>
    <small>
    <div id="debug" class="border">

    </div>
    </small></pre>
         <div class="row bg-primary text-white">
            <div class="col">
                <h4>AI and Model</h4>
            </div>
        </div>
        <div class="row mb-3 mt-2 bg-tertiary">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="prompt-icon" name="prompt-icon">
                        <span class="material-symbols-sharp">psychology_alt</span>
                    </span>
                    <select class="form-select form-control" aria-label="form-select" id="ai-type" name="ai-type" style="font-size: 1.00rem" required>
                        <option value="Select Type" disabled selected>Select Type</option>
                        <option value="gpt">GPT-3.5 - GPT 4</option>
                        <option value="dalle">DALL-E 2/DALL-E 3</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-3 mt-2 bg-tertiary">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="prompt-icon" name="prompt-icon">
                        <span class="material-symbols-sharp">cognition</span>
                    </span>
                    <select class="form-select form-control" aria-label="form-select" id="ai-model" name="ai-model" style="font-size: 1.00rem" required>
                    
                    </select>
                </div>
            </div>
        </div>

        <div class="container invisible" id="dall-e" name="dall-e">
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
                                <span class="input-group-text" id="generate-images-icon">
                                    <span class="material-symbols-sharp">sort_by_alpha</span>
                                </span>

                                <select class="form-select form-control" aria-label="form-select" id="type" name="type" style="font-size: 1.00rem" required>
                                    <option value="Select Type" disabled selected>Select Type</option>
                                    <option value="avatars">Avatar</option>
                                    <option value="eggs">Egg</option>
                                    <option value="enemies">Enemy</option>
                                </select>

                                <label for="image-prompt">Enter image description:</label>
                                <input type="text" class="form-control" id="image-prompt" name="image-prompt">
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

                    <input type="hidden" id="model" name="model">

                    <div class="row mb-3 justify-content-center align-content-center d-flex">
                        <div class="col text-center">
                            <button class="btn btn-success" id="gen-images" name="gen-images" value="1">Generate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="container invisible" id="gpt" name="gpt">
            <div class="d-flex container justify-content-evenly border p-3">
                <form id="generate-images" name="generate-images" method="POST" action="?page=administrator">
                    <div class="row bg-primary text-white">
                        <div class="col">
                            <h4>Chat it up why not</h4>
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
                                <span class="input-group-text" id="generate-images-icon">
                                    <span class="material-symbols-sharp">sort_by_alpha</span>
                                </span>

                                <select class="form-select form-control" aria-label="form-select" id="type" name="type" style="font-size: 1.00rem" required>
                                    <option value="Select Type" disabled selected>Select system type</option>
                                    <option value="assistant">Assistant</option>
                                    <option value="eggs">other</option>
                                    <option value="enemies">other2</option>
                                </select>
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
        </div>
    </div>
    <script type="text/javascript" src="../js/openai.js"></script>
<?php

$gpt_models = [
    'gpt-4',
    'gpt-3.5-turbo-1106',
    'gpt-3.5-turbo',
    'gpt-3.5-turbo-16k'
];

$dalle_models = [
    'dall-e-3',
    'dall-e-2'
];

$user_privs = Privileges::name_to_enum($account->get_privileges());

if (isset($_POST['gen-images']) && $_POST['gen-images'] == 1) {
    global $system;
    $count  = 0;
    $prompt = null;

    if (isset($_POST['image-prompt'])) {
        $prompt = htmlentities($_POST['image-prompt']);
    } else {
        $prompt = "Generate an image of a monster";
    }
    
    $count++;
    
    $type   = $_POST['type'];
    $model = $_POST['model'];
    $count  = 1;

    $endpoint = 'https://api.openai.com/v1/images/generations';
    
    if (in_array($model, $dalle_models)) {
        if ($count < 1 || $count > 5) {
            $count = 2;
        }
        
        $openai_apikey = $_ENV['OPENAI_APIKEY'];    
        $OpenAI = new OpenAI($openai_apikey, $endpoint);
        
        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => "Bearer " . $OpenAI->get_apiKey()
        ];

        $OpenAI->set_imageModel($model);
        $OpenAI->set_imagePrompt($prompt);
        $OpenAI->set_imageCount($count);
        $OpenAI->set_imageSize("256x256");
        
        $temp = print_r($OpenAI, true);
        $temp = str_replace($_ENV['OPENAI_APIKEY'], '<font color="red"><APIKEY></font>', $temp);
        echo $temp ;
        $response = $OpenAI->doRequest(
            HttpMethod::POST,
            $OpenAI->get_payload()
        );
    } else {
        echo "sry no";
        exit();
    }

        $json_obj = json_decode($response);
        $filename = explode("/", $json_obj->data[0]->url)[-1];
        echo "<img src='" . htmlentities($json_obj->data[0]->url, ENT_QUOTES, 'UTF-8') . "'></img><br>";
        $img_file = 'img/generated/';
        $ch = curl_init($json_obj->data[0]->url);
        $fp = fopen($img_file, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        echo "Saved $img_file\n<br>";
        if ($count % 5 == 0) {
            echo "sleepin'<br>";
            sleep(360);
        }
        
    }
    exit();
    
    //echo '<div class="d-flex container">';
  /*  foreach ($json_obj->data as $image) {
        foreach ($image as $property) {   
                    echo '<div class="row-cols-3">
                        <div class="col">
                            <h5>Image ' . $icount . '</h5>
                        </div>
                        <div class="col p-2 m-2 border">
                            <img src="data:image/png;base64,' . $property . '" alt="user-generated-' . $i . '></img>
                        </div>
                        <div class="col">
                            <small>' . $prompt . '</small>
                        </div>
                    </div>';
        }
        $icount++;
    }
    echo '</div>';
    */

?>
