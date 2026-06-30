Feature: Auditoria
  Como administrador
  Quiero consultar los registros de auditoria
  Para rastrear acciones importantes del sistema

  Background:
    Given el usuario ha iniciado sesion como administrador

  Scenario: CP-085 Log registra operacion CREAR al registrar nuevo producto
    Given se ha registrado un nuevo producto
    When el administrador navega al modulo de auditoria
    Then existe un registro con accion "CREAR"
    And el registro pertenece al modulo "Productos"
    And muestra usuario, fecha, hora e IP

  Scenario: CP-086 Log registra operacion ELIMINAR al desactivar proveedor
    Given se ha desactivado un proveedor
    When el administrador navega al modulo de auditoria
    Then existe un registro con accion "ELIMINAR"
    And el registro pertenece al modulo "Proveedores"
    And muestra usuario, fecha, hora e IP
