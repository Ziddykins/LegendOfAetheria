<?php
    $hp_textcolor = 'text-black';
    $mp_textcolor = 'text-black';
    $ep_textcolor = 'text-black';

    $cur_hp       = $character->stats->get_hp();
    $cur_mp       = $character->stats->get_mp();
    $cur_ep       = $character->stats->get_ep();

    $max_hp       = $character->stats->get_maxHp();
    $max_mp       = $character->stats->get_maxMp();
    $max_ep       = $character->stats->get_maxEp();

    if (50 >= $cur_hp / $max_hp * 100) {
        $hp_textcolor = 'text-white';
    }

    if (50 >= $cur_mp / $max_mp * 100) {
        $mp_textcolor = 'text-white';
    }

    if (50 >= $cur_ep / $max_ep * 100) {
        $ep_textcolor = 'text-white';
    }
?>

<p></p>

<div class="container text-center d-flex justify-content-center align-items-center">
    <div class="row">
        <div class="col">
            <div class="card mb-3" style="max-width: 700px;">
                <div class="row g-0">
                    <div class="col-4">
                        <img src="img/avatars/<?php echo $character->get_avatar(); ?>" class="img-fluid rounded m-3" alt="character-avatar">
                    </div>

                    <div class="col-6">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $character->get_name(); ?></h5>
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
                                            data-value="<?php echo $character->stats->get_hp(); ?>"
                                            data-max="<?php echo $character->stats->get_maxHp(); ?>"
                                            data-preset="bubble"
                                            data-pattern-size="120"
                                        >
                                    </div>
                                    <div class="col-4">
                                        <span id="mp" name="mp"
                                            class="ldBar label-center <?php echo $mp_textcolor; ?>"
                                            data-value="<?php echo $character->stats->get_mp(); ?>"
                                            data-max="<?php echo $character->stats->get_maxMp(); ?>"
                                            data-preset="stripe"
                                            data-pattern-size="120"
                                        >
                                    </div>
                                    <div class="col-4">
                                        <span id="ep" name="ep"   
                                            class="ldBar label-center <?php echo $ep_textcolor; ?>"
                                            data-value="<?php echo $character->stats->get_ep(); ?>"
                                            data-max="<?php echo $character->stats->get_maxEp(); ?>"
                                            data-preset="energy"
                                            data-pattern-size="120"
                                        >
                                    </div>
                                </div>

                                <hr style="opacity: .25; align-self: center;">
                                
                                <div class="row mb-3">
                                    <div class="col text-truncate">
                                        Strength
                                    </div>
                                     <div class="col text-truncate">
                                        Defense
                                    </div>
                                   <div class="col text-truncate">
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
                                        <?php echo $character->stats->get_str(); ?>
                                    </div>
                                    <div class="col-4 fs-1">
                                        <?php echo $character->stats->get_def(); ?>
                                    </div>
                                    <div class="col-4 fs-1">
                                        <?php echo $character->stats->get_int(); ?>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text"><small class="text-body-secondary">Character created on <?php echo $account->get_dateRegistered(); ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/loading-bar.js"></script>