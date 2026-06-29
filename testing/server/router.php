<?php
$prefix = '/tiendaAbarrotes';
$root = realpath(__DIR__ . '/../../');
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = parse_url($requestUri, PHP_URL_PATH);
$query = parse_url($requestUri, PHP_URL_QUERY);

if ($uri !== null && str_starts_with($uri, $prefix)) {
    $uri = substr($uri, strlen($prefix)) ?: '/';
}

$_SERVER['REQUEST_URI'] = $uri . ($query ? '?' . $query : '');
$_SERVER['SCRIPT_NAME'] = $uri;
$_SERVER['PHP_SELF'] = $uri;

if ($query !== null) {
    parse_str($query, $_GET);
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = realpath($root . '/' . ltrim($path ?: '/', '/'));

if ($file && is_file($file) && str_starts_with($file, $root)) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require $file;
        return true;
    }

    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2'
    ];
    $type = $types[$extension] ?? 'application/octet-stream';
    header("Content-Type: $type");
    readfile($file);
    return true;
}

require $root . '/index.php';
