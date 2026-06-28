<?php
$prefix = '/tiendaAbarrotes';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri !== null && str_starts_with($uri, $prefix)) {
    $_SERVER['REQUEST_URI'] = substr($uri, strlen($prefix)) ?: '/';
    $_SERVER['SCRIPT_NAME'] = $_SERVER['REQUEST_URI'];
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = realpath(__DIR__ . '/../../' . ltrim($path ?: '/', '/'));

if ($file && is_file($file)) {
    return false;
}

require __DIR__ . '/../../index.php';
