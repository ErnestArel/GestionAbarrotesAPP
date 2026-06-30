Feature: Movimientos de stock
  Como administrador
  Quiero registrar entradas de stock
  Para actualizar el inventario y consultar su historial

  Background:
    Given el usuario ha iniciado sesion como administrador
    And se encuentra en el modulo de movimientos

  Scenario: CP-058 Registro exitoso de entrada de stock
    When selecciona un producto existente "PROD-001"
    And selecciona un proveedor valido
    And ingresa la cantidad 50
    And hace clic en "Registrar entrada"
    Then el sistema incrementa el stock del producto en 50 unidades
    And guarda el movimiento en el historial con fecha actual, cantidad y proveedor
    And registra la accion en auditoria
    And muestra el mensaje "Entrada registrada exitosamente"

  Scenario: CP-084 Historial de movimientos registra correctamente la entrada de stock
    Given se ha registrado una entrada de 30 unidades para "PROD-001" con proveedor "Distribuidora XYZ"
    When el administrador consulta el historial de movimientos
    Then el historial muestra la entrada con producto, cantidad, fecha, proveedor y tipo "entrada"
