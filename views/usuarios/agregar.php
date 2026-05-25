<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>

    <!-- Si quieres, puedes seguir cargando tu CSS global -->
    <!-- <link rel="stylesheet" href="../../assets/css/styles.css"> -->

    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{
            font-family:'Segoe UI',sans-serif;
            background:#f5f7fa;
        }

        /* NAV BAR BONITA */
        .navbar{
            background:linear-gradient(135deg,#667eea,#764ba2);
            color:#fff;
            padding:15px 30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .navbar-brand{
            display:flex;
            align-items:center;
            gap:10px;
            font-size:20px;
            font-weight:600;
        }
        .btn-back{
            background:rgba(255,255,255,0.15);
            border:1px solid rgba(255,255,255,0.4);
            color:#fff;
            padding:8px 18px;
            border-radius:999px;
            text-decoration:none;
            font-size:14px;
        }
        .btn-back:hover{
            background:rgba(255,255,255,0.25);
        }

        .container{
            max-width:1000px;
            margin:40px auto;
            padding:0 20px;
        }

        .form-card{
            background:white;
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 10px 30px rgba(0,0,0,0.06);
        }
        .form-header{
            background:linear-gradient(135deg,#84fab0,#8fd3f4);
            padding:40px;
            text-align:center;
            color:#333;
        }
        .form-header-icon{
            font-size:80px;
            margin-bottom:10px;
        }
        .form-header h1{
            font-size:30px;
            font-weight:700;
        }
        .form-body{
            padding:35px 40px 30px 40px;
        }
        .form-row{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
            margin-bottom:20px;
        }
        .form-group{
            display:flex;
            flex-direction:column;
            margin-bottom:15px;
        }
        .form-group label{
            font-size:14px;
            font-weight:600;
            margin-bottom:6px;
            color:#555;
        }
        .form-group input,
        .form-group select{
            padding:10px 12px;
            border-radius:8px;
            border:1px solid #d0d7e2;
            font-size:14px;
        }
        .form-group input:focus,
        .form-group select:focus{
            outline:none;
            border-color:#667eea;
            box-shadow:0 0 0 2px rgba(102,126,234,0.2);
        }
        .form-buttons{
            margin-top:20px;
            display:flex;
            justify-content:center;
            gap:15px;
        }
        .btn-primary,
        .btn-secondary{
            border:none;
            padding:10px 22px;
            border-radius:999px;
            font-size:14px;
            font-weight:600;
            cursor:pointer;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .btn-primary{
            background:#28a745;
            color:#fff;
        }
        .btn-primary:hover{
            background:#218838;
        }
        .btn-secondary{
            background:#dc3545;
            color:#fff;
        }
        .btn-secondary:hover{
            background:#c82333;
        }

        @media(max-width:768px){
            .form-body{padding:25px 20px;}
            .form-row{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <span>➕</span>
        <span>Agregar Usuario</span>
    </div>
    <!-- Ajusta la ruta si tu listado es otra -->
    <a href="/tiendaAbarrotes/controllers/UsuarioController.php?action=listar" class="btn-back">
        ← Volver
    </a>
</nav>

<div class="container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-header-icon">👤</div>
            <h1>Nuevo Usuario</h1>
        </div>

        <div class="form-body">
            <!-- IMPORTANTE: mandar al controlador, no a ?action= -->
            <form action="/tiendaAbarrotes/controllers/UsuarioController.php?action=crear" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Usuario *</label>
                        <input type="text" name="usuario" required autofocus>
                    </div>
                    <div class="form-group">
                        <label>Contraseña *</label>
                        <input type="password" name="clave" required minlength="6">
                    </div>
                </div>

                <div class="form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" name="nombre_completo" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" placeholder="usuario@correo.com" required>
                    </div>
                    <div class="form-group">
                        <label>Rol *</label>
                        <select name="rol" required>
                            <option value="">Seleccione...</option>
                            <option value="admin">Administrador</option>
                            <option value="vendedor">Vendedor</option>
                        </select>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-primary">💾 Guardar Usuario</button>
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
