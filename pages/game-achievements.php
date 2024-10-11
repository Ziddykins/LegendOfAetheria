<?php
    function gen_achievements() {
        global $monster_pool;

        $intervals = [5, 10, 100, 250, 500, 1000];
        $count = 0;

        foreach ($monster_pool->monsters as $monster) {
            foreach ($intervals as $interval) {
                $html = '<div class="card mb-3 me-3" style="max-width: 400px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="img/enemies/enemy-kobold.webp" class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $monster->get_name() . '</h5>
                                        <p class="card-text">Kill ' . $interval . ' ' . $monster->get_name() . '\'s</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>';
                echo $html;
            }            
        }
    }
?>

<div class="container">
    <div class="d-flex flex-wrap">
        <?php gen_achievements(); ?>
    </div>
</div>