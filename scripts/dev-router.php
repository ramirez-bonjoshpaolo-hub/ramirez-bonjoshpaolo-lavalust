<?php
// Router for php -S 127.0.0.1:3000 -t public scripts/dev-router.php.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$public = realpath(__DIR__ . '/../public');
$file = realpath($public . $path);
if ($file && str_starts_with($file, $public . DIRECTORY_SEPARATOR) && is_file($file)) return false;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php' . $path;
require $public . '/index.php';
