<?php
    $ai_button_class = null;
    if (strlen($_ENV['OPENAI_APIKEY']) < 128) {
        $ai_button_class = 'disabled';
    }
?>

                        <div class="container-lg">
                            <div class="row">
                                <div class="col p-4 rounded" style="background-color: rgba(5,5,5,.3);">
                                    <div class="mb-3 row">
                                        <label for="character-name" class="col-form-label fw-bold">Character Name:</label>
                                        <div class="col">
                                            <input type="text" class="form-control" id="character-name" name="character-name" value="<?php echo $character->get_name(); ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="character-race" class="col-form-label fw-bold">Character Race:</label>
                                        <div class="col">
                                            <input type="text" class="form-control" id="character-race" name="character-race" value="<?php echo $character->get_race(); ?>" disabled>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="character-description" class="form-label fw-bold">Character Description:</label>
                                        <textarea class="form-control" id="character-description" name="character-description" rows="3"><?php echo $character->get_description(); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <button  id="generate-description" name="generate-description" class="btn btn-primary border border-black swap-icon <?php echo $ai_button_class; ?>">
                                            <i id="generate-icon" name="generate-icon" class="bi bi-magic"></i> AI Generate
                                        </button>
                                        <button id="save-description" name="save-description" class="btn btn-success border border-black swap-icon">
                                            <i id="save-icon" name="save-icon" class="bi bi-save"></i> Save
                                        </button>
                                        <button id="clear-description" name="clear-description" class="btn btn-warning border border-black swap-icon">
                                            <i id="clear-icon" name="clear-icon" class="bi bi-x-lg"></i> Clear
                                        </button>
                                    </div>
                                </div>
                                <div class="col">

                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="js/profile.js"></script>