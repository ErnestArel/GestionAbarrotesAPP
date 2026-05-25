<?php
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand"><span>👥</span><span>Gestión de Usuarios</span></div>
        <a href="/tiendaAbarrotes/views/dashboard.php" class="btn-back">← Dashboard</a>
    </nav>

    <div class="container">
        <div class="header-section">
            <div>
                <h1>Listado de Usuarios</h1>
                <p>Administración de cuentas y roles del sistema</p>
            </div>
            <div>
                <a href="/tiendaAbarrotes/controllers/UsuarioController.php?action=agregar" class="btn-add">
                    ➕ Nuevo Usuario
                </a>
            </div>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (!empty($usuarios) && count($usuarios) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($u['usuario']); ?></strong></td>
                                <td><?php echo htmlspecialchars($u['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($u['rol'])); ?></td>
                                <td>
                                    <?php if ((int)$u['estado'] === 1): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/tiendaAbarrotes/controllers/UsuarioController.php?action=editar&id=<?php echo $u['id']; ?>" class="btn-action btn-edit">✏️ Editar</a>
                                    <a href="/tiendaAbarrotes/controllers/UsuarioController.php?action=eliminar&id=<?php echo $u['id']; ?>" class="btn-action btn-delete" onclick="return confirm('¿Eliminar este usuario?');">🗑️ Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center;padding:60px 20px;color:#999;">
                    <div style="font-size:64px;margin-bottom:20px;">📭</div>
                    <h3>No se encontraron usuarios</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
