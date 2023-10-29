<?php

?>

<div class="card mb-3" style="max-width: 800px;">
    <div class="row g-0">
        <div class="col-4">
            <img src="img/avatars/<?php echo $character['avatar']; ?>" class="img-fluid rounded-start" alt="...">
        </div>
        <div class="col">
            <div class="card-body">
                <h5 class="card-title text-center"><?php echo $character['name']; ?></h5>
                <div class="container">
                    <div class="row">
                        <div class="col">
                            Health
                        </div>
                        <div class="col">
                            Mana
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="ldBar" data-value="<?php echo $character['hp']; ?>" data-preset="bubble" data-pattern-size="120">
                        </div>
                        <div class="col">
                            <div class="ldBar" data-value="<?php echo $character['mp']; ?>" data-preset="bubble" data-pattern-size="120">
                        </div>
                    </div>
                </div>
                <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
            </div>
        </div>
    </div>
</div>
