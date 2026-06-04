<?php

session_start();

/*
|--------------------------------------------------------------------------
| CONTROLADORES Y MODELOS
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Usuario.php';

AuthController::verificarSesion();

/*
|--------------------------------------------------------------------------
| CLASE
|--------------------------------------------------------------------------
*/

class UsuarioController {

    private $usuarioModel;

    public function __construct() {

        $this->usuarioModel =
            new Usuario();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR
    |--------------------------------------------------------------------------
    */

    public function listar() {

        $usuarios =
            $this->usuarioModel->obtenerTodos();

        /*
        |--------------------------------------------------------------------------
        | IMPORTANTE:
        | TU ARCHIVO ES "Listar.php"
        |--------------------------------------------------------------------------
        */

        include __DIR__ .
            '/../views/usuarios/listar.php';
    }

    /*
    |--------------------------------------------------------------------------
    | AGREGAR
    |--------------------------------------------------------------------------
    */

    public function agregar() {

        include __DIR__ .
            '/../views/usuarios/agregar.php';
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    public function crear() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {

                $datos = [

                    'usuario' =>
                        $_POST['usuario'],

                    'clave' =>
                        $_POST['clave'],

                    'nombre_completo' =>
                        $_POST['nombre_completo'],

                    'email' =>
                        $_POST['email'],

                    'rol' =>
                        $_POST['rol']
                ];

                $this->usuarioModel->crear($datos);

                $_SESSION['mensaje'] =
                    "Usuario creado exitosamente";

                $_SESSION['tipo_mensaje'] =
                    "success";

            } catch (Exception $e) {

                $_SESSION['mensaje'] =
                    "Error: " . $e->getMessage();

                $_SESSION['tipo_mensaje'] =
                    "error";
            }

            $this->redirigir(
                '/tiendaAbarrotes/controllers/UsuarioController.php?action=listar'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    public function editar() {

        $id =
            $_GET['id'] ?? 0;

        $usuario =
            $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {

            $_SESSION['mensaje'] =
                "Usuario no encontrado";

            $_SESSION['tipo_mensaje'] =
                "error";

            $this->redirigir(
                '/tiendaAbarrotes/controllers/UsuarioController.php?action=listar'
            );

            return;
        }

        include __DIR__ .
            '/../views/usuarios/editar.php';
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function actualizar() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {

                $id =
                    $_POST['id'];

                $datos = [

                    'usuario' =>
                        $_POST['usuario'],

                    'nombre_completo' =>
                        $_POST['nombre_completo'],

                    'email' =>
                        $_POST['email'],

                    'rol' =>
                        $_POST['rol'],

                    'clave' =>
                        $_POST['clave'] ?? ''
                ];

                $this->usuarioModel->actualizar(
                    $id,
                    $datos
                );

                $_SESSION['mensaje'] =
                    "Usuario actualizado exitosamente";

                $_SESSION['tipo_mensaje'] =
                    "success";

            } catch (Exception $e) {

                $_SESSION['mensaje'] =
                    "Error: " . $e->getMessage();

                $_SESSION['tipo_mensaje'] =
                    "error";
            }

            $this->redirigir(
                '/tiendaAbarrotes/controllers/UsuarioController.php?action=listar'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function eliminar() {

        $id =
            $_GET['id'] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | EVITAR AUTOELIMINACIÓN
        |--------------------------------------------------------------------------
        */

        if ($id == $_SESSION['usuario_id']) {

            $_SESSION['mensaje'] =
                "No puedes eliminar tu propio usuario";

            $_SESSION['tipo_mensaje'] =
                "error";

            $this->redirigir(
                '/tiendaAbarrotes/controllers/UsuarioController.php?action=listar'
            );

            return;
        }

        try {

            $this->usuarioModel->eliminar($id);

            $_SESSION['mensaje'] =
                "Usuario eliminado exitosamente";

            $_SESSION['tipo_mensaje'] =
                "success";

        } catch (Exception $e) {

            $_SESSION['mensaje'] =
                "Error: " . $e->getMessage();

            $_SESSION['tipo_mensaje'] =
                "error";
        }

        $this->redirigir(
            '/tiendaAbarrotes/controllers/UsuarioController.php?action=listar'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REDIRECCIONAR
    |--------------------------------------------------------------------------
    */

    private function redirigir($url) {

        header("Location: $url");

        exit();
    }
}

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

$controller =
    new UsuarioController();

$action =
    $_GET['action'] ?? 'listar';

switch ($action) {

    case 'listar':

        $controller->listar();

        break;

    case 'agregar':

        $controller->agregar();

        break;

    case 'crear':

        $controller->crear();

        break;

    case 'editar':

        $controller->editar();

        break;

    case 'actualizar':

        $controller->actualizar();

        break;

    case 'eliminar':

        $controller->eliminar();

        break;

    default:

        $controller->listar();
}

?>