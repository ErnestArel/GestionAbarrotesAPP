<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', sans-serif; background: #f5f7fa;}
        .navbar {background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;}
        .navbar-brand {display: flex; align-items: center; gap: 15px; font-size: 24px; font-weight: bold;}
        .btn-back {background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 20px; border-radius: 20px; text-decoration: none;}
        .container {max-width: 900px; margin: 30px auto; padding: 0 20px;}
        .form-card {background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;}
        .form-header {background: linear-gradient(135deg, #ffc107, #ff9800); padding: 40px; text-align: center;}
        .form-header-icon {font-size: 80px; margin-bottom: 15px;}
        .form-header h1 {color: #333; font-size: 28px; margin-bottom: 5px;}
        .form-body {padding: 40px;}
        .product-id {background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center;}
        .product-id strong {color: #667eea; font-size: 16px;}
        .form-row {display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;}
        .form-group {margin-bottom: 20px;}
        .form-group label {display: block; color: #555; font-weight: 600; margin-bottom: 8px; font-size: 14px;}
        .form-group label span {color: #dc3545;}
        .form-group input, .form-group textarea {width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit;}
        .form-group textarea {resize: vertical; min-height: 80px;}
        .form-buttons {display: flex; gap: 15px; justify-content: center; margin-top: 30px;}
        .btn {padding: 12px 30px; border: none; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block;}
        .btn-warning {background: linear-gradient(135deg, #ffc107, #ff9800); color: #333;}
        .btn-secondary {background: #6c757d; color: white;}
        @media (max-width: 768px) {.form-row {grid-template-columns: 1fr;}}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span>✏️</span>
            <span>Editar Proveedor</span>
        </div>
        <!-- VOLVER SIEMPRE AL CONTROLADOR -->
        <a href="/tiendaAbarrotes/controllers/ProveedorController.php?action=listar" class="btn-back">← Volver</a>
    </nav>
    
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-header-icon">✏️</div>
                <h1>Editar Proveedor</h1>
                <p>Modifique los datos del proveedor</p>
            </div>
            
            <div class="form-body">
                <div class="product-id">
                    <strong>ID del Proveedor: #<?php echo htmlspecialchars($proveedor['id']); ?></strong>
                </div>
                
                <!-- IMPORTANTE: ACTION DIRECTO AL CONTROLADOR -->
                <form action="/tiendaAbarrotes/controllers/ProveedorController.php?action=actualizar" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($proveedor['id']); ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ruc">RUC <span>*</span></label>
                            <input 
                                type="text" 
                                id="ruc" 
                                name="ruc" 
                                value="<?php echo htmlspecialchars($proveedor['ruc']); ?>"
                                pattern="[0-9]{11}"
                                maxlength="11"
                                required
                                autofocus
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono <span>*</span></label>
                            <input 
                                type="tel" 
                                id="telefono" 
                                name="telefono" 
                                value="<?php echo htmlspecialchars($proveedor['telefono']); ?>"
                                pattern="[0-9]{9}"
                                maxlength="9"
                                required
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="razon_social">Razón Social <span>*</span></label>
                        <input 
                            type="text" 
                            id="razon_social" 
                            name="razon_social" 
                            value="<?php echo htmlspecialchars($proveedor['razon_social']); ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contacto">Persona de Contacto <span>*</span></label>
                            <input 
                                type="text" 
                                id="contacto" 
                                name="contacto" 
                                value="<?php echo htmlspecialchars($proveedor['contacto']); ?>"
                                required
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email <span>*</span></label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="<?php echo htmlspecialchars($proveedor['email']); ?>"
                                required
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="direccion">Dirección <span>*</span></label>
                        <textarea 
                            id="direccion" 
                            name="direccion" 
                            required
                        ><?php echo htmlspecialchars($proveedor['direccion']); ?></textarea>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-warning">
                            💾 Actualizar Proveedor
                        </button>
                        <a href="/tiendaAbarrotes/controllers/ProveedorController.php?action=listar" class="btn btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
