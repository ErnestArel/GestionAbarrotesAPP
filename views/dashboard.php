<?php
session_start();
require_once __DIR__.'/../controllers/AuthController.php';
require_once __DIR__.'/../models/Producto.php';
require_once __DIR__.'/../models/Usuario.php';
require_once __DIR__.'/../models/Proveedor.php';
AuthController::verificarSesion();

$nombre=$_SESSION['nombre_completo']??'';
$rol=$_SESSION['rol']??'';
$productoModel=new Producto();
$usuarioModel=new Usuario();
$proveedorModel=new Proveedor();

$totalProductos=count($productoModel->obtenerTodos());
$stockBajo=count($productoModel->stockBajo());
$totalUsuarios=count($usuarioModel->obtenerTodos());
$totalProveedores=count($proveedorModel->obtenerTodos());

$menuItems=[
    ['icon'=>'📦','titulo'=>'Gestión de Productos','desc'=>'CRUD completo de productos','url'=>'../controllers/ProductoController.php?action=listar','color'=>'blue'],
    ['icon'=>'👥','titulo'=>'Gestión de Usuarios','desc'=>'CRUD completo de usuarios','url'=>'../controllers/UsuarioController.php?action=listar','color'=>'green'],
    ['icon'=>'🏢','titulo'=>'Gestión de Proveedores','desc'=>'CRUD completo de proveedores','url'=>'../controllers/ProveedorController.php?action=listar','color'=>'purple'],
    ['icon'=>'🔍','titulo'=>'Auditoría','desc'=>'Registro de actividades','url'=>'../controllers/AuditoriaController.php','color'=>'orange'],
    ['icon'=>'💾','titulo'=>'Respaldo de Datos','desc'=>'Exportar base de datos','url'=>'../controllers/RespaldoController.php','color'=>'red'],
    ['icon'=>'📊','titulo'=>'Reportes','desc'=>'Reportes y estadísticas','url'=>'../controllers/ReporteController.php','color'=>'pink']

];
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Dashboard</title>
<link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">

<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
.navbar{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
.navbar-brand{display:flex;align-items:center;gap:15px;font-size:24px;font-weight:bold}
.user-info{text-align:right}.user-name{font-weight:bold;font-size:16px}.user-role{font-size:12px;opacity:0.8}
.btn-logout{background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);padding:8px 20px;border-radius:20px;text-decoration:none;font-size:14px;display:inline-block;margin-top:5px}
.container{max-width:1400px;margin:0 auto;padding:30px}
.welcome-section{background:white;padding:30px;border-radius:15px;margin-bottom:30px;box-shadow:0 2px 10px rgba(0,0,0,0.05)}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:30px}
.stat-card{background:white;padding:25px;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,0.05);display:flex;align-items:center;gap:20px;transition:transform 0.3s}
.stat-card:hover{transform:translateY(-5px)}
.stat-icon{width:60px;height:60px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:28px}
.stat-icon.blue{background:linear-gradient(135deg,#667eea,#764ba2)}.stat-icon.green{background:linear-gradient(135deg,#84fab0,#8fd3f4)}
.stat-icon.orange{background:linear-gradient(135deg,#fa709a,#fee140)}.stat-icon.purple{background:linear-gradient(135deg,#a8edea,#fed6e3)}
.stat-info h3{color:#999;font-size:14px;margin-bottom:5px}.stat-info .number{color:#333;font-size:32px;font-weight:bold}
.menu-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.menu-card{background:white;padding:30px;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,0.05);transition:all 0.3s;text-decoration:none;color:inherit;display:block;cursor:pointer;border-top:4px solid}
.menu-card.blue{border-color:#667eea}.menu-card.green{border-color:#84fab0}.menu-card.purple{border-color:#a8edea}
.menu-card.orange{border-color:#fa709a}.menu-card.red{border-color:#ff6b6b}.menu-card.pink{border-color:#ffc3a0}
.menu-card:hover{transform:translateY(-5px);box-shadow:0 10px 30px rgba(0,0,0,0.15)}
.menu-icon{font-size:48px;margin-bottom:15px}.menu-card h3{color:#333;font-size:20px;margin-bottom:10px}
.menu-card p{color:#666;font-size:14px;line-height:1.5}
@media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr)}.menu-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.menu-grid{grid-template-columns:1fr}}
</style></head><body>
<nav class="navbar">
<div class="navbar-brand"><span>🛒</span><span>La canasta de buena vida</span></div>
<div><div class="user-info"><div class="user-name"><?php echo htmlspecialchars($nombre);?></div>
<div class="user-role"><?php echo ucfirst($rol);?></div>
<a href="../controllers/AuthController.php?action=logout" class="btn-logout">Cerrar Sesión</a></div></div>
</nav>
<div class="container">
<div class="welcome-section"><h1>🎉 ¡Bienvenido <?php echo htmlspecialchars($nombre);?>!</h1>
<p>Sistema completo de gestión para tu tienda de abarrotes</p></div>
<div class="stats-grid">
<div class="stat-card"><div class="stat-icon blue">📦</div><div class="stat-info"><h3>Productos</h3><div class="number"><?php echo $totalProductos;?></div></div></div>
<div class="stat-card"><div class="stat-icon green">👥</div><div class="stat-info"><h3>Usuarios</h3><div class="number"><?php echo $totalUsuarios;?></div></div></div>
<div class="stat-card"><div class="stat-icon purple">🏢</div><div class="stat-info"><h3>Proveedores</h3><div class="number"><?php echo $totalProveedores;?></div></div></div>
<div class="stat-card"><div class="stat-icon orange">⚠️</div><div class="stat-info"><h3>Stock Bajo</h3><div class="number"><?php echo $stockBajo;?></div></div></div>
</div>
<div class="menu-grid">
<?php foreach($menuItems as $item):?>
<a href="<?php echo $item['url'];?>" class="menu-card <?php echo $item['color'];?>" 
   <?php if(isset($item['onclick'])):?>onclick="<?php echo $item['onclick'];?>;return false"<?php endif;?>>
<div class="menu-icon"><?php echo $item['icon'];?></div>
<h3><?php echo $item['titulo'];?></h3>
<p><?php echo $item['desc'];?></p>
</a>
<?php endforeach;?>
</div>
</div></body></html>