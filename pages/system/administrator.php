<?php
    use Game\Account\Enums\Privileges;
    use Game\AI\OpenAI;
    use Game\AI\Enums\HttpMethod;

    $user_privs = Privileges::name_to_enum($account->get_privileges());

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


    $openai = new OpenAI($_ENV['OPENAI_APIKEY']);

    if (isset($_POST['use-previous-slot']) && $_POST['use-previous-slot'] > 0) {
        $slot = $_POST['use-previous-slot'];
        $endpoint = 'https://api.openai.com/v1/images/edits';
    }

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
                    $img_relative = "/img/generated/$type/$filename";

                    if (!is_dir(GENERATED_DIRECTORY . "/$type")) {
                        throw new \Error("Invalid type");
                    }

                    if (array_search($img_file, $_SESSION['last_generated_image']) === false) {
                        array_push($_SESSION['last_generated_image'], $img_relative);
                        array_push($_SESSION['last_image_prompt'], $prompt);
                    }

                    // Download image
                    $ch = curl_init($image->url);
                    $fp = fopen(basename($img_file), 'wb');

                    if ($fp === false) {
                        $openai->throw_error('Error: Could not open file for writing: ' . htmlentities($img_relative) . '</div>');
                        continue;
                    }

                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_exec($ch);

                    if (curl_errno($ch)) {
                        echo "<div class='alert alert-danger'>Error downloading image: " . htmlentities(curl_error($ch)) . "</div>";
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
                                    <img src="' . htmlspecialchars($img_relative) . '" alt="Generated image ' . $icount . '" class="img-fluid">
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
            global $log;
            $log->error($openai->throw_error('Error: ' . $e->getMessage()));
        }
    }
?>

<div class="container border border-primary mb-3 me-3 w-75">
    <div class="row bg-primary bg-gradient shadow text-white mb-3 pt-2">
        <div class="col text-center">
            <h5>AI and Model</h5>
        </div>
    </div>

    <div class="row mb-3 bg-tertiary justify-content-md-evenly">
        <div class="col-4">
            <label for="ai-type-select" class="form-label">AI Type:</label>
            <div id="ai-type-select" name="ai-type-select" class="input-group">
                <span class="input-group-text" id="prompt-icon" name="prompt-icon">
                    <span class="material-symbols-sharp">psychology_alt</span>
                </span>
                <select id="ai-type" name="ai-type" class="form-select form-control" aria-label="form-select" style="font-size: 1.00rem" required>
                    <option value="Select Type" disabled selected>Select Type</option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                </select>
            </div>
        </div>

        <div class="col-4">
            <label for="ai-model-select" class="form-label">AI Model:</label>
            <div id="ai-model-select" name="ai-model-select" class="input-group mb-3">
                <span class="input-group-text" id="prompt-icon" name="prompt-icon">
                    <span class="material-symbols-sharp">cognition</span>
                </span>
                <select id="ai-model" name="ai-model" class="form-select form-control" aria-label="form-select" style="font-size: 1.00rem" required>
                    <option value="Select Model" disabled selected>Select Model</option>
                </select>
            </div>
        </div>
    </div>


    <form id="generate-dalle-images" name="generate-dalle-images" method="POST" action="/game?page=administrator&sub=system">
        <div id="previous-images" name="previous-images" class="container border border-primary text-center mb-3" style="display: none;">
            <input type="hidden" name="gen-images" value="1" />
            <input id="use-previous-slot" name="use-previous-slot" type="hidden" value="0" />

            <?php if (isset($_SESSION['last_generated_image'][0])): ?>
                <div class="row bg-primary bg-gradient shadow text-white mb-3 pt-2">
                    <div class="col text-center">
                        <h5>Previous Images</h5>
                    </div>
                </div>

                <div class="row">
                    <?php for ($i = 0; $i < count($_SESSION['last_generated_image']); $i++): ?>
                        <div class="col mb-3 ms-1 me-1" style="max-width:300px;">
                            <div class="card">
                                <div class="card-header bg-success bg-gradient opacity-75">
                                    <h6 class="opacity-100">Generated Image <?php echo $i; ?></h6>
                                </div>

                                <div class="card-body">
                                    <img src="<?php echo htmlspecialchars($_SESSION['last_generated_image'][$i]); ?>" class="img-fluid mb-2" style="max-height: 200px;" />

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="use-previous" id="use-previous-<?php echo $i; ?>">
                                        <label class="form-check-label" for="use-previous-<?php echo $i; ?>">Use as generation edit</label>
                                    </div>

                                    <small class="text-muted d-block">
                                        Previous prompt: <?php echo htmlspecialchars($_SESSION['last_image_prompt'][$i] ?? ''); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="dall-e-container" name="dall-e-container" class="container border border-primary text-center" style="display: none;">
            <div class="row bg-primary text-white mb-3">
                <div class="col text-center">
                    <h5>Generate Images</h5>
                </div>
            </div>

            <div class="row mb-3 justify-content-around" style="min-height:200px;">
                <div class="col">
                    <div class="input-group input-group">
                        <span class="input-group-text">
                            <span class="material-symbols-sharp">sort_by_alpha</span>
                        </span>
                        <select class="form-select" id="type" name="type" required>
                            <option value="= Select =" selected disabled>= Select =</option>
                            <option value="eggs">Egg</option>
                            <option value="avatars">Avatar</option>
                            <option value="enemies">Enemy</option>
                        </select>
                    </div>
                </div>

                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text">
                            <span class="material-symbols-sharp">view_in_ar</span>
                        </span>

                        <select id="resolutions" name="resolutions" class="form-select" id="type" name="type" required>

                        </select>
                    </div>
                </div>

                <div class="col-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <span class="material-symbols-sharp">format_list_numbered</span>
                        </span>

                        <input type="number" class="form-control" id="count" name="count" value="1" min="1" max="5">
                    </div>
                </div>
            </div>

            <div class="row mb-3 ps-4 pe-4 justify-content-evenly">
                <div class="col-8"></div>
                <div class="col-6">
                    <div class="input-group">
                        <span class="input-group-text"><span class="material-symbols-sharp">description</span> Prompt</span>
                        <textarea class="form-control" id="image-prompt" name="image-prompt" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-8"></div>
            </div>

            <input type="hidden" id="model" name="model" value="">
            <input type="hidden" id="selected-type" name="selected-type" value="dalle">

            <div class="row mb-3 justify-content-center">
                <div class="col text-center">
                    <button type="submit" class="btn btn-success" id="gen-images" name="gen-images" value="1">Generate Images</button>
                </div>
            </div>
        </div>
    </form>
    <form id="generate-gpt-content" name="generate-gpt-content" method="POST" action="/game?page=administrator&sub=system">
        <div class="container invisible" id="gpt" name="gpt">
            <div class="d-flex container justify-content-evenly border p-3">
                <div class="row bg-primary text-white">
                    <div class="col">
                        <h5>Chat it up why not</h5>
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
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="../js/openai.js"></script>