<?php
declare(strict_types=1);

error_reporting(E_ALL);

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/models/Usuario.php';
require_once PROJECT_ROOT . '/models/Producto.php';
require_once PROJECT_ROOT . '/models/Proveedor.php';
require_once PROJECT_ROOT . '/models/Auditoria.php';
require_once __DIR__ . '/Support/FakeDatabase.php';
