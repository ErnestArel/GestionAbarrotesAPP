<?php
session_start();
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Proveedor.php';



AuthController::verificarSesion();

class ReporteController {
    private $productoModel;
    private $usuarioModel;
    private $proveedorModel;

    public function __construct() {
        $this->productoModel  = new Producto();
        $this->usuarioModel   = new Usuario();
        $this->proveedorModel = new Proveedor();
    }

    public function index() {
        $productos        = $this->productoModel->obtenerTodos();
        $totalProductos   = count($productos);
        $stockBajo        = $this->productoModel->stockBajo();
        $totalStockBajo   = count($stockBajo);
        $totalUsuarios    = count($this->usuarioModel->obtenerTodos());
        $totalProveedores = count($this->proveedorModel->obtenerTodos());

        // Variables disponibles en la vista:
        // $totalProductos, $totalStockBajo, $totalUsuarios,
        // $totalProveedores, $stockBajo
        include __DIR__ . '/../views/reportes/index.php';
    }
}

$controller = new ReporteController();
$controller->index();
