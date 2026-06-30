Feature: Autenticacion de usuarios
  Como usuario del sistema
  Quiero iniciar y cerrar sesion
  Para acceder de forma segura a las rutas protegidas

  Scenario: CP-001 Inicio de sesion exitoso con credenciales validas
    Given el usuario se encuentra en la pagina de login
    When ingresa el correo "admin@tienda.com" y la contrasena "Admin123"
    And hace clic en "Iniciar sesion"
    Then el sistema valida las credenciales
    And registra la accion "LOGIN" en auditoria
    And redirige al dashboard
    And muestra un mensaje de bienvenida

  Scenario: CP-006 Cierre de sesion exitoso
    Given el usuario tiene una sesion activa
    When selecciona "Cerrar sesion"
    Then el sistema destruye la sesion
    And registra la accion "LOGOUT" en auditoria
    And redirige a la pagina de login
    And no permite acceder a rutas protegidas con la URL anterior
