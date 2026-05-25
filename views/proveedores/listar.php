<?php
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
$termino = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores</title>
    <!-- RUTA ABSOLUTA AL CSS -->
    <link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span>🏢</span>
            <span>Gestión de Proveedores</span>
        </div>
        <!-- SIEMPRE VOLVER AL DASHBOARD CORRECTO -->
        <a href="/tiendaAbarrotes/views/dashboard.php" class="btn-back">← Volver al Dashboard</a>
    </nav>
    
    <div class="container">
        <div class="header-section">
            <h1>Listado de Proveedores</h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <!-- BUSCADOR GENERAL: AL CONTROLADOR -->
                <form action="/tiendaAbarrotes/controllers/ProveedorController.php" method="GET" class="search-box">
                    <input type="hidden" name="action" value="buscar">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Buscar por razón social, RUC o contacto..." 
                        value="<?php echo htmlspecialchars($termino); ?>"
                    >
                    <button type="submit" class="btn-add" style="background: #667eea; color: white;">🔍 Buscar</button>
                </form>
                <!-- NUEVO PROVEEDOR: TAMBIÉN AL CONTROLADOR -->
                <a href="/tiendaAbarrotes/controllers/ProveedorController.php?action=agregar" class="btn-add">
                    ➕ Nuevo Proveedor
                </a>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensaje); ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de búsqueda por RUC -->
        <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 15px; color: #333;">🔍 Búsqueda Rápida por RUC</h3>
            <form action="/tiendaAbarrotes/controllers/ProveedorController.php" method="GET" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="action" value="buscarPorRuc">
                <input 
                    type="text" 
                    name="ruc" 
                    placeholder="Ingrese RUC (11 dígitos)" 
                    pattern="[0-9]{11}"
                    maxlength="11"
                    style="flex: 1; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;"
                    required
                >
                <button type="submit" class="btn-add" style="background: #17a2b8; color: white;">
                    📄 Buscar por RUC
                </button>
            </form>
        </div>
        
        <div class="table-container">
            <?php if (!empty($proveedores) && count($proveedores) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>RUC</th>
                            <th>Razón Social</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $p): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($p['id']); ?></strong></td>
                                <td><span class="badge badge-success"><?php echo htmlspecialchars($p['ruc']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($p['razon_social']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['contacto']); ?></td>
                                <td>📞 <?php echo htmlspecialchars($p['telefono']); ?></td>
                                <td>✉️ <?php echo htmlspecialchars($p['email']); ?></td>
                                <td><?php echo htmlspecialchars($p['direccion']); ?></td>
                                <td>
                                    <!-- EDITAR / ELIMINAR VAN AL CONTROLADOR -->
                                    <a href="/tiendaAbarrotes/controllers/ProveedorController.php?action=editar&id=<?php echo $p['id']; ?>" class="btn-action btn-edit">
                                        ✏️ Editar
                                    </a>
                                    <a 
                                        href="/tiendaAbarrotes/controllers/ProveedorController.php?action=eliminar&id=<?php echo $p['id']; ?>" 
                                        class="btn-action btn-delete"
                                        onclick="return confirm('¿Está seguro de eliminar este proveedor?\n\nNOTA: Si tiene productos asociados, no se podrá eliminar.')"
                                    >
                                        🗑️ Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #999;">
                    <div style="font-size: 64px; margin-bottom: 20px;">📭</div>
                    <h3>No se encontraron proveedores</h3>
                    <p>
                        <?php if (!empty($termino)): ?>
                            No hay resultados para "<?php echo htmlspecialchars($termino); ?>"
                        <?php else: ?>
                            Comienza agregando tu primer proveedor
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
