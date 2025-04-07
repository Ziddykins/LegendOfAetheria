<?php
    include 'snippets/snip-charstats.php';

?>
        <div class="d-flex justify-content-center">
            <div class="card ps-3 mb-3 pt-3" style="max-width: 700px;">
                <div class="card-title lead">
                    <?php echo "$char_name the level $char_level $char_race"; ?>
                    
                    <span class="small text-body-secondary float-end pe-3" style="font-size: 10px;">
                        Character created <?php echo $character->get_dateCreated(); ?>
                    </span>
                    
                </div>
                <div class="hr">
                <div class="d-flex">                
                    <div class="flex-shrink-0">
                        <img src="img/avatars/<?php echo $char_avatar; ?>" class="img-fluid rounded m-3" alt="character-avatar" style="width: 256px; height:256px;">
                        <div class="small mb-3 pe-3 text-center">
                            <?php echo "$char_location ($cur_x, $cur_y)"; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="flex-grow-1">
                            <div class="container shadow-sm border">
                                <div class="row mb-3 mt-3 text-center fw-bold">
                                    <div class="col">HP:</div>
                                    <div class="col">MP:</div>
                                    <div class="col">EP:</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <span id="hp" name="hp" class="ldBar label-center" data-value="<?php echo $cur_hp; ?>" data-max="<?php echo $max_hp; ?>" data-type="stroke" data-content="hp" data-stroke="red"></span>
                                    </div>

                                    <div class="col">
                                        <span id="mp" name="mp" class="ldBar label-center" data-max="<?php echo $cur_mp; ?>" data-type="stroke" data-content="mp" data-stroke="blue"></span>
                                    </div>

                                    <div class="col">
                                        <span id="ep" name="ep" class="ldBar label-center" data-value="<?php echo $cur_ep; ?>" data-max="<?php echo $max_ep; ?>" data-type="stroke" data-content="ep" data-stroke="yellow"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="container border shadow-sm">
                                <div class="row mb-3 mt-3">
                                    <span class="col">
                                        <i class="bi bi-hammer me-3"></i>
                                        <span class="d-none d-md-inline">Strength</span>
                                    </span>
                                    <span class="col"><?php echo $char_str; ?></span>
                                </div>

                                <div class="row mb-3">
                                    <span class="col">
                                        <i class="bi bi-shield-fill me-3"></i>
                                        <span class="d-none d-md-inline">Defense</span>
                                    </span>
                                    <span class="col"><?php echo $char_def; ?></span>
                                </div>

                                <div class="row mb-3">
                                    <span class="col">
                                        <i class="bi bi-journal-bookmark-fill me-3"></i>
                                        <span class="d-none d-md-inline small">Intelligence</span>
                                    </span>
                                    <span class="col"><?php echo $char_int; ?></span>
                                </div>
                            </div>
                            
                            <div class="container border shadow-sm">
                                <div class="row mb-3 mt-3">
                                    <div class="col">
                                        Alignment: <?php echo $char_align; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        Gold&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $character->get_gold(); ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        Floor&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $character->get_floor(); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-4">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="/js/loading-bar.js"></script>