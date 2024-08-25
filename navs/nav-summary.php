<?php
    $cur_hp       = $character['hp'];
    $cur_mp       = $character['mp'];
    $cur_ep       = $character['ep'];

    $max_hp       = $character['max_hp'];
    $max_mp       = $character['max_mp'];
    $max_ep       = $character['max_ep'];

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
                <div class="d-flex align-items-center col josefin-slab-text">
                    <i class="bi bi-heart"></i>  Health: <?php echo "$cur_hp/$max_hp"; ?>

                    
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        $(".ldBar-label").css("top", "-24%");

</script>
    

  