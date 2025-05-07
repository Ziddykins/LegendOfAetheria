<?php
    use Game\Account\Enums\Privileges;
    use Game\OpenAI\OpenAI;
    use Game\OpenAI\Enums\HttpMethod;

    require_once "constants.php";
    global $system;

    $_POST = file_get_contents('php://input');

    if ($_POST) {
        print_r($_POST);
        exit();
    }

    $user_privs = $account->get_privileges();

    if ($user_privs->value < Privileges::ADMINISTRATOR->value) {
        echo "F O R B I D D E N";
        exit();
    }

    if (!isset($_SESSION['last_generated_image'])) {
        $_SESSION['last_generated_image'] = [];
    }
    if (!isset($_SESSION['last_image_prompt'])) {
        $_SESSION['last_image_prompt'] = [];
    }
    

    $type = null;
    $prompt = null;
    $model = null;
    $count = null;
    $endpoint = null;
    $reference = -1;

    $ai = [
        'text' => [
            'gpt-4',
            'gpt-3.5-turbo-1106',
            'gpt-3.5-turbo',
            'gpt-3.5-turbo-16k'
        ],
        'image' => [
            'dall-e-2' => [
                'max_prompt_size' => 1000,
                'max_count' => 5,
                'edits' => true,

                'resolutions' => [
                    '256x256',
                    '512x512',
                    '1024x1024'
                ],

                'quality' => [
                    'standard'
                ],
            ],

            'dall-e-3' => [
                'max_count' => 1,
                'max_prompt_size' => 4000,
                'edits' => false,

                'resolutions' => [
                    '1024x1024',
                    '1792x1024',
                    '1024x1792'
                ],

                'quality' => [
                    'standard',
                    'hd'
                ],

                'style' => [
                    'vivid',
                    'natural'
                ]
            ]
        ],

        'output' => [
            'png',
            'webp',
            'jpg'
        ]
    ];

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: text/html; charset=utf-8');
    }
    
    $openai = new OpenAI($_ENV['OPENAI_APIKEY']);

    if (isset($_POST['type'])) {
        $type = $_POST['type'];

        if ($type === 'text') {
            $openai->set_endpoint('https://api.openai.com/v1/chat/completions');
        } else if ($type === 'image') {
            $openai->set_endpoint('https://api.openai.com/v1/images/generations');
        } else {
            $openai->throw_error('Invalid Type');
        }

        $openai->set_type($type);
    } else {
        $openai->throw_error('<div class="alert alert-danger">No type selected</div>');
        return;
    }

    if (isset($_POST['model'])) {
        $model = $_POST['model'];

        if (array_search($model, $ai[$type]) !== false) {
            $openai->set_model($model);
        } else {
            $openai->throw_error("No type selected");
            return;
        }
    }

    if (isset($_POST['image-prompt'])) {
        $prompt     = htmlentities($_POST['image-prompt']);
        $max_length = $ai[$type][$model]['max_prompt_size'];

        if (strlen($prompt) <= $max_length) {
            $openai->set_prompt($prompt);
        } else {
            $openai->throw_error("Promp exceeds maximum length of $max_length");
        }
    }
    
    if (isset($_POST['reference'])) {
        $reference = $_POST['reference'];
        if ($reference < 0 || $reference > count($_SESSION['last_generated_image']) - 1) {
            echo '<div class="alert alert-danger">Invalid reference image selected</div>';
            return;
        }
    }


    if (array_search($_POST['model'], $gpt_models) === false && array_search($_POST['model'], $dalle_models) === false) {
        echo "<div class='alert alert-danger'>Invalid model selected</div>";
    }

    if (isset($_POST['count'])) {
        $max_count = $ai[$type][$model]['max_count'];
        if (is_numeric($_POST['count'])) {
            $openai->set_count(min(max(1, intval($_POST['count'])), $max_count));
        } else {
            $openai->throw_error("Invalid count; max: $max_count");
            return;
        }
    } else {
        $openai->throw_error("No count selected");
        return;
    }

    $payload = $OpenAI->get_payload();

    $response = $OpenAI->doRequest(HttpMethod::POST, $payload);
    $json_obj = json_decode($response);

    if ($json_obj === null) {
        $openai->throw_error('Error decoding API response');
        return;
    }

    if (isset($json_obj->error)) {
        $openai->throw_error('API Error: ' . htmlspecialchars($json_obj->error->message));
        return;
    }

    if (isset($json_obj->data) && count($json_obj->data) > 0) {
        echo '<div class="d-flex container flex-wrap">';
        $icount = 1;
        
        foreach ($json_obj->data as $image) {
            if (!isset($image->url)) {
                continue;
            }

            $timestamp = date('YmdHis');
            $filename  = "{$type}_{$timestamp}_$icount.png";
            $img_file  = GENERATED_DIRECTORY . "/$type/$filename";

            if (!is_dir(GENERATED_DIRECTORY . escapeshellarg("/$type"))) {
                mkdir(GENERATED_DIRECTORY . escapeshellarg("/$type"), 0755, true);
            }

            if (array_search($img_file, $_SESSION['last_generated_image']) === false) {
                array_push($_SESSION['last_generated_image'], $img_file);
                array_push($_SESSION['last_image_prompt'], $prompt);
            }

            // Download image
            $ch = curl_init($image->url);
            $fp = fopen(escapeshellarg($img_file), 'wb');
            if ($fp === false) {
                $openai->throw_error('Error: Could not open file for writing: ' . htmlentities($img_file) . '</div>');
                continue;
            }

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);

            if (curl_errno($ch)) {
                echo "<div class='alert alert-danger'>Error downloading image: " . curl_error($ch) . "</div>";
            }

            curl_close($ch);
            fclose($fp);

            // Display image card
            echo '<div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Image ' . $icount . '</h5>
                    </div>
                    <div class="card-body">
                        <img src="/' . htmlspecialchars($img_file) . '" alt="Generated image ' . $icount . '" class="img-fluid">
                        <p class="card-text mt-2"><small>' . htmlspecialchars($prompt) . '</small></p>
                    </div>
                </div>
            </div>';

            $icount++;
        }
        echo '</div>';
    } else {
        echo "<div class='alert alert-warning'>No images were generated</div>";
    }
?>

<?php include('html/opener.html'); ?>
    <head>
        <?php include('html/headers.html'); ?>

    </head>
        
    <body>
        <pre>
            <small>
                <div id="debug" class="border">

                </div>
            </small>
        </pre>
        
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
                        <option value="text">Text</option>
                        <option value="image">Image</option>
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
                <form id="generate-dalle-images" name="generate-dalle-images" method="POST">
                    <div class="row bg-primary text-white">
                        <div class="col">
                            <h4>Generate Images</h4>
                        </div>
                    </div>

                    <?php for($i=0; $i < count($_SESSION['last_generated_image']); $i++): ?>
                    <?php if (isset($_SESSION['last_generated_image'][$i]) && file_exists($_SESSION['last_generated_image'][$i])): ?>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Generated Image <?php echo $i;?></h6>
                                </div>
                                
                                    <div class="card-body">
                                        <img src="/<?php echo htmlspecialchars($_SESSION['last_generated_image'][$i]); ?>" class="img-fluid mb-2" style="max-height: 200px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="use-previous-<?php echo $i;?>" name="use-previous-<?php echo $i; ?>">
                                            <label class="form-check-label" for="use-previous">
                                                Use this as reference for next generation
                                            </label>
                                        </div>
                                        <small class="text-muted d-block">Previous prompt: <?php echo htmlspecialchars($_SESSION['last_image_prompt'][$i] ?? ''); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>

                    <div class="row mb-3"> 
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-sharp">sort_by_alpha</span>
                                </span>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="eggs" selected>Egg</option>
                                    <option value="avatars">Avatar</option>
                                    <option value="enemies">Enemy</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-sharp">description</span>
                                </span>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="image-prompt" name="image-prompt" maxlength="1000" required>
                                    <label for="image-prompt">Image Description</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-sharp">format_list_numbered</span>
                                </span>
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="count" name="count" value="1" min="1" max="5">
                                    <label for="count">Number of Images (1-5)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                                        <div class="row mb-3">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-sharp">view_in_ar</span>
                                </span>
                                <div class="form-floating">
                                    <input type="dropdown" class="form-control" id="resolution" name="resolution">
                                        <?php if ($OpenAI->get_imageModel() === 'dall-e-3'): ?>
                                            <option value="""
                                    <label for="count">Image Resolution</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="model" name="model" value="">
                    <input type="hidden" id="selected-type" name="selected-type" value="dalle">

                    <div class="row mb-3 justify-content-center">
                        <div class="col text-center">
                            <button type="submit" class="btn btn-success" id="gen-images" name="gen-images" value="1">Generate Images</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="container invisible" id="gpt" name="gpt">
            <div class="d-flex container justify-content-evenly border p-3">
                <form id="generate-gpt-content" name="generate-gpt-content" method="POST" action="/game?page=administrator&sub=system">
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
