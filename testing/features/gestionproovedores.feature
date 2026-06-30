Feature: Gestion de proveedores
  Como administrador
  Quiero gestionar proveedores
  Para mantener actualizada la informacion comercial

  Background:
    Given el usuario ha iniciado sesion como administrador
    And se encuentra en el modulo de proveedores

  Scenario: CP-033 Registro exitoso de proveedor con RUC valido de 11 digitos
    When hace clic en "Nuevo proveedor"
    And llena el formulario con los siguientes datos:
      | RUC          | 20601234567       |
      | razon_social | Distribuidora XYZ |
      | telefono     | 987654321         |
      | email        | ventas@xyz.com    |
      | contacto     | Juan Perez        |
      | direccion    | Av. Principal 123 |
    And hace clic en "Guardar"
    Then el sistema registra el proveedor exitosamente
    And muestra el mensaje "Proveedor registrado exitosamente"
    And redirige al listado de proveedores

  Scenario: CP-036 Edicion exitosa de datos de proveedor existente
    Given selecciona un proveedor y hace clic en "Editar"
    When modifica el telefono a "999888777" y el email a "nuevo@xyz.com"
    And guarda los cambios
    Then el sistema actualiza el registro
    And registra la accion "ACTUALIZAR" en auditoria
    And muestra el mensaje "Proveedor actualizado exitosamente"

  Scenario: CP-037 Eliminacion fisica exitosa de proveedor sin productos asociados
    Given selecciona un proveedor que no tiene productos asociados
    When confirma la eliminacion
    Then el sistema elimina fisicamente el registro
    And registra la accion "ELIMINAR" en auditoria
    And el proveedor desaparece del listado

  Scenario: CP-040 Busqueda exitosa de proveedor con RUC existente
    When busca el RUC "20601234567"
    Then el sistema muestra la ficha completa del proveedor con todos sus datos

  Scenario: CP-043 Reactivacion exitosa de proveedor inactivo
    Given existe un proveedor inactivo
    When el administrador selecciona la opcion "Reactivar" y confirma
    Then el sistema cambia el estado a activo
    And registra la accion en auditoria
    And el proveedor aparece nuevamente en el listado activo

  Scenario: CP-044 Proveedor reactivado disponible en formularios de movimiento
    Given el proveedor ha sido reactivado
    When el administrador accede al formulario de entrada de stock
    Then el proveedor reactivado aparece en el selector de proveedores
