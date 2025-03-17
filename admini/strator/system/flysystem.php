<?php
    $adapter = new League\Flysystem\Local\LocalFilesystemAdapter(UPLOAD_DIRECTORY);
    $filesystem = new League\Flysystem\Filesystem($adapter);