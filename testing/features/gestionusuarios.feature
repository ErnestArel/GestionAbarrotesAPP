Feature: Gestion de usuarios
  Como administrador
  Quiero gestionar usuarios y roles
  Para controlar el acceso al sistema

  Background:
    Given el usuario ha iniciado sesion como administrador
    And se encuentra en el modulo de usuarios

  Scenario: CP-045 Creacion exitosa de usuario administrador
    When hace clic en "Nuevo usuario"
    And llena el formulario con los siguientes datos:
      | usuario         | juan@tienda.com |
      | clave           | Admin123        |
      | nombre_completo | Juan Perez      |
      | email           | juan@tienda.com |
      | rol             | admin           |
    And hace clic en "Guardar"
    Then el sistema crea el usuario exitosamente
    And muestra el mensaje "Usuario creado exitosamente"

  Scenario: CP-049 Verificar hash de contrasena en base de datos
    Given existe un usuario creado con contrasena "Admin123"
    When consulta la contrasena almacenada del usuario
    Then la contrasena no aparece en texto plano
    And usa hash bcrypt valido

  Scenario: CP-050 Desactivacion exitosa de usuario
    Given selecciona un usuario activo y hace clic en "Eliminar"
    When confirma la accion en el dialogo
    Then el sistema cambia el estado del usuario a inactivo
    And registra la accion en auditoria
    And el usuario no puede iniciar sesion

  Scenario: CP-053 Activacion exitosa de usuario inactivo
    Given existe un usuario inactivo
    When el administrador selecciona la opcion "Activar" y confirma
    Then el sistema cambia el estado del usuario a activo
    And el usuario puede iniciar sesion nuevamente

  Scenario: CP-055 Cambio de rol exitoso
    Given selecciona un usuario existente y hace clic en "Editar"
    When cambia el rol a "admin"
    And guarda los cambios
    Then el sistema actualiza el rol
    And el rol se aplica en el proximo inicio de sesion
