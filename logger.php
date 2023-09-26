<?php
    use Monolog\Level;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    $log = new Logger('LegendOfAetheria');
    $log->pushHandler(new StreamHandler(__DIR__ . '/gamelog.txt', Level::Info));

?>