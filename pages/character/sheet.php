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
                <div class="card ps-3 mb-3 pt-3">
                    <div class="card-title lead text-lg-center">
                        <?php echo "$char_name the level $char_level {$char_race->name}"; ?><p></p>
				    </div>

					<div class="hr">
						<div class="d-grid d-lg-flex">
							<div class="flex-shrink-0">
								<img class="mb-3" src="img/avatars/<?php echo $char_avatar; ?>" class="img-fluid rounded" alt="character-avatar" style="width: 256px; height:256px;">
								<div class="small text-center mb-3">
									<?php echo "$char_location ($cur_x, $cur_y)"; ?>
									<div class="small text-body-secondary" style="font-size: 10px;">
										Character created <?php echo $character->get_dateCreated(); ?>
									</div>
								</div>
							</div>
							
							<div class="card-body">
								<div class="d-flex-grow-1">
									<div>
										<div class="container framed-grey shadow-sm border align-items-center">
											<div class="row mb-3 mt-3">
												<div class="col text-center">
													<span class="bi bi-bookmark-heart-fill text-danger"></span><span class="fw-bold ms-2">HP: <?php echo "$cur_hp"; ?></span><span class="d-none fw-bold d-lg-inline"> <?php echo "/ $max_hp"; ?></span>
												</div>
												<div class="col text-center">
													<span class="bi bi-bookmark-star-fill text-primary"></span><span class="fw-bold ms-2">MP: <?php echo "$cur_mp"; ?></span><span class="d-none fw-bold d-lg-inline"> <?php echo "/ $max_mp"; ?></span>
												</div>
												<div class="col text-center">
													<span class="bi bi-bookmark-plus-fill"></span><span class="fw-bold ms-2">EP: <?php echo "$cur_ep"; ?></span><span class="d-none fw-bold d-lg-inline"> <?php echo "/ $max_ep"; ?></span>
												</div>
											</div>
										</div>
									</div>

									<div class="container border">
										<div class="row">
											<?php foreach ($stats_map as $stats): ?>
												<div class="col-6 mb-2 mt-2 px-3" data-bs-toggle="tooltip" data-bs-title="<?php echo $stats['description']; ?>">
													<div class="d-flex align-items-center">
														<span class="material-symbols-outlined me-2" style="font-size: 18px; width: 24px; flex-shrink: 0;"><span class="text-<?php echo $stats['color']?>"><?php echo $stats['icon']; ?></span></span>
														<span class="small" style="min-width: 110px;"><?php echo $stats['name']; ?>:</span>
														<span class="fw-bold small"><?php echo $stats['value']; ?></span>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
									
									<div class="container justify-content-center border shadow-sm">
										<div class="row mb-2 mt-2">
											<div class="d-flex align-items-center col text-center" data-bs-toggle="tooltip" data-bs-title="Your moral alignment score">
												<img src="img/svg/alignment.svg" class="me-2" style="width: 24px; height: 24px; flex-shrink: 0;" alt="alignment">
												<span class="fw-bold ms-2">Alignment: <?php echo $char_align; ?></span>
											</div>

											<div class="d-flex col text-center align-items-center" data-bs-toggle="tooltip" data-bs-title="Current gold on-hand">
												<span class="material-symbols-outlined me-2" style="font-size: 18px; width: 24px; flex-shrink: 0;">monetization_on</span>
												<span class="fw-bold ms-2">Gold: <?php echo $character->get_gold(); ?></span>
											</div>

											<div class="d-flex align-items-center col text-center" data-bs-toggle="tooltip" data-bs-title="Current dungeon floor">
												<span class="material-symbols-outlined me-2" style="font-size: 18px; width: 24px; flex-shrink: 0;">stairs</span>
												<span class="fw-bold ms-2">Floor: <?php echo $character->get_floor(); ?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		  
