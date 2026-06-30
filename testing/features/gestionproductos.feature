Feature: Gestion de productos
  Como administrador
  Quiero gestionar productos, busquedas, bajas logicas, vencimientos y categorias
  Para mantener actualizado el inventario

  Background:
    Given el usuario ha iniciado sesion como administrador
    And se encuentra en el modulo de productos

  Scenario: CP-008 Registro exitoso de producto con todos los campos validos
    When hace clic en "Nuevo producto"
    And llena el formulario con los siguientes datos:
      | SKU               | PROD-001          |
      | nombre            | Arroz Costeno 1kg |
      | categoria         | Granos            |
      | precio_compra     | 2.50              |
      | precio_venta      | 3.80              |
      | stock_inicial     | 100               |
      | stock_minimo      | 20                |
      | fecha_vencimiento | 2026-12-31        |
    And hace clic en "Guardar"
    Then el sistema registra el producto exitosamente
    And muestra el mensaje "Producto registrado exitosamente"
    And el nuevo producto aparece en el listado de inventario

  Scenario: CP-013 Edicion exitosa de datos de producto existente
    Given selecciona un producto existente y hace clic en "Editar"
    When modifica el precio_venta a 4.20 y el stock_minimo a 15
    And hace clic en "Guardar cambios"
    Then el sistema actualiza la informacion en la base de datos
    And registra la accion "ACTUALIZAR" en auditoria
    And muestra el mensaje "Producto actualizado exitosamente"

  Scenario: CP-018 Busqueda exitosa de producto por nombre
    When busca "Arroz Costeno 1kg" en la barra de busqueda
    Then el sistema muestra los productos coincidentes
    And el producto buscado aparece en el listado

  Scenario: CP-019 Busqueda de producto por SKU exacto
    When busca "PROD-001"
    Then el sistema muestra la ficha del producto correspondiente

  Scenario: CP-021 Busqueda por categoria
    When busca "Granos" en la barra de busqueda
    Then el sistema lista unicamente productos de esa categoria

  Scenario: CP-023 Baja logica exitosa de producto activo
    Given selecciona un producto activo y hace clic en "Eliminar"
    When confirma la accion en el dialogo
    Then el sistema cambia el estado del producto a inactivo
    And registra la accion "ELIMINAR" en auditoria
    And muestra el mensaje "Producto desactivado exitosamente"
    And el producto desaparece del listado principal

  Scenario: CP-025 Producto inactivo no aparece en formularios de movimientos
    Given existe un producto inactivo con SKU "PROD-001"
    When el administrador accede al formulario de movimientos
    Then el producto "PROD-001" no aparece en el selector de productos

  Scenario: CP-026 Visualizacion de productos con vencimiento menor o igual a 30 dias
    Given existen productos con fecha_vencimiento dentro de los proximos 30 dias
    When el administrador accede al dashboard o al modulo de alertas
    Then el sistema muestra la lista de esos productos
    And cada producto muestra nombre, categoria, stock y fecha de vencimiento
    And la lista esta ordenada por fecha de vencimiento ascendente

  Scenario: CP-028 Producto con vencimiento exactamente en dia 30
    Given existe un producto con fecha_vencimiento exactamente a 30 dias
    When el administrador accede al dashboard o al modulo de alertas
    Then el producto aparece en la lista de alertas

  Scenario: CP-029 Registro exitoso de categoria con nombre valido
    When navega a la gestion de categorias
    And ingresa el nombre "Lacteos" y hace clic en "Guardar"
    Then el sistema registra la categoria
    And muestra el mensaje "Categoria registrada exitosamente"

  Scenario: CP-030 Actualizacion exitosa de categoria
    Given selecciona la categoria "Lacteos" y hace clic en "Editar"
    When cambia el nombre a "Lacteos y derivados" y guarda
    Then el sistema actualiza la categoria correctamente

  Scenario: CP-076 Desactivacion exitosa de categoria sin productos asociados
    Given la categoria "Bebidas" no tiene productos asociados
    When intenta desactivarla y confirma
    Then el sistema desactiva la categoria
    And ya no aparece en el listado activo ni en formularios de producto

  Scenario: CP-077 Categoria desactivada no aparece en formulario de nuevo producto
    Given la categoria "Bebidas" esta desactivada
    When hace clic en "Nuevo producto"
    Then la categoria "Bebidas" no aparece en el selector de categorias
