<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Auditoria.php';
require_once __DIR__ . '/../../models/Usuario.php';

AuthController::verificarSesion();

$auditoriaModel = new Auditoria();
$usuarioModel   = new Usuario();

// Filtros
$usuario_id   = $_GET['usuario_id']   ?? null;
$modulo       = $_GET['modulo']       ?? null;
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin    = $_GET['fecha_fin']    ?? null;

// Obtener registros
if ($usuario_id || $modulo || $fecha_inicio || $fecha_fin) {
    $registros = $auditoriaModel->filtrar($usuario_id, $modulo, $fecha_inicio, $fecha_fin);
} else {
    $registros = $auditoriaModel->obtenerTodos(200);
}

// Obtener lista de usuarios para filtro
$usuarios = $usuarioModel->obtenerTodos();

// Módulos disponibles
$modulos = ['Autenticación', 'Usuarios', 'Productos', 'Proveedores', 'Respaldo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría del Sistema</title>

    <!-- Ruta absoluta al CSS del proyecto -->
    <link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">

    <style>
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .filter-group label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-filter {
            background: #667eea;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 22px;
        }
        .btn-clear {
            background: #6c757d;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 22px;
            text-decoration: none;
            display: inline-block;
        }
        .badge-accion {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-login {background: #d1ecf1; color: #0c5460;}
        .badge-crear {background: #d4edda; color: #155724;}
        .badge-actualizar {background: #fff3cd; color: #856404;}
        .badge-eliminar {background: #f8d7da; color: #721c24;}
        .badge-logout {background: #e2e3e5; color: #383d41;}
        .badge-backup {background: #cfe2ff; color: #084298;}
        .stats-mini {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-mini {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            flex: 1;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-mini-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-mini-label {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        @media (max-width: 1200px) {
            .filters-grid {grid-template-columns: repeat(2, 1fr);}
        }
        @media (max-width: 768px) {
            .filters-grid {grid-template-columns: 1fr;}
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span>🔍</span>
            <span>Auditoría del Sistema</span>
        </div>
        <!-- Ruta absoluta al dashboard -->
        <a href="/tiendaAbarrotes/views/dashboard.php" class="btn-back">← Volver al Dashboard</a>
    </nav>
    
    <div class="container">
        <div class="header-section">
            <h1>Registro de Auditoría</h1>
            <div style="color: #666; font-size: 14px;">
                Última actualización: <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>
        
        <!-- Estadísticas rápidas -->
        <div class="stats-mini">
            <div class="stat-mini">
                <div class="stat-mini-number"><?php echo count($registros); ?></div>
                <div class="stat-mini-label">Registros Mostrados</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-number">
                    <?php 
                    $hoy = date('Y-m-d');
                    $registrosHoy = array_filter($registros, function($r) use ($hoy) {
                        return date('Y-m-d', strtotime($r['fecha'])) == $hoy;
                    });
                    echo count($registrosHoy);
                    ?>
                </div>
                <div class="stat-mini-label">Acciones Hoy</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-number">
                    <?php 
                    $usuarios_activos = array_unique(array_column($registros, 'usuario_id'));
                    echo count(array_filter($usuarios_activos));
                    ?>
                </div>
                <div class="stat-mini-label">Usuarios Activos</div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="filters-section">
            <h3 style="margin-bottom: 15px; color: #333;">🔎 Filtros de Búsqueda</h3>
            <form action="" method="GET">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Usuario</label>
                        <select name="usuario_id">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo $usuario_id == $u['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['nombre_completo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Módulo</label>
                        <select name="modulo">
                            <option value="">Todos los módulos</option>
                            <?php foreach ($modulos as $m): ?>
                                <option value="<?php echo $m; ?>" <?php echo $modulo == $m ? 'selected' : ''; ?>>
                                    <?php echo $m; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio ?? ''); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin ?? ''); ?>">
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn-filter">🔍 Buscar</button>
                    <a href="?" class="btn-clear">🔄 Limpiar Filtros</a>
                </div>
            </form>
        </div>
        
        <!-- Tabla de registros -->
        <div class="table-container">
            <?php if (count($registros) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Descripción</th>
                            <th>IP</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r['id']; ?></strong></td>
                                <td>
                                    <?php if ($r['usuario_id']): ?>
                                        <strong><?php echo htmlspecialchars($r['nombre_completo']); ?></strong><br>
                                        <small style="color: #999;"><?php echo htmlspecialchars($r['usuario']); ?></small>
                                    <?php else: ?>
                                        <span style="color: #999;">Sistema</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $accion = strtoupper($r['accion']);
                                    $clase  = 'badge-accion ';
                                    if (strpos($accion, 'LOGIN') !== false)      $clase .= 'badge-login';
                                    elseif (strpos($accion, 'CREAR') !== false)      $clase .= 'badge-crear';
                                    elseif (strpos($accion, 'ACTUALIZAR') !== false) $clase .= 'badge-actualizar';
                                    elseif (strpos($accion, 'ELIMINAR') !== false)   $clase .= 'badge-eliminar';
                                    elseif (strpos($accion, 'LOGOUT') !== false)     $clase .= 'badge-logout';
                                    elseif (strpos($accion, 'BACKUP') !== false)     $clase .= 'badge-backup';
                                    else                                              $clase .= 'badge-success';
                                    ?>
                                    <span class="<?php echo $clase; ?>"><?php echo $accion; ?></span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($r['modulo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['descripcion']); ?></td>
                                <td><code><?php echo htmlspecialchars($r['ip']); ?></code></td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($r['fecha'])); ?><br>
                                    <small style="color: #999;"><?php echo date('H:i:s', strtotime($r['fecha'])); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #999;">
                    <div style="font-size: 64px; margin-bottom: 20px;">📭</div>
                    <h3>No se encontraron registros</h3>
                    <p>No hay actividad registrada con los filtros seleccionados</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px; margin-top: 20px; text-align: center; color: #666; font-size: 13px;">
            💡 <strong>Tip:</strong> Los registros se mantienen indefinidamente para cumplir con las normas de auditoría y seguridad.
        </div>
    </div>
</body>
</html>
