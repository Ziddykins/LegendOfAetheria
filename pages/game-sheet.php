<?php
    $hp_textcolor = 'text-white';
    $mp_textcolor = 'text-white';
    $ep_textcolor = 'text-black';

    $cur_hp       = $character['hp'];
    $cur_mp       = $character['mp'];
    /* $cur_ep       = $character['ep']; */

    $max_hp       = $character['max_hp'];
    $max_mp       = $character['max_mp'];
    /* $max_ep       = $character['max_ep']; */

    if (50 >= $cur_hp / $max_hp * 100) {
        $hp_textcolor = 'text-black';
    }

    if (50 >= $cur_mp / $max_mp * 100) {
        $mp_textcolor = 'text-black';
    }

    /* EP actually looks fine only black
        if (50 <= $cur_ep / $max_ep * 100) {
            $ep_textcolor = 'text-black';
        }
    */

?>
<script>
    $(document).ready(
        function() {
            $("head").append("<!-- Dynamically loaded content -->");
            $("head").append("<link rel='stylesheet' href='../css/refracted-text.css'>");
        }
    );
</script>
<p></p>
<div class="container text-center d-flex justify-content-center align-items-center">
    <div class="row">
        <div class="col">
            <div class="card mb-3" style="max-width: 700px;">
                <div class="row g-0">
                    <div class="col-4">
                        <img src="img/avatars/<?php echo $character['avatar']; ?>" class="img-fluid rounded m-3" alt="character-avatar">
                    </div>
                    <div class="col-6">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $character['name']; ?></h5>
                            <div class="container">
                                <div class="row mb-3">
                                    <div class="col-4 truncate">
                                        Health
                                    </div>
                                    <div class="col-4 truncate">
                                        Mana
                                    </div>
                                    <div class="col-4 truncate">
                                        Energy
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <span id="hp" name="hp"   
                                            class="ldBar label-center <?php echo $hp_textcolor; ?>"
                                            data-value="<?php echo $character['hp']; ?>"
                                            data-max="<?php echo $character['max_hp']; ?>"
                                            data-preset="energy"
                                            data-pattern-size="120"
                                        >
                                    </div>
                                    <div class="col-4">
                                        <span id="mp" name="mp"
                                            class="ldBar label-center <?php echo $mp_textcolor; ?>"
                                            data-value="<?php echo $character['mp']; ?>"
                                            data-max="<?php echo $character['max_mp']; ?>"
                                            data-preset="energy"
                                            data-pattern-size="120"
                                        >
                                    </div>
                                    <div class="col-6">
                                        <span id="ep" name="ep"   
                                            class="ldBar label-center <?php echo $ep_textcolor; ?>"
                                            data-value="<?php echo $character['ep']; ?>"
                                            data-max="<?php echo $character['max_ep']; ?>"
                                            data-preset="energy"
                                            data-pattern-size="120"
                                        >
                                    </div>
                                </div>

                                <hr style="opacity: .25; align-self: center;">
                                
                                <div class="row mb-3">
                                    <div class="col-4 text-truncate">
                                        Strength
                                    </div>
                                     <div class="col-4 text-truncate">
                                        Defense
                                    </div>
                                   <div class="col-4 text-truncate">
                                        Intelligence
                                    </div>
                                </div>
                                <div class="row mb-3 fs-1">
                                   <div class="col-4">
                                        <i class="bi bi-hammer"></i>
                                    </div>
                                    <div class="col-4 fs-1">
                                        <i class="bi bi-shield-fill"></i>
                                    </div>
                                    <div class="col-4 fs-1">
                                        <i class="bi bi-journal-bookmark-fill"></i>
                                    </div>
                                </div>
                                <div class="row mb-3 fs-1">
                                    <div class="col-4">
                                        <?php echo $character['str']; ?>
                                    </div>
                                    <div class="col-4 fs-1">
                                        <?php echo $character['def']; ?>
                                    </div>
                                    <div class="col-4 fs-1">
                                        <?php echo $character['int']; ?>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text"><small class="text-body-secondary">Character created on <?php echo $account['date_registered']; ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
