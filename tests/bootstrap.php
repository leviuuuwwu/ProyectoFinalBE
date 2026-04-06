<?php

declare(strict_types=1);

$base = dirname(__DIR__);

if (! is_file($base.DIRECTORY_SEPARATOR.'.env')) {
    file_put_contents($base.DIRECTORY_SEPARATOR.'.env', <<<'ENV'
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:RhOTfF/hi1B1nI1IstMscSe33dl4GwfToP9b0RPrq7M=
APP_DEBUG=true
APP_URL=http://localhost
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
SESSION_DRIVER=array
CACHE_STORE=array
QUEUE_CONNECTION=sync

ENV);
}

require $base.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
