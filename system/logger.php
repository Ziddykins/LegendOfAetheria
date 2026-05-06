<?php
    use Monolog\Level;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    $log = new Logger('LegendOfAetheria');
    $log->pushHandler(new StreamHandler(LOG_DIRECTORY . '/gamelog.txt', Level::Debug));

?>