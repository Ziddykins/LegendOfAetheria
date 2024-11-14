<?php
    function gen_achievements() {
        global $monster_pool;

        $intervals = [5, 10, 100, 250, 500, 1000];
        $count = 0;

        foreach ($monster_pool->monsters as $monster) {
            foreach ($intervals as $interval) {
                $name_ns = str_replace(' ', '', $monster->get_name());

                if ($count++ % 3 == 0) {
                    echo '<br>';
                }

                $img = 'img/enemies/' . $name_ns . '.png';

                $html = '<div class="card mb-3 me-3" style=" min-width: 400px; max-width: 400px;">
                            <div class="row g-1">
                                <div class="col-md-4 p-3">
                                    <img src="' . $img . '" class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $monster->get_name() . '</h5>
                                        <p class="card-text">Kill ' . $interval . ' ' . $monster->get_name() . '\'s</p>
                                        <p class="card-text"><small class="text-body-secondary"><div class="ldBar" data-value="' . rand(0,100) . '"></div></small></p>
                                    </div>
                                </div>
                            </div>
                        </div>';
                echo $html;
            }            
        }
    }
?>
 
<div class="h1 text-center">Achievements</div>

<div class="h2 text-center">Completed</div>
None
<div class="h2 text-center">Incomplete</div>

<div class="container">
    <div class="d-flex flex-wrap">
        <?php gen_achievements(); ?>
    </div>
</div>