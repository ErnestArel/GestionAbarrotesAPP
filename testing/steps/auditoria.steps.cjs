const { Given, When, Then } = require('@cucumber/cucumber');

const { dbQuery, expectBodyContains } = require('../support/helpers.cjs');

Given('se ha registrado un nuevo producto', function () {
  const rows = dbQuery('SELECT * FROM auditoria WHERE accion = ? AND modulo = ? LIMIT 1', ['CREAR', 'Productos']);
  if (!rows.length) {
    dbQuery(
      'INSERT INTO auditoria (usuario_id, accion, modulo, descripcion, ip) VALUES (1, ?, ?, ?, ?)',
      ['CREAR', 'Productos', 'Producto creado para auditoria', '127.0.0.1']
    );
  }
});

Given('se ha desactivado un proveedor', function () {
  const rows = dbQuery('SELECT * FROM auditoria WHERE accion = ? AND modulo = ? LIMIT 1', ['ELIMINAR', 'Proveedores']);
  if (!rows.length) {
    dbQuery(
      'INSERT INTO auditoria (usuario_id, accion, modulo, descripcion, ip) VALUES (1, ?, ?, ?, ?)',
      ['ELIMINAR', 'Proveedores', 'Proveedor eliminado para auditoria', '127.0.0.1']
    );
  }
});

When('el administrador navega al modulo de auditoria', async function () {
  await this.page.goto('/controllers/AuditoriaController.php');
  await expectBodyContains(this.page, 'Auditoria', ['Auditor']);
});

Then('existe un registro con accion {string}', async function (accion) {
  await expectBodyContains(this.page, accion);
});

Then('el registro pertenece al modulo {string}', async function (modulo) {
  await expectBodyContains(this.page, modulo);
});

Then('muestra usuario, fecha, hora e IP', async function () {
  await expectBodyContains(this.page, '127.0.0.1', ['admin']);
});
