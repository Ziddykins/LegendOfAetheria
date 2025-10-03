<?php
    include 'snippets/snip-charstats.php';
    $index = 1;
    $max_index = count($stats_map);

    if ($character->stats->get_ap() > 0) {
        $href = '';
    }
?>
        <div class="d-flex justify-content-center">
            
            <div class="container"style="max-width: 850px;">
                <div class="card ps-3 mb-3 pt-3"  ">
                    <div class="card-title lead">
                        <?php echo "$char_name the level $char_level {$char_race->name}"; ?>
                        
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
                                <div class="rpgui-content">
                                    <div class="rpgui-container container framed-grey shadow-sm border">
                                        <div class="row mb-3 mt-3 text-center fw-bold">
                                            <div class="col">HP:</div>
                                            <div class="col">MP:</div>
                                            <div class="col">EP:</div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col">
                                                <div id="health-bar" class="rpgui-progress red"></div>
                                                <script>
                                                    let health_bar = document.getElementById('health-bar');
                                                    
                                                    RPGUI.set_value(health_bar, <?php echo $hp_percent_full; ?>);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid border justify-content-evenly">
                                    <?php
                                    $index = 0;
                                    $total = count($stats_map);
                                    foreach ($stats_map as $stats):
                                        if ($index % 3 === 0): ?>
                                            <div class="d-flex justify-content-evenly small mb-3 ms-3 mt-3 me-3 fs-3">
                                        <?php endif; ?>

                                        
                                            <a class="d-flex align-items-center link-underline link-underline-opacity-0 text-white" href="#" data-bs-toggle="tooltip" data-bs-title="<?php echo $stats['name']; ?>"><span class="material-symbols-outlined me-2 ms-3"><?php echo $stats['icon']; ?></span>

                                            <span class="ms-auto fw-bold me-3"><?php echo $stats['value']; ?></span></a>


                                        <?php
                                        $index++;
                                        if ($index % 3 === 0 || $index === $total): ?>
                                            </div>
                                        <?php endif;
                                    endforeach; ?>
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
        </div>
        

