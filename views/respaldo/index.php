<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/AuthController.php';
AuthController::verificarSesion();

$mensaje      = $_SESSION['mensaje']      ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Obtener lista de backups existentes
$backupDir = __DIR__ . '/../../backups/';
$backups   = [];
if (is_dir($backupDir)) {
    $files = scandir($backupDir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
            $backups[] = [
                'nombre' => $file,
                'ruta'   => $backupDir . $file,
                'tamano' => filesize($backupDir . $file),
                'fecha'  => filemtime($backupDir . $file)
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respaldo de Datos</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', sans-serif; background: #f5f7fa;}
        .navbar {background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;}
        .navbar-brand {display: flex; align-items: center; gap: 15px; font-size: 24px; font-weight: bold;}
        .btn-back {background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 20px; border-radius: 20px; text-decoration: none;}
        .container {max-width: 1000px; margin: 30px auto; padding: 0 20px;}
        .backup-card {background: white; padding: 50px; border-radius: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;}
        .backup-icon {font-size: 120px; margin-bottom: 20px;}
        h1 {color: #333; margin-bottom: 10px; font-size: 32px;}
        .subtitle {color: #666; margin-bottom: 30px; font-size: 16px;}
        .btn-backup {background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px 50px; border: none; border-radius: 12px; font-size: 20px; font-weight: bold; cursor: pointer; transition: all 0.3s; display: inline-block; text-decoration: none;}
        .btn-backup:hover {transform: translateY(-3px); box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);}
        .info-box {background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 30px; text-align: left;}
        .info-box h3 {color: #333; margin-bottom: 10px; font-size: 18px;}
        .info-box ul {margin-left: 20px; color: #666;}
        .info-box ul li {margin: 8px 0;}
        .alert {padding: 15px 20px; border-radius: 10px; margin-bottom: 20px;}
        .alert-success {background: #d4edda; color: #155724; border-left: 4px solid #28a745;}
        .alert-error {background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545;}
        .backups-list {background: white; padding: 30px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);}
        .backups-list h2 {color: #333; margin-bottom: 20px;}
        .backup-item {background: #f8f9fa; padding: 15px 20px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;}
        .backup-info {flex: 1;}
        .backup-name {font-weight: bold; color: #333; margin-bottom: 5px;}
        .backup-details {font-size: 13px; color: #999;}
        .backup-actions {display: flex; gap: 10px;}
        .btn-download {background: #17a2b8; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;}
        .empty-backups {text-align: center; padding: 40px; color: #999;}
        .empty-backups-icon {font-size: 64px; margin-bottom: 15px;}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span>💾</span>
            <span>Respaldo de Datos</span>
        </div>
        <!-- carpeta del proyecto en htdocs -->
        <a href="/tiendaAbarrotes/views/dashboard.php" class="btn-back">← Volver al Dashboard</a>
    </nav>
    
    <div class="container">
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensaje); ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <div class="backup-card">
            <div class="backup-icon">💾</div>
            <h1>Respaldo de Base de Datos</h1>
            <p class="subtitle">Exporta una copia completa de tu base de datos</p>
            
            <!-- SIN JS raro: el form solo se envía al controlador -->
            <form action="/tiendaAbarrotes/controllers/RespaldoController.php?action=exportar" method="POST">
                <button type="submit" class="btn-backup"
                        onclick="return confirm('¿Desea generar un respaldo de la base de datos?\n\nEl archivo SQL se descargará automáticamente.');">
                    🔽 Descargar Respaldo Ahora
                </button>
            </form>
            
            <div class="info-box">
                <h3>ℹ️ Información del Respaldo</h3>
                <ul>
                    <li><strong>Formato:</strong> Archivo SQL (.sql)</li>
                    <li><strong>Incluye:</strong> Usuarios, Productos, Proveedores, Auditoría</li>
                    <li><strong>Descarga:</strong> El archivo se descargará automáticamente</li>
                    <li><strong>Nombre:</strong> backup_tiendaabarrotes_[fecha-hora].sql</li>
                    <li><strong>Uso:</strong> Importar en phpMyAdmin para restaurar datos</li>
                </ul>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                <strong>⚠️ Importante:</strong> Se recomienda realizar respaldos periódicamente (semanalmente o después de cambios importantes)
            </div>
        </div>
        
        <!-- Lista de backups existentes -->
        <?php if (count($backups) > 0): ?>
            <div class="backups-list">
                <h2>📂 Respaldos Anteriores (<?php echo count($backups); ?>)</h2>
                <?php foreach ($backups as $backup): ?>
                    <div class="backup-item">
                        <div class="backup-info">
                            <div class="backup-name">📄 <?php echo htmlspecialchars($backup['nombre']); ?></div>
                            <div class="backup-details">
                                Tamaño: <?php echo number_format($backup['tamano'] / 1024, 2); ?> KB | 
                                Creado: <?php echo date('d/m/Y H:i:s', $backup['fecha']); ?>
                            </div>
                        </div>
                        <div class="backup-actions">
                            <a href="/tiendaAbarrotes/controllers/RespaldoController.php?action=descargar&archivo=<?php echo urlencode($backup['nombre']); ?>" class="btn-download">
                                ⬇️ Descargar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="backups-list">
                <div class="empty-backups">
                    <div class="empty-backups-icon">📭</div>
                    <h3>No hay respaldos anteriores</h3>
                    <p>Crea tu primer respaldo usando el botón de arriba</p>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="background: white; padding: 20px; border-radius: 10px; margin-top: 20px; text-align: center; color: #666; font-size: 13px;">
            💡 <strong>Tip:</strong> Los respaldos se guardan en la carpeta <code>backups/</code> del sistema. Guárdalos en un lugar seguro fuera del servidor.
        </div>
    </div>
</body>
</html>
