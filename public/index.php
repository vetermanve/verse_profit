<?php

chdir(__DIR__.'/..');
require_once __DIR__.'/../vendor/autoload.php';

(new \Dotenv\Dotenv(\getcwd()))->safeLoad();

$runner = new \Run\Runner\HttpRunner();
$runner->run();