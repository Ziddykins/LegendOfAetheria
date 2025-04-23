<?php

use Game\Components\Modals\Enums\ModalButtonType;
    include "snippets/snip-charstats.php";

?>

    <div class="offcanvas offcanvas-end" data-bs-theme="dark" tabindex="-1" id="offcanvas-summary" aria-labelledby="offcanvas-summary-label">
        <div class="offcanvas-header">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body">
            <div class="container">
                <div class="d-flex justify-items-center align-items-center">
                    <div class="col">
                        <?php $ap = $character->stats->get_ap(5); ?>
                        <div class="h3 josefin-slab-text">
                                <?php if ($ap): ?>
                                    <i class="bi bi-capslock-fill text-success"></i>
                                <?php else: ?>
                                    <i class="bi bi-capslock"></i>
                                <?php endif; ?>
                                <u>
                                    <?php echo $character->get_name(); ?>
                                </u>
                        <div class="h3">
                            <?php if ($ap): ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#ap-spend-modal">
                                    <i id="test" class="bi bi-capslock-fill text-success"></i>
                                </a>
                                <script>bootstrap.Modal.Default.backdrop = false; </script>
                                <?php
                                    echo generate_modal('ap-spend', 'success', 'Assign AP', $ap_modal_body, ModalButtonType::OKCANCEL);
                                ?>
                            <?php else: ?>
                                <i class="bi bi-capslock"></i>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="row float-right">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="small" style="font-size: 10px;"><?php echo $location; ?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="small" style="font-size: 10px;">x <?php echo $cur_x; ?>, y <?php echo $cur_y; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="d-flex flex-column align-items-start">
                        <span>
                            <i class="bi bi-heart"></i>&ensp;
                            <span>HP: <?php echo "$cur_hp/$max_hp"; ?></span>
                        </span>
                        <span>
                            <i class="bi bi-bookmark-star"></i>&ensp;
                            MP: <?php echo "$cur_mp/$max_mp"; ?>
                        </span>
                        <span>
                            <?php echo "<i class=\"bi $ep_icon $ep_color\"></i>"; ?>&ensp;
                            <span>EP: <?php echo "$cur_ep/$max_ep"; ?></span>
                        </span>
                    </div>

                    <div class="d-flex flex-column align-items-center">
                        <span>
                            <i class="bi bi-caret-up"></i>&ensp;
                            <span>Expr: <?php echo $cur_xp; ?></span>
                        </span>
                        <span>
                            <i class="bi bi-caret-up-fill"></i>&ensp;
                            <span>Next: <?php echo $next_lvl; ?></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>