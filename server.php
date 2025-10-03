<?php
// public file passthrough for PHP's built-in server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$public = __DIR__ . '/public';

if ($uri !== '/' && file_exists($public . $uri)) {
    return false; // let the server serve static files
}

require $public . '/index.php';