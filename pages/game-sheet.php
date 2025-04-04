<div?php
    $hp_textcolor = $mp_textcolor = $ep_textcolor = 'text-black';

    $cur_hp = $character->stats->get_hp();
    $cur_mp = $character->stats->get_mp();
    $cur_ep = $character->stats->get_ep();

    $max_hp = $character->stats->get_maxHP();
    $max_mp = $character->stats->get_maxMP();
    $max_ep = $character->stats->get_maxEP();

    $hp_textcolor = ($cur_hp / $max_hp * 100 <d= 50) ? 'text-white' : $hp_textcolor;
    $mp_textcolor = ($cur_mp / $max_mp * 100 <= 50) ? 'text-white' : $mp_textcolor;
    $ep_textcolor = ($cur_ep / $max_ep * 100 <= 50) ? 'text-white' : $ep_textcolor;
?>

<p></p>

<div class="container text-center d-flex justify-content-center align-items-center">
    <div class="row">
        <div class="col">
            <div class="card mb-3" style="max-width: 700px;">
                <div class="row g-0">
                    <div class="col-4">
                        <img src="img/avatars/<?= $character->get_avatar(); ?>" class="img-fluid rounded m-3" alt="character-avatar">
                    </div>
                    
                    <div class="col-6">
                        <div class="card-body">
                            <h5 class="card-title"><?= $character->get_name(); ?></h5>
                            <div class="container">
                                <div class="row mb-3">
                                    <div class="col-4">
                                        Name:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        EP:
                                    </div>
                                    
                                    <div class="col-4">
                                        <span id="ep" name="ep" class="ldBar label-center <?= $ep_textcolor; ?>"
                                            data-value="<?= $cur_ep; ?>"
                                            data-max="<?= $max_ep; ?>"
                                            data-preset="energy"
                                            data-pattern-size="120">
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-4">
                                        EP:
                                    </div>
                                    
                                    <div class="col-4">
                                        <span id="ep" name="ep" class="ldBar label-center <?= $ep_textcolor; ?>"
                                            data-value="<?= $cur_ep; ?>"
                                            data-max="<?= $max_ep; ?>"
                                            data-preset="energy"
                                            data-pattern-size="120">
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        EP:
                                    </div>
                                    
                                    <div class="col-4">
                                        <span id="ep" name="ep" class="ldBar label-center <?= $ep_textcolor; ?>"
                                            data-value="<?= $cur_ep; ?>"
                                            data-max="<?= $max_ep; ?>"
                                            data-preset="energy"
                                            data-pattern-size="120">
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        Level:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        Race:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        X, Y:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        Location:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        Alignment:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        Gold:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        Floor:
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        
                                    </div>
                                </div>
                                

                                <hr style="opacity: .25; align-self: center;">
                                
                                <div class="row mb-3">
                                    <div class="col text-truncate">Strength</div>
                                    <div class="col text-truncate">Defense</div>
                                    <div class="col text-truncate">Intelligence</div>
                                </div>
                                <div class="row mb-3 fs-1">
                                    <div class="col-4"><i class="bi bi-hammer"></i></div>
                                    <div class="col-4 fs-1"><i class="bi bi-shield-fill"></i></div>
                                    <div class="col-4 fs-1"><i class="bi bi-journal-bookmark-fill"></i></div>
                                </div>
                                <div class="row mb-3 fs-1">
                                    <div class="col-4"><?= $character->stats->get_str(); ?></div>
                                    <div class="col-4 fs-1"><?= $character->stats->get_def(); ?></div>
                                    <div class="col-4 fs-1"><?= $character->stats->get_int(); ?></div>
                                </div>
                            </div>
                            <p class="card-text"><small class="text-body-secondary">Character created on <?= $character->get_dateCreated(); ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/loading-bar.js"></script>