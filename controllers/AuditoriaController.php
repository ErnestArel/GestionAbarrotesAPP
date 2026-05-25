<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Auditoria.php';

AuthController::verificarSesion();

// Solo mostrar la vista
include __DIR__ . '/../views/auditoria/index.php';
?>