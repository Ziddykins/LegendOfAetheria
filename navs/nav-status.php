<?php
    $hp = $character['hp'];
    $max_hp = $character['max_hp'];
    $icon = 'bi bi-emoji-smile-fill';
    $color = 'text-success';
    
    if ($hp == 0) {
        $icon = 'bi bi-emoji-dizzy-fill';
        $color = 'text-danger';
    }

    $weather = get_globals('weather');
    $weather_eval = Weather::name_to_value($weather);
    $weather_string = '<div class="col">';
    $weather_string .= Weather::from($weather_eval)->icon();
    $weather_string .= ' ' . Weather::from($weather_eval)->name;
    $weather_string .= '</div> |';

    $status_string = "<div class=\"col $color\"><i class=\"bi $icon\"></i>$hp/$max_hp</div> |";

?>
                    <div class="row bg-black justify-content-center text-dark-emphasis">
                        <div class="col"><i class="bi bi-currency-exchange text-warning"></i> <?php echo $character['gold']; ?></div> |
                        <?php echo $status_string; ?>
                        <?php echo $weather_string; ?>
                        <div class="col"><i class="bi bi-ladder"></i> 42</div> |
                        <div class="col"><i class="bi bi-envelope-fill"></i> 0</div> |
                        <div class="col"><i class="bi bi-clipboard-fill"></i> 5</div> |
                        <div class="col"><i class="bi bi-box2-heart-fill"></i><span class="text-warning"> 42/50</span></div> |
                        <div class="col"><i class="bi bi-bookmark-fill"></i> 7</div> |
                        <div class="col text-white"><i class="bi bi-hourglass-split"></i><span id="tick-left"></span></div> |
                        <div class="col" id="ep-status" name="ep-status">
                            <span id="ep-icon"></span>
                            <span id="ep-value">20</span>/<span id="ep-max">100</span>
                        </div>
                    </div>