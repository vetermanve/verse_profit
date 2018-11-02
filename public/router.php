<?php
chdir(__DIR__ . '/..');

$uri = $_SERVER["REQUEST_URI"];
$uri = strpos($uri, '?') !== false ? strstr($uri, '?', true) : $uri;

if (file_exists(__DIR__.$uri)) {
    return false;
}

include 'index.php';

