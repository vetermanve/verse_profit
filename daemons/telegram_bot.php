<?php

include_once __DIR__.'/../vendor/autoload.php';
chdir(__DIR__.'/..');

(new \Dotenv\Dotenv(\getcwd()))->safeLoad();

$runner = new \Run\Runner\TelegramRunner();
$runner->run();