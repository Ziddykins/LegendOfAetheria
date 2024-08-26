<?php
    $cur_hp       = $character['hp'];
    $cur_mp       = $character['mp'];
    $cur_ep       = $character['ep'];

    $max_hp       = $character['max_hp'];
    $max_mp       = $character['max_mp'];
    $max_ep       = $character['max_ep'];

    $ep_icon  = 'bi-battery-full';
    $ep_color = 'success';
    $ep_percent_full = ($cur_ep / $max_ep) * 100;
    
    if ($ep_percent_full > 25 && $ep_percent_full < 75) {
        $ep_icon  = 'bi-battery-half';
        $ep_color = 'warning';
    } elseif ($ep_percent_full >= 0 && $ep_percent_full <= 25) {
        $ep_icon  = 'bi-battery';
        $ep_color = 'danger';
    }

    $ap = $character['ap'];
?>

<div class="offcanvas offcanvas-end" data-bs-theme="dark" tabindex="-1" id="offcanvas-summary" aria-labelledby="offcanvas-summary-label">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title josefin-slab-text" id="offcanvas-summary-label">Summary</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="h3 josefin-slab-text">
                            <?php if ($ap): ?>
                                <i class="bi bi-capslock-fill text-success"></i>
                            <?php else: ?>
                                <i class="bi bi-capslock"></i>
                            <?php endif; ?>
                            <u>
                                <?php echo $character['name']; ?>
                            </u>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="d-flex flex-column josefin-slab-text">
                    <span><i class="bi bi-heart"></i> <span class="major-mono-display-regular">H</span>ealth: <?php echo "$cur_hp/$max_hp"; ?></span>
                    <span><i class="bi bi-bookmark-star"></i> <span class="major-mono-display-regular">M</span>agick: <?php echo "$cur_mp/$max_mp"; ?></span>
                    <span><?php echo "<i class=\"bi $ep_icon $ep_color\"></i>"; ?> <span class="major-mono-display-regular">E</span>nergy: <?php echo "$cur_ep/$max_ep"; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        $(".ldBar-label").css("top", "-24%");

</script>
    

  