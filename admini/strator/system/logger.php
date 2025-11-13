<?php
    use Monolog\Level;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    $log = new Logger('logger');
    $log->pushHandler(new StreamHandler(LOG_DIRECTORY . '\debug.log', Level::Debug));