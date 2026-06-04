<?php

// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| CONTROLADORES Y MODELOS
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Auditoria.php';

AuthController::verificarSesion();

/*
|--------------------------------------------------------------------------
| CLASE
|--------------------------------------------------------------------------
*/

class RespaldoController {

    private $auditoriaModel;

    public function __construct() {

        $this->auditoriaModel =
            new Auditoria();
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index() {

        include __DIR__ .
            '/../views/respaldo/index.php';
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORTAR BACKUP
    |--------------------------------------------------------------------------
    */

    public function exportar() {

        /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN MYSQL
        |--------------------------------------------------------------------------
        */

        $host =
            'localhost';

        $usuario =
            'root';

        $clave =
            '';

        $base =
            'tiendaabarrotes';

        try {

            /*
            |--------------------------------------------------------------------------
            | CONEXIÓN MYSQLI
            |--------------------------------------------------------------------------
            */

            $mysqli =
                new mysqli(
                    $host,
                    $usuario,
                    $clave,
                    $base
                );

            if ($mysqli->connect_error) {

                throw new Exception(
                    "Error de conexión MySQL: "
                    . $mysqli->connect_error
                );
            }

            $mysqli->set_charset("utf8mb4");

            /*
            |--------------------------------------------------------------------------
            | CREAR NOMBRE BACKUP
            |--------------------------------------------------------------------------
            */

            $fecha =
                date('Y-m-d_H-i-s');

            $nombreArchivo =
                "backup_tiendaabarrotes_{$fecha}.sql";

            $rutaBackupDir =
                __DIR__ . '/../backups/';

            echo $rutaBackupDir;
            exit();


            $rutaBackup =
                $rutaBackupDir . $nombreArchivo;

            /*
            |--------------------------------------------------------------------------
            | CREAR CARPETA
            |--------------------------------------------------------------------------
            */

            if (!file_exists($rutaBackupDir)) {

                if (!mkdir($rutaBackupDir, 0777, true)) {

                    throw new Exception(
                        "No se pudo crear la carpeta backups"
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CREAR ARCHIVO
            |--------------------------------------------------------------------------
            */

            $fh =
                fopen($rutaBackup, 'w');

            if (!$fh) {

                throw new Exception(
                    "No se pudo crear el archivo backup"
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CABECERA SQL
            |--------------------------------------------------------------------------
            */

            $header  =
                "-- Backup generado automáticamente\n";

            $header .=
                "-- Base de datos: {$base}\n";

            $header .=
                "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";

            $header .=
                "SET FOREIGN_KEY_CHECKS=0;\n\n";

            fwrite($fh, $header);

            /*
            |--------------------------------------------------------------------------
            | OBTENER TABLAS
            |--------------------------------------------------------------------------
            */

            $tablesResult =
                $mysqli->query("SHOW TABLES");

            if (!$tablesResult) {

                throw new Exception(
                    "No se pudieron obtener las tablas"
                );
            }

            while ($row = $tablesResult->fetch_array()) {

                $tabla =
                    $row[0];

                /*
                |--------------------------------------------------------------------------
                | COMENTARIOS
                |--------------------------------------------------------------------------
                */

                fwrite(
                    $fh,
                    "-- --------------------------------------\n"
                );

                fwrite(
                    $fh,
                    "-- Tabla: {$tabla}\n"
                );

                fwrite(
                    $fh,
                    "-- --------------------------------------\n\n"
                );

                /*
                |--------------------------------------------------------------------------
                | DROP TABLE
                |--------------------------------------------------------------------------
                */

                fwrite(
                    $fh,
                    "DROP TABLE IF EXISTS `{$tabla}`;\n"
                );

                /*
                |--------------------------------------------------------------------------
                | CREATE TABLE
                |--------------------------------------------------------------------------
                */

                $createResult =
                    $mysqli->query(
                        "SHOW CREATE TABLE `{$tabla}`"
                    );

                if (!$createResult) {

                    throw new Exception(
                        "Error SHOW CREATE TABLE {$tabla}"
                    );
                }

                $createRow =
                    $createResult->fetch_assoc();

                fwrite(
                    $fh,
                    $createRow['Create Table'] . ";\n\n"
                );

                /*
                |--------------------------------------------------------------------------
                | DATOS
                |--------------------------------------------------------------------------
                */

                $dataResult =
                    $mysqli->query(
                        "SELECT * FROM `{$tabla}`"
                    );

                if (!$dataResult) {

                    throw new Exception(
                        "Error SELECT tabla {$tabla}"
                    );
                }

                if ($dataResult->num_rows > 0) {

                    fwrite(
                        $fh,
                        "-- Datos de {$tabla}\n"
                    );

                    while ($dataRow = $dataResult->fetch_assoc()) {

                        $cols = [];
                        $vals = [];

                        foreach ($dataRow as $col => $val) {

                            $cols[] =
                                "`{$col}`";

                            if ($val === null) {

                                $vals[] =
                                    "NULL";

                            } else {

                                $vals[] =
                                    "'" .
                                    $mysqli->real_escape_string($val)
                                    . "'";
                            }
                        }

                        $colsStr =
                            implode(", ", $cols);

                        $valsStr =
                            implode(", ", $vals);

                        $insert =
                            "INSERT INTO `{$tabla}` ({$colsStr}) VALUES ({$valsStr});\n";

                        fwrite($fh, $insert);
                    }

                    fwrite($fh, "\n");
                }

                $dataResult->free();

                $createResult->free();
            }

            $tablesResult->free();

            /*
            |--------------------------------------------------------------------------
            | REACTIVAR FOREIGN KEYS
            |--------------------------------------------------------------------------
            */

            fwrite(
                $fh,
                "SET FOREIGN_KEY_CHECKS=1;\n"
            );

            fclose($fh);

            $mysqli->close();

            /*
            |--------------------------------------------------------------------------
            | AUDITORÍA
            |--------------------------------------------------------------------------
            */

            if (isset($_SESSION['usuario_id'])) {

                $this->auditoriaModel->registrar(

                    $_SESSION['usuario_id'],

                    'BACKUP',

                    'Respaldo',

                    "Backup generado: {$nombreArchivo}"
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DESCARGAR ARCHIVO
            |--------------------------------------------------------------------------
            */

            header('Content-Type: application/octet-stream');

            header(
                'Content-Disposition: attachment; filename="' .
                $nombreArchivo .
                '"'
            );

            header(
                'Content-Length: ' .
                filesize($rutaBackup)
            );

            readfile($rutaBackup);

            exit();

        } catch (Exception $e) {

            $_SESSION['mensaje'] =
                "Error al crear respaldo: "
                . $e->getMessage();

            $_SESSION['tipo_mensaje'] =
                "error";

            header(
                'Location: /tiendaAbarrotes/controllers/RespaldoController.php?action=index'
            );

            exit();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESCARGAR
    |--------------------------------------------------------------------------
    */

    public function descargar() {

        $archivo =
            $_GET['archivo'] ?? '';

        $ruta =
            __DIR__ .
            '/../backups/' .
            basename($archivo);

        if ($archivo && file_exists($ruta)) {

            header(
                'Content-Type: application/octet-stream'
            );

            header(
                'Content-Disposition: attachment; filename="' .
                basename($ruta) .
                '"'
            );

            header(
                'Content-Length: ' .
                filesize($ruta)
            );

            readfile($ruta);

            exit();

        } else {

            $_SESSION['mensaje'] =
                "El backup no existe";

            $_SESSION['tipo_mensaje'] =
                "error";

            header(
                'Location: /tiendaAbarrotes/controllers/RespaldoController.php?action=index'
            );

            exit();
        }
    }
}

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

$controller =
    new RespaldoController();

$action =
    $_GET['action'] ?? 'index';

switch ($action) {

    case 'index':

        $controller->index();

        break;

    case 'exportar':

        $controller->exportar();

        break;

    case 'descargar':

        $controller->descargar();

        break;

    default:

        $controller->index();
}

?>
