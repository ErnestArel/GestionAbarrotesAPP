<?php
// Se asume que $usuario viene desde UsuarioController->editar()
// con claves: id, usuario, nombre_completo, email, rol
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>

    <!-- CSS general del sistema -->
    <link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">

    <style>
        .form-card{
            background:white;
            border-radius:15px;
            overflow:hidden;
            max-width:900px;
            margin:30px auto;
            box-shadow:0 2px 10px rgba(0,0,0,0.05);
        }
        .form-header{
            background:linear-gradient(135deg,#ffc107,#ff9800);
            padding:40px;
            text-align:center;
        }
        .form-header h1{
            margin:0;
            color:#333;
            font-size:28px;
        }
        .form-body{
            padding:40px;
        }
        .user-id{
            background:#f8f9fa;
            padding:15px;
            border-radius:8px;
            margin-bottom:25px;
            text-align:center;
            font-weight:bold;
            color:#555;
        }
        .form-row{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
        }
        .form-group{
            margin-bottom:15px;
        }
        .form-group label{
            display:block;
            margin-bottom:5px;
            font-weight:600;
            font-size:13px;
            color:#555;
        }
        .form-group small{
            color:#999;
            font-size:11px;
        }
        .form-group input,
        .form-group select{
            width:100%;
            padding:10px;
            border:2px solid #e0e0e0;
            border-radius:8px;
            font-size:14px;
        }
        .form-buttons{
            margin-top:25px;
            display:flex;
            gap:10px;
            justify-content:flex-end;
        }
        .btn-primary{
            background:linear-gradient(135deg,#ffc107,#ff9800);
            border:none;
            padding:10px 25px;
            border-radius:8px;
            color:#333;
            font-weight:bold;
            cursor:pointer;
        }
        .btn-secondary{
            background:#6c757d;
            color:white;
            padding:10px 25px;
            border-radius:8px;
            text-decoration:none;
            font-weight:bold;
        }
        @media (max-width:768px){
            .form-row{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>
    <!-- Navbar como en los demás módulos -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span>✏️</span>
            <span>Editar Usuario</span>
        </div>
        <!-- Volver al listado -->
        <a href="/tiendaAbarrotes/controllers/UsuarioController.php?action=listar" class="btn-back">
            ← Volver
        </a>
    </nav>

    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <h1>Editar Usuario</h1>
            </div>

            <div class="form-body">
                <div class="user-id">
                    ID: #<?php echo htmlspecialchars($usuario['id']); ?>
                </div>

                <!-- Acción ABSOLUTA: actualizar usuario -->
                <form action="/tiendaAbarrotes/controllers/UsuarioController.php?action=actualizar" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Usuario *</label>
                            <input type="text" name="usuario"
                                   value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Nueva Contraseña</label>
                            <input type="password" name="clave" autocomplete="new-password">
                            <small>Dejar en blanco para no cambiarla</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nombre Completo *</label>
                        <input type="text" name="nombre_completo"
                               value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email"
                                   value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Rol *</label>
                            <select name="rol" required>
                                <?php
                                $roles = ['admin' => 'Administrador', 'vendedor' => 'Vendedor'];
                                foreach ($roles as $valor => $texto):
                                    $sel = ($usuario['rol'] === $valor) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $valor; ?>" <?php echo $sel; ?>>
                                        <?php echo $texto; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-primary">💾 Actualizar</button>
                        <a href="/tiendaAbarrotes/controllers/UsuarioController.php?action=listar" class="btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
