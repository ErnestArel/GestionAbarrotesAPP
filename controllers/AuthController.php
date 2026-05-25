<?php
// Iniciar sesión solo si no hay una activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Auditoria.php';

class AuthController {
    private $usuarioModel;
    private $auditoriaModel;
    private $maxIntentos = 3;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->auditoriaModel = new Auditoria();
        
        if (!isset($_SESSION['intentos_login'])) {
            $_SESSION['intentos_login'] = 0;
        }
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SESSION['intentos_login'] >= $this->maxIntentos) {
                $this->cerrarNavegador();
                exit();
            }
            
            $usuario = $_POST['usuario'] ?? '';
            $clave   = $_POST['clave'] ?? '';
            
            if (empty($usuario) || empty($clave)) {
                $_SESSION['intentos_login']++;
                $_SESSION['error_login'] = "Complete todos los campos";
                $this->redirigir('../index.php');
                return;
            }
            
            $usuarioData = $this->usuarioModel->autenticar($usuario, $clave);
            
            if ($usuarioData) {
                // Guardar datos en sesión
                $_SESSION['usuario_id']      = $usuarioData['id'];
                $_SESSION['usuario']         = $usuarioData['usuario'];
                $_SESSION['nombre_completo'] = $usuarioData['nombre_completo'];
                $_SESSION['rol']             = $usuarioData['rol'];
                $_SESSION['autenticado']     = true;
                $_SESSION['intentos_login']  = 0;
                
                // Registrar en auditoría (LOGIN)
                try {
                    $this->auditoriaModel->registrar(
                        $usuarioData['id'],
                        'LOGIN',
                        'Autenticación',
                        "Inicio de sesión exitoso: {$usuarioData['usuario']}"
                    );
                } catch (Exception $e) {
                    // Si falla la auditoría, no rompemos el login
                    // error_log($e->getMessage());
                }
                
                $this->redirigir('../views/dashboard.php');
            } else {
                $_SESSION['intentos_login']++;
                $intentosRestantes = $this->maxIntentos - $_SESSION['intentos_login'];
                
                // Registrar intento fallido (usuario_id NULL es válido para la FK)
                try {
                    $this->auditoriaModel->registrar(
                        null,
                        'LOGIN_FALLIDO',
                        'Autenticación',
                        "Intento de inicio de sesión fallido para usuario: $usuario"
                    );
                } catch (Exception $e) {
                    // No hacemos nada, solo evitamos que truene
                    // error_log($e->getMessage());
                }
                
                if ($intentosRestantes > 0) {
                    $_SESSION['error_login'] = "Credenciales incorrectas. Intentos restantes: " . $intentosRestantes;
                    $this->redirigir('../index.php');
                } else {
                    $this->cerrarNavegador();
                }
            }
        }
    }
    
    public function logout() {
        // Registrar cierre de sesión
        if (isset($_SESSION['usuario_id'])) {
            try {
                $usuarioId   = $_SESSION['usuario_id'];
                $usuarioName = $_SESSION['usuario'] ?? '';

                // Si el id es raro, lo mandamos como NULL para no romper la FK
                if (!is_numeric($usuarioId) || $usuarioId <= 0) {
                    $usuarioId = null;
                }

                $this->auditoriaModel->registrar(
                    $usuarioId,
                    'LOGOUT',
                    'Autenticación',
                    "Cierre de sesión: {$usuarioName}"
                );
            } catch (Exception $e) {
                // Si falla la auditoría, de todos modos cerramos sesión
                // error_log($e->getMessage());
            }
        }
        
        // Destruir sesión
        session_unset();
        session_destroy();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        $this->redirigir('../index.php');
    }
    
    public static function verificarSesion() {
        if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
            header('Location: ../index.php');
            exit();
        }
    }
    
    private function cerrarNavegador() {
        echo '<!DOCTYPE html><html><head><title>Acceso Bloqueado</title><style>
        body{font-family:Arial;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;justify-content:center;align-items:center;height:100vh;margin:0}
        .mensaje{background:white;padding:40px;border-radius:10px;text-align:center;box-shadow:0 10px 25px rgba(0,0,0,0.3)}
        h1{color:#e74c3c}p{color:#555;font-size:18px}
        </style></head><body><div class="mensaje"><h1>⚠️ Acceso Bloqueado</h1>
        <p>Ha excedido el número máximo de intentos.</p>
        <p>Esta ventana se cerrará automáticamente.</p></div>
        <script>setTimeout(function(){window.open("","_self");window.close();},3000);</script></body></html>';
        session_destroy();
        exit();
    }
    
    private function redirigir($url) {
        header("Location: $url");
        exit();
    }
}

// Enrutador simple
if (isset($_GET['action'])) {
    $controller = new AuthController();
    switch ($_GET['action']) {
        case 'login':
            $controller->login();
            break;
        case 'logout':
            $controller->logout();
            break;
    }
}
?>
