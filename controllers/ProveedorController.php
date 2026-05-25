<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Proveedor.php';
require_once __DIR__ . '/../models/Auditoria.php';

AuthController::verificarSesion();

class ProveedorController {
    private $proveedorModel;
    private $auditoriaModel;
    
    public function __construct() {
        $this->proveedorModel = new Proveedor();
        $this->auditoriaModel = new Auditoria();
    }
    
    public function listar() {
        $proveedores = $this->proveedorModel->obtenerTodos();
        include __DIR__ . '/../views/proveedores/listar.php';
    }
    
    public function agregar() {
        include __DIR__ . '/../views/proveedores/agregar.php';
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $datos = [
                    'ruc' => $_POST['ruc'],
                    'razon_social' => $_POST['razon_social'],
                    'contacto' => $_POST['contacto'],
                    'telefono' => $_POST['telefono'],
                    'email' => $_POST['email'],
                    'direccion' => $_POST['direccion']
                ];
                
                $this->proveedorModel->crear($datos);
                
                // Auditoría
                $this->auditoriaModel->registrar(
                    $_SESSION['usuario_id'],
                    'CREAR',
                    'Proveedores',
                    "Proveedor creado: {$datos['razon_social']} (RUC: {$datos['ruc']})"
                );
                
                $_SESSION['mensaje'] = "Proveedor registrado exitosamente";
                $_SESSION['tipo_mensaje'] = "success";
            } catch (Exception $e) {
                $_SESSION['mensaje'] = "Error: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = "error";
            }
            $this->redirigir('../controllers/ProveedorController.php?action=listar');
        }
    }
    
    public function buscarPorRuc() {
        if (isset($_GET['ruc'])) {
            $ruc = $_GET['ruc'];
            $proveedor = $this->proveedorModel->buscarPorRuc($ruc);
            
            if ($proveedor) {
                include __DIR__ . '/../views/proveedores/detalle.php';
            } else {
                $_SESSION['mensaje'] = "Proveedor no encontrado con RUC: $ruc";
                $_SESSION['tipo_mensaje'] = "error";
                $this->redirigir('../controllers/ProveedorController.php?action=listar');
            }
        }
    }
    
    public function editar() {
        $id = $_GET['id'] ?? 0;
        $proveedor = $this->proveedorModel->obtenerPorId($id);
        
        if (!$proveedor) {
            $_SESSION['mensaje'] = "Proveedor no encontrado";
            $_SESSION['tipo_mensaje'] = "error";
            $this->redirigir('../controllers/ProveedorController.php?action=listar');
            return;
        }
        include __DIR__ . '/../views/proveedores/editar.php';
    }
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $datos = [
                    'ruc' => $_POST['ruc'],
                    'razon_social' => $_POST['razon_social'],
                    'contacto' => $_POST['contacto'],
                    'telefono' => $_POST['telefono'],
                    'email' => $_POST['email'],
                    'direccion' => $_POST['direccion']
                ];
                
                $this->proveedorModel->actualizar($id, $datos);
                
                // Auditoría
                $this->auditoriaModel->registrar(
                    $_SESSION['usuario_id'],
                    'ACTUALIZAR',
                    'Proveedores',
                    "Proveedor actualizado: {$datos['razon_social']} (ID: $id)"
                );
                
                $_SESSION['mensaje'] = "Proveedor actualizado exitosamente";
                $_SESSION['tipo_mensaje'] = "success";
            } catch (Exception $e) {
                $_SESSION['mensaje'] = "Error: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = "error";
            }
            $this->redirigir('../controllers/ProveedorController.php?action=listar');
        }
    }
    
    public function eliminar() {
        $id = $_GET['id'] ?? 0;
        
        try {
            // Verificar si tiene productos asociados
            $totalProductos = $this->proveedorModel->contarProductos($id);
            
            if ($totalProductos > 0) {
                $_SESSION['mensaje'] = "No se puede eliminar. Tiene $totalProductos productos asociados";
                $_SESSION['tipo_mensaje'] = "error";
            } else {
                $proveedor = $this->proveedorModel->obtenerPorId($id);
                $this->proveedorModel->eliminar($id);
                
                // Auditoría
                $this->auditoriaModel->registrar(
                    $_SESSION['usuario_id'],
                    'ELIMINAR',
                    'Proveedores',
                    "Proveedor eliminado: {$proveedor['razon_social']} (ID: $id)"
                );
                
                $_SESSION['mensaje'] = "Proveedor eliminado exitosamente";
                $_SESSION['tipo_mensaje'] = "success";
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error: " . $e->getMessage();
            $_SESSION['tipo_mensaje'] = "error";
        }
        $this->redirigir('../controllers/ProveedorController.php?action=listar');
    }
    
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $proveedores = $this->proveedorModel->buscar($termino);
        include __DIR__ . '/../views/proveedores/listar.php';
    }
    
    private function redirigir($url) {
        header("Location: $url");
        exit();
    }
}

$controller = new ProveedorController();
$action = $_GET['action'] ?? 'listar';

switch ($action) {
    case 'listar': $controller->listar(); break;
    case 'agregar': $controller->agregar(); break;
    case 'crear': $controller->crear(); break;
    case 'buscarPorRuc': $controller->buscarPorRuc(); break;
    case 'editar': $controller->editar(); break;
    case 'actualizar': $controller->actualizar(); break;
    case 'eliminar': $controller->eliminar(); break;
    case 'buscar': $controller->buscar(); break;
    default: $controller->listar();
}
?>