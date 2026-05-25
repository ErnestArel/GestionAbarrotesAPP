<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Proveedor</title>
    <!-- RUTA ABSOLUTA AL CSS -->
    <link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">
    <style>
        .detalle-card {background: white; padding: 40px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; margin: 30px auto;}
        .detalle-header {text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0;}
        .detalle-header-icon {font-size: 80px; margin-bottom: 15px;}
        .detalle-grid {display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;}
        .detalle-item {padding: 15px; background: #f8f9fa; border-radius: 8px;}
        .detalle-item label {display: block; color: #999; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;}
        .detalle-item .value {color: #333; font-size: 16px; font-weight: 600;}
        .detalle-full {grid-column: 1 / -1;}
        .btn-group {display: flex; gap: 10px; justify-content: center; margin-top: 30px;}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand"><span>🏢</span><span>Detalle del Proveedor</span></div>
        <!-- SIEMPRE REGRESAR AL CONTROLADOR -->
        <a href="/tiendaAbarrotes/controllers/ProveedorController.php?action=listar" class="btn-back">← Volver al Listado</a>
    </nav>
    
    <div class="container">
        <div class="detalle-card">
            <div class="detalle-header">
                <div class="detalle-header-icon">🏢</div>
                <h1><?php echo htmlspecialchars($proveedor['razon_social']); ?></h1>
                <p style="color: #666;">Proveedor #<?php echo $proveedor['id']; ?></p>
            </div>
            
            <div class="detalle-grid">
                <div class="detalle-item">
                    <label>RUC</label>
                    <div class="value"><?php echo htmlspecialchars($proveedor['ruc']); ?></div>
                </div>
                
                <div class="detalle-item">
                    <label>Teléfono</label>
                    <div class="value">📞 <?php echo htmlspecialchars($proveedor['telefono']); ?></div>
                </div>
                
                <div class="detalle-item">
                    <label>Contacto</label>
                    <div class="value"><?php echo htmlspecialchars($proveedor['contacto']); ?></div>
                </div>
                
                <div class="detalle-item">
                    <label>Email</label>
                    <div class="value">✉️ <?php echo htmlspecialchars($proveedor['email']); ?></div>
                </div>
                
                <div class="detalle-item detalle-full">
                    <label>Dirección</label>
                    <div class="value">📍 <?php echo htmlspecialchars($proveedor['direccion']); ?></div>
                </div>
                
                <div class="detalle-item">
                    <label>Fecha de Registro</label>
                    <div class="value">
                        <?php
                        if (!empty($proveedor['fecha_registro'])) {
                            echo date('d/m/Y H:i', strtotime($proveedor['fecha_registro']));
                        } else {
                            echo '—';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="detalle-item">
                    <label>Estado</label>
                    <div class="value">
                        <span class="badge <?php echo $proveedor['estado'] ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $proveedor['estado'] ? '✅ Activo' : '❌ Inactivo'; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="btn-group">
                <a href="/tiendaAbarrotes/controllers/ProveedorController.php?action=editar&id=<?php echo $proveedor['id']; ?>" class="btn-primary" style="padding: 12px 30px; text-decoration: none; border-radius: 8px;">
                    ✏️ Editar Proveedor
                </a>
                <a href="/tiendaAbarrotes/controllers/ProveedorController.php?action=listar" class="btn-secondary" style="padding: 12px 30px; text-decoration: none; border-radius: 8px;">
                    📋 Ver Todos
                </a>
            </div>
        </div>
    </div>
</body>
</html>
