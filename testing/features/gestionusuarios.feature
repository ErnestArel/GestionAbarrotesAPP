Feature: Gestión de Usuarios
  Como administrador
  Quiero gestionar las cuentas de usuario (creación, edición, activación/desactivación, cambio de rol)
  Para controlar el acceso al sistema y mantener la seguridad

  Background:
    Given el usuario ha iniciado sesión como administrador
    And se encuentra en el módulo de usuarios

  # ===== US-12: Crear usuario =====
  Scenario: USR-UAT-001 Crear usuario administrador exitosamente
    When hace clic en "Nuevo usuario"
    And llena el formulario con los siguientes datos:
      | nombre     | Juan Pérez      |
      | email      | juan@tienda.com |
      | password   | Admin123        |
    And hace clic en "Crear usuario"
    Then el sistema guarda el usuario con contraseña hasheada (bcrypt)
    And el estado es activo (activo = true)
    And muestra el mensaje "Usuario creado exitosamente"

  Scenario: USR-UAT-002 Crear usuario con correo duplicado
    Given ya existe un usuario con email "juan@tienda.com"
    When intenta crear otro con el mismo email
    Then el sistema muestra el error "El correo ingresado ya está en uso"
    And no crea el usuario

  Scenario: USR-UAT-003 Verificar hash de contraseña en base de datos
    When se crea un usuario
    Then la contraseña almacenada en la BD no es texto plano
    And es verificable con password_verify()

  # ===== US-13: Activar/Desactivar usuario =====
  Scenario: USR-UAT-004 Desactivar usuario exitosamente (distinto al propio)
    Given selecciona un usuario diferente a su propia cuenta
    When confirma la desactivación
    Then el sistema cambia el estado a inactivo (activo = false)
    And registra la acción "ELIMINAR" en auditoría
    And el usuario no puede iniciar sesión

  Scenario: USR-UAT-005 Intentar desactivar la propia cuenta
    Given selecciona su propia cuenta de usuario
    When intenta desactivarla
    Then el sistema bloquea la operación
    And muestra el mensaje "No puede desactivar su propia cuenta"

  Scenario: USR-UAT-006 Activar usuario inactivo
    Given selecciona un usuario inactivo
    When confirma la activación
    Then el sistema cambia el estado a activo (activo = true)
    And el usuario puede iniciar sesión nuevamente

  # ===== US-14: Cambiar rol de usuario =====
  Scenario: USR-UAT-007 Cambiar rol de usuario exitosamente
    Given selecciona un usuario registrado
    When modifica el rol (en esta versión solo "Administrador") y guarda
    Then el sistema actualiza el rol en la base de datos
    And registra la acción en auditoría
    And el cambio se aplica en el próximo inicio de sesión