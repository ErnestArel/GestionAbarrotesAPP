<?php
// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Estamos en /controllers
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Auditoria.php';

AuthController::verificarSesion();

class RespaldoController {
    private $auditoriaModel;
    
    public function __construct() {
        $this->auditoriaModel = new Auditoria();
    }
    
    public function index() {
        include __DIR__ . '/../views/respaldo/index.php';
    }
    
    public function exportar() {
        // Configuración de la BD (NOMBRE EXACTO EN phpMyAdmin)
        $host    = 'localhost';
        $usuario = 'root';
        $clave   = '';
        $base    = 'tiendaabarrotes';  // <--- nombre de la BD

        try {
            // Conexión directa con mysqli SOLO para el backup
            $mysqli = new mysqli($host, $usuario, $clave, $base);
            if ($mysqli->connect_error) {
                throw new Exception("Error de conexión a MySQL: " . $mysqli->connect_error);
            }
            $mysqli->set_charset("utf8mb4");

            $fecha         = date('Y-m-d_H-i-s');
            $nombreArchivo = "backup_tiendaabarrotes_{$fecha}.sql";
            $rutaBackupDir = __DIR__ . '/../backups/';
            $rutaBackup    = $rutaBackupDir . $nombreArchivo;

            // Crear carpeta backups si no existe
            if (!file_exists($rutaBackupDir)) {
                if (!mkdir($rutaBackupDir, 0777, true)) {
                    throw new Exception("No se pudo crear la carpeta de respaldos: " . $rutaBackupDir);
                }
            }

            $fh = fopen($rutaBackup, 'w');
            if (!$fh) {
                throw new Exception("No se pudo crear el archivo de respaldo en: " . $rutaBackup);
            }

            // Cabecera del archivo
            $header  = "-- Respaldo de base de datos: {$base}\n";
            $header .= "-- Generado en: " . date('Y-m-d H:i:s') . "\n\n";
            $header .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            fwrite($fh, $header);

            // Obtener todas las tablas
            $tablesResult = $mysqli->query("SHOW TABLES");
            if (!$tablesResult) {
                throw new Exception("No se pudieron obtener las tablas: " . $mysqli->error);
            }

            while ($row = $tablesResult->fetch_array()) {
                $tabla = $row[0];

                // Comentarios de sección
                fwrite($fh, "-- -------------------------------------------\n");
                fwrite($fh, "-- Tabla: `{$tabla}`\n");
                fwrite($fh, "-- -------------------------------------------\n\n");

                // DROP TABLE
                fwrite($fh, "DROP TABLE IF EXISTS `{$tabla}`;\n");

                // CREATE TABLE
                $createResult = $mysqli->query("SHOW CREATE TABLE `{$tabla}`");
                if (!$createResult) {
                    throw new Exception("No se pudo obtener SHOW CREATE TABLE para {$tabla}: " . $mysqli->error);
                }
                $createRow = $createResult->fetch_assoc();
                fwrite($fh, $createRow['Create Table'] . ";\n\n");

                // Datos (INSERT)
                $dataResult = $mysqli->query("SELECT * FROM `{$tabla}`");
                if (!$dataResult) {
                    throw new Exception("No se pudieron obtener datos de {$tabla}: " . $mysqli->error);
                }

                if ($dataResult->num_rows > 0) {
                    fwrite($fh, "-- Datos de la tabla `{$tabla}`\n");

                    while ($dataRow = $dataResult->fetch_assoc()) {
                        $cols = [];
                        $vals = [];
                        foreach ($dataRow as $col => $val) {
                            $cols[] = "`" . $col . "`";
                            if ($val === null) {
                                $vals[] = "NULL";
                            } else {
                                $vals[] = "'" . $mysqli->real_escape_string($val) . "'";
                            }
                        }

                        $colsStr = implode(", ", $cols);
                        $valsStr = implode(", ", $vals);

                        $insert = "INSERT INTO `{$tabla}` ({$colsStr}) VALUES ({$valsStr});\n";
                        fwrite($fh, $insert);
                    }

                    fwrite($fh, "\n");
                }

                $dataResult->free();
                $createResult->free();
            }

            $tablesResult->free();

            // Restaurar chequeo de llaves foráneas
            fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($fh);
            $mysqli->close();

            // Registrar en auditoría
            if (isset($_SESSION['usuario_id'])) {
                $this->auditoriaModel->registrar(
                    $_SESSION['usuario_id'],
                    'BACKUP',
                    'Respaldo',
                    "Respaldo creado (PHP): $nombreArchivo"
                );
            }

            // Enviar archivo al navegador
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
            header('Content-Length: ' . filesize($rutaBackup));
            readfile($rutaBackup);
            exit();

        } catch (Exception $e) {
            $_SESSION['mensaje']      = "Error al crear respaldo (PHP): " . $e->getMessage();
            $_SESSION['tipo_mensaje'] = "error";
            header('Location: RespaldoController.php?action=index');
            exit();
        }
    }

    public function descargar() {
        $archivo = $_GET['archivo'] ?? '';
        $ruta    = __DIR__ . '/../backups/' . basename($archivo);

        if ($archivo && file_exists($ruta)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
            header('Content-Length: ' . filesize($ruta));
            readfile($ruta);
            exit();
        } else {
            $_SESSION['mensaje']      = "El archivo de respaldo no existe.";
            $_SESSION['tipo_mensaje'] = "error";
            header('Location: RespaldoController.php?action=index');
            exit();
        }
    }
}

// Enrutador simple
$controller = new RespaldoController();
$action     = $_GET['action'] ?? 'index';

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
