<?php
session_start();
if(isset($_SESSION['autenticado'])&&$_SESSION['autenticado']===true){header('Location:views/dashboard.php');exit();}
$error=$_SESSION['error_login']??'';unset($_SESSION['error_login']);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login - La canasta de buena vida </title><style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;display:flex;justify-content:center;align-items:center;padding:20px}
.login-container{background:white;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;width:100%;max-width:900px;display:flex}
.login-left{flex:1;background:linear-gradient(135deg,#f093fb,#f5576c);padding:60px 40px;color:white;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center}
.logo{width:150px;height:150px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:30px;box-shadow:0 10px 30px rgba(0,0,0,0.2);font-size:80px}
.login-left h1{font-size:36px;margin-bottom:15px}.features{margin-top:40px;display:flex;gap:20px}
.feature-item{background:rgba(255,255,255,0.2);padding:20px;border-radius:10px;flex:1}.feature-icon{font-size:40px;margin-bottom:10px}
.login-right{flex:1;padding:60px 50px}.login-right h2{color:#333;font-size:32px;margin-bottom:10px}.subtitle{color:#666;margin-bottom:40px}
.form-group{margin-bottom:25px}.form-group label{display:block;color:#555;font-weight:600;margin-bottom:8px}
.form-group input{width:100%;padding:15px;border:2px solid #e0e0e0;border-radius:10px;font-size:15px}
.form-group input:focus{outline:none;border-color:#667eea;box-shadow:0 0 0 3px rgba(102,126,234,0.1)}
.btn-login{width:100%;padding:15px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:10px;font-size:16px;font-weight:bold;cursor:pointer}
.error-message{background:#fee;color:#c33;padding:12px 15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #c33}
.credentials-box{margin-top:30px;padding:15px;background:#f0f8ff;border-radius:8px;font-size:13px}code{background:#e8f4f8;padding:2px 6px;border-radius:4px;color:#d63384}
@media(max-width:768px){.login-container{flex-direction:column}}
</style></head><body>
<div class="login-container">
<div class="login-left"><div class="logo">🛒</div><h1>La canasta de buena vida </h1><p>Sistema de Gestión Integral</p>
<div class="features">
<div class="feature-item"><div class="feature-icon">📦</div><small>Inventario</small></div>
<div class="feature-item"><div class="feature-icon">💰</div><small>Precios</small></div>
<div class="feature-item"><div class="feature-icon">📊</div><small>Reportes</small></div>
</div></div>
<div class="login-right"><h2>Iniciar Sesión</h2><p class="subtitle">Ingresa tus credenciales</p>
<?php if(!empty($error)):?><div class="error-message"><?php echo htmlspecialchars($error);?></div><?php endif;?>
<form action="controllers/AuthController.php?action=login" method="POST">
<div class="form-group"><label>👤 Usuario</label><input type="text" name="usuario" required autofocus></div>
<div class="form-group"><label>🔒 Contraseña</label><input type="password" name="clave" required></div>
<button type="submit" class="btn-login">Ingresar al Sistema</button>
</form>
<div class="credentials-box"><strong>Credenciales:</strong><br>Usuario: <code>admin</code> / Contraseña: <code>password123</code></div>
</div></div></body></html>