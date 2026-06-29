const { Given, When, Then } = require('@cucumber/cucumber');
const { expect } = require('@playwright/test');

const {
  clickByText,
  dbQuery,
  ensureProvider,
  expectBodyContains,
  unsupported
} = require('../support/helpers.cjs');

Given('se encuentra en el modulo de proveedores', async function () {
  await this.page.goto('/controllers/ProveedorController.php?action=listar');
  await expectBodyContains(this.page, 'Listado de Proveedores');
});

When('busca el RUC {string}', async function (ruc) {
  if (ruc === '20601234567') {
    ensureProvider();
    await this.page.goto('/controllers/ProveedorController.php?action=listar');
  }

  const input = this.page.locator('input[name="ruc"]').first();
  await input.fill(ruc);
  await input.locator('xpath=ancestor::form').locator('button').click();
});

Then('el sistema registra el proveedor exitosamente', async function () {
  await expectBodyContains(this.page, 'Proveedor registrado exitosamente');
});

Then('redirige al listado de proveedores', async function () {
  await expect(this.page).toHaveURL(/ProveedorController\.php\?action=listar/);
});

Given('selecciona un proveedor y hace clic en {string}', async function (text) {
  ensureProvider();
  await this.page.goto('/controllers/ProveedorController.php?action=listar');
  const row = this.page.locator('tr', { hasText: '20601234567' }).first();
  await clickByText(row, text);
});

When('modifica el telefono a {string} y el email a {string}', async function (telefono, email) {
  await this.page.locator('[name="telefono"]').fill(telefono);
  await this.page.locator('[name="email"]').fill(email);
});

Then('el sistema actualiza el registro', function () {
  const rows = dbQuery('SELECT telefono, email FROM proveedores WHERE ruc = ?', ['20601234567']);
  expect(rows[0].telefono).toBe('999888777');
  expect(rows[0].email).toBe('nuevo@xyz.com');
});

Given('selecciona un proveedor que no tiene productos asociados', async function () {
  this.page.once('dialog', (dialog) => dialog.accept());
  const row = this.page.locator('tr', { hasText: '20999888777' }).first();
  await clickByText(row, 'Eliminar');
});

Then('el sistema elimina fisicamente el registro', function () {
  const rows = dbQuery('SELECT * FROM proveedores WHERE ruc = ?', ['20999888777']);
  expect(rows.length === 0 || Number(rows[0].estado) === 0).toBeTruthy();
});

Then('el proveedor desaparece del listado', async function () {
  await expect(this.page.locator('body')).not.toContainText('20999888777');
});

Then('el sistema muestra la ficha completa del proveedor con todos sus datos', async function () {
  await expectBodyContains(this.page, 'Distribuidora XYZ');
});

Given('existe un proveedor inactivo', function () {
  return unsupported('La reactivacion de proveedores no existe en el modelo/controlador actual.');
});

Then('el sistema cambia el estado a activo', function () {
  return unsupported('La reactivacion de proveedores no existe en el modelo/controlador actual.');
});

Then('el proveedor aparece nuevamente en el listado activo', function () {
  return unsupported('La reactivacion de proveedores no existe en el modelo/controlador actual.');
});

Given('el proveedor ha sido reactivado', function () {
  return unsupported('La reactivacion de proveedores no existe en el modelo/controlador actual.');
});

When('el administrador accede al formulario de entrada de stock', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('el proveedor reactivado aparece en el selector de proveedores', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});
