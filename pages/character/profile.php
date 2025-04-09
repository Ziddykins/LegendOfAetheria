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
                                        <textarea class="form-control" id="character-description" name="character-description" rows="3" width><?php echo $character->get_description(); ?></textarea>
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

                    <script>
                    let swap_icons = document.querySelectorAll(".swap-icon");

                        swap_icons.forEach((element) => {
                            element.addEventListener('click', (btn) => {
                                let icon_par = element.parentElement;
                                let id = '#' + $(element)[0].children[0].id;

                                if (btn.target.id == 'clear-icon') {
                                    if (confirm("Are you sure you want to clear the character description?")) {
                                        document.getElementById("character-description").textContent = "";
                                    }
                                } else {
                                    let do_ajax = 0;
                                    let data    = null;
                                    let url     = null;

                                    $(id).hide();
                                    document.querySelector(id).style.display = 'none';
                                    element.insertAdjacentHTML('afterbegin', '<span id="spinner" class="spinner-border spinner-border-sm"></span>');

                                    if (id == "#save-icon") {
                                        url = "/game?page=save";
                                        data = {
                                            data: document.getElementById("character-description").textContent
                                            id: <?php echo $character->get_id(); ?>,
                                            data: JSON.stringify(document.getElementById("character-description").textContent)
                                        };
                                        do_ajax = 1;
                                    } else if (id == "#generate-icon") {
                                        url = "openai";
                                        data = { 
                                            characterID: <?php echo $character->get_id(); ?>,
                                            accountID: <?php echo $account->get_id(); ?>,
                                            generate_description: 1
                                        };
                                        do_ajax = 1;
                                    }

                                    if (do_ajax) {
                                        $.ajax({
                                            type: "POST",
                                            url: url,
                                            data: data,
                                            dataType: "json"
                                        }).done(function (response) {
                                            if (response && response.responseText) {
                                                document.querySelector("#character-description").value = response.responseText;
                                            } else {
                                                console.error("Invalid response received:", response);
                                                alert("Failed to update character description. Please try again.");
                                            }
                                        }).fail(function (jqXHR, textStatus, errorThrown) {
                                            console.error("AJAX request failed:", textStatus, errorThrown);
                                            alert("An error occurred while processing your request. Please try again.");
                                        }).always(function () {
                                            $("#spinner").remove();
                                            $(id).show();
                                            icon_par.classList.remove('disabled');
                                            console.log(icon_par);
                                        });
                                    }
                                }
                            });
                        });
                    </script>