<?php
// Variables que vienen desde ReporteController:
// $totalProductos, $totalStockBajo, $totalUsuarios, $totalProveedores, $stockBajo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes</title>
    <link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">
    <style>
        .kpi-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:20px;
            margin-top:20px;
            margin-bottom:30px;
        }
        .kpi-card{
            background:white;
            border-radius:15px;
            padding:20px 25px;
            box-shadow:0 2px 10px rgba(0,0,0,0.05);
        }
        .kpi-title{font-size:14px;color:#777;margin-bottom:8px;}
        .kpi-value{font-size:32px;font-weight:bold;color:#333;margin-bottom:5px;}
        .kpi-sub{font-size:12px;color:#999;}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand"><span>📊</span><span>Reportes del Sistema</span></div>
        <a href="/tiendaAbarrotes/views/dashboard.php" class="btn-back">← Dashboard</a>
    </nav>

    <div class="container">
        <div class="header-section">
            <div>
                <h1>Resumen general</h1>
                <p>Indicadores principales de la tienda</p>
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-title">Productos registrados</div>
                <div class="kpi-value"><?php echo $totalProductos; ?></div>
                <div class="kpi-sub">Total en el catálogo</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Productos con stock bajo</div>
                <div class="kpi-value"><?php echo $totalStockBajo; ?></div>
                <div class="kpi-sub">Por debajo del stock mínimo</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Usuarios activos</div>
                <div class="kpi-value"><?php echo $totalUsuarios; ?></div>
                <div class="kpi-sub">Cuentas en el sistema</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Proveedores registrados</div>
                <div class="kpi-value"><?php echo $totalProveedores; ?></div>
                <div class="kpi-sub">Aliados comerciales</div>
            </div>
        </div>

        <div class="table-container">
            <?php if ($totalStockBajo > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Stock mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stockBajo as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                                <td><span class="badge badge-danger"><?php echo $p['stock']; ?></span></td>
                                <td><?php echo $p['stock_minimo']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center;padding:40px 20px;color:#999;">
                    <div style="font-size:48px;margin-bottom:10px;">✅</div>
                    <h3>No hay productos con stock bajo</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
