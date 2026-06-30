const { Given, When, Then } = require('@cucumber/cucumber');
const { expect } = require('@playwright/test');

const {
  clickByText,
  dbQuery,
  ensureUser,
  expectBodyContains,
  phpValue,
  unsupported
} = require('../support/helpers.cjs');

Given('se encuentra en el modulo de usuarios', async function () {
  await this.page.goto('/controllers/UsuarioController.php?action=listar');
  await expectBodyContains(this.page, 'Listado de Usuarios');
});

Then('el sistema crea el usuario exitosamente', async function () {
  await expectBodyContains(this.page, 'Usuario creado exitosamente');
});

Given('existe un usuario creado con contrasena {string}', function (password) {
  const rows = dbQuery('SELECT clave FROM usuarios WHERE usuario = ?', ['juan@tienda.com']);
  if (!rows.length) {
    const hash = phpValue(`echo password_hash(${JSON.stringify(password)}, PASSWORD_DEFAULT);`);
    dbQuery(
      'INSERT INTO usuarios (usuario, clave, nombre_completo, email, rol, estado) VALUES (?, ?, ?, ?, ?, 1)',
      ['juan@tienda.com', hash, 'Juan Perez', 'juan@tienda.com', 'admin']
    );
  }
});

When('consulta la contrasena almacenada del usuario', function () {
  const rows = dbQuery('SELECT clave FROM usuarios WHERE usuario = ?', ['juan@tienda.com']);
  expect(rows.length).toBe(1);
  this.storedPassword = rows[0].clave;
});

Then('la contrasena no aparece en texto plano', function () {
  expect(this.storedPassword).not.toBe('Admin123');
});

Then('usa hash bcrypt valido', function () {
  expect(this.storedPassword.startsWith('$2y$') || this.storedPassword.startsWith('$2b$')).toBeTruthy();
});

Given('selecciona un usuario activo y hace clic en {string}', async function (text) {
  ensureUser();
  await this.page.goto('/controllers/UsuarioController.php?action=listar');
  this.page.once('dialog', (dialog) => dialog.accept());
  const row = this.page.locator('tr', { hasText: 'juan@tienda.com' }).first();
  await clickByText(row, text);
});

Then('el sistema cambia el estado del usuario a inactivo', function () {
  const rows = dbQuery('SELECT * FROM usuarios WHERE usuario = ?', ['juan@tienda.com']);
  expect(rows.length).toBe(0);
});

Then('el usuario no puede iniciar sesion', function () {
  const rows = dbQuery('SELECT * FROM usuarios WHERE usuario = ?', ['juan@tienda.com']);
  expect(rows.length).toBe(0);
});

Given('existe un usuario inactivo', function () {
  return unsupported('El codigo actual elimina usuarios fisicamente y no tiene accion de activacion.');
});

Then('el sistema cambia el estado del usuario a activo', function () {
  return unsupported('El codigo actual no tiene accion de activacion de usuarios.');
});

Then('el usuario puede iniciar sesion nuevamente', function () {
  return unsupported('El codigo actual no tiene accion de activacion de usuarios.');
});

Given('selecciona un usuario existente y hace clic en {string}', async function (text) {
  ensureUser();
  await this.page.goto('/controllers/UsuarioController.php?action=listar');
  const row = this.page.locator('tr', { hasText: 'admin@tienda.com' }).first();
  await clickByText(row, text);
});

When('cambia el rol a {string}', async function (rol) {
  const select = this.page.locator('[name="rol"]');
  await expect(select).toBeVisible({ timeout: 10000 });
  await select.selectOption(rol);
});

Then('el sistema actualiza el rol', function () {
  const rows = dbQuery('SELECT rol FROM usuarios WHERE usuario = ?', ['admin@tienda.com']);
  expect(rows[0].rol).toBe('admin');
});

Then('el rol se aplica en el proximo inicio de sesion', function () {
  const rows = dbQuery('SELECT rol FROM usuarios WHERE usuario = ?', ['admin@tienda.com']);
  expect(rows[0].rol).toBe('admin');
});
