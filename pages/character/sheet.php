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
                                <div>
                                    <div class="container framed-grey shadow-sm border align-items-center">
                                        <div class="row mb-3 mt-3">
                                            <div class="col text-center">
                                                <span class="fw-bold">HP:</span> <?php echo "$cur_hp / $max_hp"; ?>
                                            </div>
                                            <div class="col text-center">
                                                <span class="fw-bold">MP:</span> <?php echo "$cur_mp / $max_mp"; ?>
                                            </div>
                                            <div class="col text-center">
                                                <span class="fw-bold">EP:</span> <?php echo "$cur_ep / $max_ep"; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="container border">
                                    <div class="row">
                                        <?php foreach ($stats_map as $stats): ?>
                                            <div class="col-6 mb-2 mt-2 px-3" data-bs-toggle="tooltip" data-bs-title="<?php echo $stats['description']; ?>">
                                                <div class="d-flex align-items-center">
                                                    <span class="material-symbols-outlined me-2" style="font-size: 18px; width: 24px; flex-shrink: 0;"><?php echo $stats['icon']; ?></span>
                                                    <span class="small" style="min-width: 110px;"><?php echo $stats['name']; ?>:</span>
                                                    <span class="fw-bold small"><?php echo $stats['value']; ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <div class="container border shadow-sm">
                                    <div class="row mb-2 mt-2">
                                        <div class="col px-3" data-bs-toggle="tooltip" data-bs-title="Your moral alignment score">
                                            <div class="d-flex align-items-center">
                                                <img src="img/svg/alignment.svg" class="me-2" style="width: 24px; height: 24px; flex-shrink: 0;" alt="alignment">
                                                <span class="small" style="min-width: 110px;">Alignment:</span>
                                                <span class="fw-bold small"><?php echo $char_align; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col px-3">
                                            <div class="d-flex align-items-center">
                                                <span class="material-symbols-outlined me-2" style="font-size: 18px; width: 24px; flex-shrink: 0;">monetization_on</span>
                                                <span class="small" style="min-width: 110px;">Gold:</span>
                                                <span class="fw-bold small"><?php echo $character->get_gold(); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col px-3">
                                            <div class="d-flex align-items-center">
                                                <span class="material-symbols-outlined me-2" style="font-size: 18px; width: 24px; flex-shrink: 0;">stairs</span>
                                                <span class="small" style="min-width: 110px;">Floor:</span>
                                                <span class="fw-bold small"><?php echo $character->get_floor(); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
