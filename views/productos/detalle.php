<?php
// Se asume que $producto viene desde ProductoController->buscarPorCodigo() o ->editar()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Producto</title>

    <!-- Usamos ruta ABSOLUTA para que siempre encuentre el CSS -->
    <link rel="stylesheet" href="/tiendaAbarrotes/assets/css/styles.css">

    <style>
        .detalle-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            max-width: 900px;
            margin: 30px auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .detalle-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .detalle-icon {
            font-size: 80px;
            margin-bottom: 10px;
        }
        .detalle-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .detalle-subtitle {
            font-size: 13px;
            color: #888;
        }
        .detalle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .detalle-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .detalle-item label {
            display: block;
            color: #999;
            font-size: 11px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .detalle-item .value {
            color: #333;
            font-size: 16px;
            font-weight: 600;
        }
        .detalle-full {
            grid-column: 1 / -1;
        }
        @media (max-width: 768px) {
            .detalle-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navbar igual que en otros módulos -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span>📦</span>
            <span>Detalle del Producto</span>
        </div>
        <!-- Ruta ABSOLUTA al listado de productos -->
        <a href="/tiendaAbarrotes/controllers/ProductoController.php?action=listar" class="btn-back">
            ← Volver
        </a>
    </nav>

    <div class="container">
        <div class="detalle-card">
            <div class="detalle-header">
                <div class="detalle-icon">📦</div>
                <div class="detalle-title">
                    <?php echo htmlspecialchars($producto['nombre']); ?>
                </div>
                <div class="detalle-subtitle">
                    Código: <?php echo htmlspecialchars($producto['codigo']); ?>
                </div>
            </div>

            <div class="detalle-grid">
                <div class="detalle-item">
                    <label>Categoría</label>
                    <div class="value">
                        <?php echo htmlspecialchars($producto['categoria']); ?>
                    </div>
                </div>

                <div class="detalle-item">
                    <label>Proveedor</label>
                    <div class="value">
                        <?php echo htmlspecialchars($producto['proveedor_nombre'] ?? 'Sin asignar'); ?>
                    </div>
                </div>

                <div class="detalle-item">
                    <label>Precio Compra</label>
                    <div class="value">
                        S/ <?php echo number_format($producto['precio_compra'], 2); ?>
                    </div>
                </div>

                <div class="detalle-item">
                    <label>Precio Venta</label>
                    <div class="value">
                        S/ <?php echo number_format($producto['precio_venta'], 2); ?>
                    </div>
                </div>

                <div class="detalle-item">
                    <label>Stock Actual</label>
                    <div class="value">
                        <?php if ($producto['stock'] < $producto['stock_minimo']): ?>
                            <span class="badge badge-warning">
                                ⚠️ <?php echo (int)$producto['stock']; ?> unidades
                            </span>
                        <?php else: ?>
                            <span class="badge badge-success">
                                ✅ <?php echo (int)$producto['stock']; ?> unidades
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detalle-item">
                    <label>Stock mínimo</label>
                    <div class="value">
                        <?php echo (int)$producto['stock_minimo']; ?> unidades
                    </div>
                </div>

                <?php if (!empty($producto['fecha_vencimiento'])): ?>
                    <div class="detalle-item detalle-full">
                        <label>Fecha de vencimiento</label>
                        <div class="value">
                            📅 <?php echo date('d/m/Y', strtotime($producto['fecha_vencimiento'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Botones de acción -->
            <div style="display:flex; gap:10px; justify-content:center; margin-top:30px;">
                <a href="/tiendaAbarrotes/controllers/ProductoController.php?action=editar&id=<?php echo $producto['id']; ?>"
                   class="btn-action btn-edit"
                   style="text-decoration:none; padding:12px 30px; border-radius:8px;">
                    ✏️ Editar Producto
                </a>

                <a href="/tiendaAbarrotes/controllers/ProductoController.php?action=listar"
                   class="btn-action"
                   style="text-decoration:none; padding:12px 30px; border-radius:8px;">
                    📋 Ver Todos
                </a>
            </div>
        </div>
    </div>
</body>
</html>
