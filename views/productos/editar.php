<?php
require_once __DIR__ . '/../../models/Proveedor.php';
$proveedorModel = new Proveedor();
$proveedores    = $proveedorModel->obtenerTodos();

// Se asume que $producto viene desde ProductoController->editar()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>

    <!-- Ruta ABSOLUTA al CSS general del sistema -->
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
        .product-id{
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
    <!-- Navbar consistente con el resto del sistema -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span>✏️</span>
            <span>Editar Producto</span>
        </div>
        <!-- Ruta ABSOLUTA al listado de productos -->
        <a href="/tiendaAbarrotes/controllers/ProductoController.php?action=listar" class="btn-back">
            ← Volver
        </a>
    </nav>

    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <h1>Editar Producto</h1>
            </div>
            <div class="form-body">
                <div class="product-id">
                    ID: #<?php echo htmlspecialchars($producto['id']); ?>
                </div>

                <!-- Acción ABSOLUTA para evitar problemas con ?action -->
                <form action="/tiendaAbarrotes/controllers/ProductoController.php?action=actualizar" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Código *</label>
                            <input type="text" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Categoría *</label>
                            <select name="categoria" required>
                                <?php
                                $cats = ['Granos','Aceites','Abarrotes','Enlatados','Pastas','Lácteos','Bebidas','Limpieza','Snacks','Conservas'];
                                foreach ($cats as $c) {
                                    $sel = ($c == $producto['categoria']) ? 'selected' : '';
                                    echo "<option $sel>".htmlspecialchars($c)."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Precio Compra (S/) *</label>
                            <input type="number" name="precio_compra" step="0.01" value="<?php echo htmlspecialchars($producto['precio_compra']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Precio Venta (S/) *</label>
                            <input type="number" name="precio_venta" step="0.01" value="<?php echo htmlspecialchars($producto['precio_venta']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Stock *</label>
                            <input type="number" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Stock Mínimo *</label>
                            <input type="number" name="stock_minimo" value="<?php echo htmlspecialchars($producto['stock_minimo']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Proveedor</label>
                        <select name="proveedor_id">
                            <option value="">Sin proveedor</option>
                            <?php foreach ($proveedores as $prov): ?>
                                <option value="<?php echo $prov['id']; ?>"
                                    <?php echo ($producto['proveedor_id'] == $prov['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($prov['razon_social']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fecha Vencimiento</label>
                        <input type="date" name="fecha_vencimiento"
                               value="<?php echo !empty($producto['fecha_vencimiento']) ? htmlspecialchars($producto['fecha_vencimiento']) : ''; ?>">
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-primary">💾 Actuali
