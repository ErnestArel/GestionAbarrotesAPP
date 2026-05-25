<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Auditoria.php';

AuthController::verificarSesion();

class ProductoController {
    private $productoModel;
    private $auditoriaModel;
    
    public function __construct() {
        $this->productoModel = new Producto();
        $this->auditoriaModel = new Auditoria();
    }
    
    public function listar() {
        $productos = $this->productoModel->obtenerTodos();
        include __DIR__ . '/../views/productos/listar.php';
    }
    
    public function agregar() {
        include __DIR__ . '/../views/productos/agregar.php';
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $datos = [
                    'codigo' => $_POST['codigo'],
                    'nombre' => $_POST['nombre'],
                    'categoria' => $_POST['categoria'],
                    'precio_compra' => $_POST['precio_compra'],
                    'precio_venta' => $_POST['precio_venta'],
                    'stock' => $_POST['stock'],
                    'stock_minimo' => $_POST['stock_minimo'],
                    'proveedor_id' => $_POST['proveedor_id'],
                    'fecha_vencimiento' => $_POST['fecha_vencimiento'] ?? ''
                ];
                
                $this->productoModel->crear($datos);
                
                // Auditoría
                $this->auditoriaModel->registrar(
                    $_SESSION['usuario_id'],
                    'CREAR',
                    'Productos',
                    "Producto creado: {$datos['nombre']} (Código: {$datos['codigo']})"
                );
                
                $_SESSION['mensaje'] = "Producto agregado exitosamente";
                $_SESSION['tipo_mensaje'] = "success";
            } catch (Exception $e) {
                $_SESSION['mensaje'] = "Error: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = "error";
            }
            $this->redirigir('../controllers/ProductoController.php?action=listar');
        }
    }
    
    public function buscarPorCodigo() {
        $codigo = $_GET['codigo'] ?? '';
        $producto = $this->productoModel->buscarPorCodigo($codigo);
        
        if ($producto) {
            // Mostrar detalle del producto
            include __DIR__ . '/../views/productos/detalle.php';
        } else {
            $_SESSION['mensaje'] = "Producto no encontrado con código: $codigo";
            $_SESSION['tipo_mensaje'] = "error";
            $this->redirigir('../controllers/ProductoController.php?action=listar');
        }
    }
    
    public function editar() {
        $id = $_GET['id'] ?? 0;
        $producto = $this->productoModel->obtenerPorId($id);
        
        if (!$producto) {
            $_SESSION['mensaje'] = "Producto no encontrado";
            $_SESSION['tipo_mensaje'] = "error";
            $this->redirigir('../controllers/ProductoController.php?action=listar');
            return;
        }
        include __DIR__ . '/../views/productos/editar.php';
    }
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $datos = [
                    'codigo' => $_POST['codigo'],
                    'nombre' => $_POST['nombre'],
                    'categoria' => $_POST['categoria'],
                    'precio_compra' => $_POST['precio_compra'],
                    'precio_venta' => $_POST['precio_venta'],
                    'stock' => $_POST['stock'],
                    'stock_minimo' => $_POST['stock_minimo'],
                    'proveedor_id' => $_POST['proveedor_id'],
                    'fecha_vencimiento' => $_POST['fecha_vencimiento'] ?? ''
                ];
                
                $this->productoModel->actualizar($id, $datos);
                
                // Auditoría
                $this->auditoriaModel->registrar(
                    $_SESSION['usuario_id'],
                    'ACTUALIZAR',
                    'Productos',
                    "Producto actualizado: {$datos['nombre']} (ID: $id)"
                );
                
                $_SESSION['mensaje'] = "Producto actualizado exitosamente";
                $_SESSION['tipo_mensaje'] = "success";
            } catch (Exception $e) {
                $_SESSION['mensaje'] = "Error: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = "error";
            }
            $this->redirigir('../controllers/ProductoController.php?action=listar');
        }
    }
    
    public function eliminar() {
        $id = $_GET['id'] ?? 0;
        try {
            $producto = $this->productoModel->obtenerPorId($id);
            $this->productoModel->eliminar($id);
            
            // Auditoría
            $this->auditoriaModel->registrar(
                $_SESSION['usuario_id'],
                'ELIMINAR',
                'Productos',
                "Producto eliminado: {$producto['nombre']} (ID: $id)"
            );
            
            $_SESSION['mensaje'] = "Producto eliminado exitosamente";
            $_SESSION['tipo_mensaje'] = "success";
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error: " . $e->getMessage();
            $_SESSION['tipo_mensaje'] = "error";
        }
        $this->redirigir('../controllers/ProductoController.php?action=listar');
    }
    
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $productos = $this->productoModel->buscar($termino);
        include __DIR__ . '/../views/productos/listar.php';
    }
    
    private function redirigir($url) {
        header("Location: $url");
        exit();
    }
}

$controller = new ProductoController();
$action = $_GET['action'] ?? 'listar';

switch ($action) {
    case 'listar': $controller->listar(); break;
    case 'agregar': $controller->agregar(); break;
    case 'crear': $controller->crear(); break;
    case 'buscarPorCodigo': $controller->buscarPorCodigo(); break;
    case 'editar': $controller->editar(); break;
    case 'actualizar': $controller->actualizar(); break;
    case 'eliminar': $controller->eliminar(); break;
    case 'buscar': $controller->buscar(); break;
    default: $controller->listar();
}
?>