<?php
    use Game\Account\Enums\Privileges;
    use Game\OpenAI\OpenAI;
    use Game\openai\Enums\HttpMethod;
    use Game\OpenAI\Enums\Models;

    
    require_once "constants.php";
    require_once "functions.php";
    global $system;



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
            'DALLE2' => [
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

            'DALLE3' => [
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


    $openai = new OpenAI($_ENV['OPENAI_APIKEY']);
    
    if (isset($_POST['gen-images']) && $_POST['gen-images'] == 1) {
        // Image generation logic starts here
        $type = $_POST['type'] ?? null;
        $model = $_POST['model'] ?? null;
        $prompt = $_POST['image-prompt'] ?? null;
        $count = $_POST['count'] ?? 1;
        $resolution = $_POST['resolutions'] ?? null;

        if (!$prompt || !$model) {
            echo '<div class="alert alert-danger">Missing required parameters</div>';
            return;
        }

        $openai->set_model($model);
        $openai->set_prompt($prompt);
        $openai->set_count((int)$count);
        $openai->set_resolution($resolution);
        
        try {
            $response = $openai->generate_image();
            $json_obj = json_decode($response);

            print_r($response);
            exit();

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
        } catch (Exception $e) {
            $openai->throw_error('Error: ' . $e->getMessage());
        }
    }
?>
        <div class="container w-50 border border-primary">
            <div class="row bg-primary text-white width-100">
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
                            <option value="Select Model" disabled selected>Select Model</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="container invisible" id="dall-e" name="dall-e">
                <div class="d-flex container justify-content-evenly border p-3">
                    <form id="generate-dalle-images" name="generate-dalle-images" method="POST" action="/game?page=administrator&sub=system">
                        <input type="hidden" name="gen-images" value="1">
                        <div class="row bg-primary text-white">
                            <div class="col">
                                <h4>Generate Images</h4>
                            </div>
                        </div>

                        <?php for($i=0; $i < count($_SESSION['last_generated_image']); $i++): ?>
                        <?php if (file_exists($_SESSION['last_generated_image'][$i])): ?>
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

                                    <select id="resolutions" name="resolutions" class="form-select" id="type" name="type" required>
                                        
                                    </select>
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
    </div>
