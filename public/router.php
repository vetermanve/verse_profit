<?php
chdir(__DIR__ . '/..');

$uri = $_SERVER["REQUEST_URI"];
$uri = strpos($uri, '?') !== false ? strstr($uri, '?', true) : $uri;

if (preg_match('/\.(?:|css|js|map|png|jpg|jpeg|gif|ico)$/', $uri)) {
    return false;
}

include 'index.php';

