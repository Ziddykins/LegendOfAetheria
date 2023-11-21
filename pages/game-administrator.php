<?php
use Orhanerday\OpenAi\OpenAi;

$open_ai_key = $_ENV['OPENAI_APIKEY'];
$open_ai     = new OpenAi($open_ai_key);
$count = 0;
$type  = 'enemies';
$user_privs = UserPrivileges::name_to_enum($account['privileges']);

if ($user_privs->value <= UserPrivileges::MODERATOR->value) {
    echo "F O R B I D D E N";
    exit();
}

if (isset($_REQUEST['gen-images']) && $_REQUEST['gen-images'] == 1) { 
    $type   = $_REQUEST['type'];
    $count  = $_REQUEST['count'];
    $prompt = $_REQUEST['prompt'];

    if ($count < 1 || $count > 5) {
        $count = 2;
    }

    if ($type !== 'eggs' and $type !== 'enemies' and $type !== 'avatars') {
        echo "Invalid type";
        exit();
    }

    $complete = $open_ai->image([
        "prompt" => $prompt,
        "n" => (int)$count,
        "size" => "256x256",
        "response_format" => "b64_json",
    ]);
    $json_obj = json_decode($complete);
}
?>
<form id="generate-images" name="generate-images" method="POST" action="?page=administrator">
        <div class="row">
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

        <div class="row"> 
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

        <div class="row">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="generate-images-icon">
                        <span class="material-symbols-sharp">recent_actors</span>
                    </span>

                    <input type="number" id="count" name="count" value="5">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <button class="btn btn-success" id="gen-images" name="gen-images" value="1">Generate</button>
            </div>
        </div>
    </div>
</form>

<div class="d-flex container justify-content-evenly">
<?php
    $count = 1;
    foreach ($json_obj->data as $image) {
        foreach ($image as $property) {            
               echo '<div class="row">
                    <div class="col">
                        <h5>Image ' . $count . '</h5>
                    </div>
                    <div class="col p-2 m-2 border">
                        <img src="data:image/png;base64,' . $property . '" alt="user-generated-' . $i . '>"></img>
                    </div>
                </div>
            </div>';
        }
        $count++;
    }
?>
