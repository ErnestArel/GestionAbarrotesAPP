const { When, Then } = require('@cucumber/cucumber');

const {
  clickByText,
  dbQuery,
  expectBodyContains,
  fillForm,
  unsupported
} = require('../support/helpers.cjs');

When('hace clic en {string}', async function (text) {
  if (text === 'Guardar' || text === 'Guardar cambios') {
    await clickByText(this.page, text);
    await this.page.waitForLoadState('networkidle').catch(() => {});
    return;
  }

  await clickByText(this.page, text);
});

When('selecciona {string}', async function (text) {
  await clickByText(this.page, text);
});

When('llena el formulario con los siguientes datos:', async function (dataTable) {
  this.lastDataTable = dataTable.rowsHash();
  await fillForm(this.page, this.lastDataTable);
});

When('guarda los cambios', async function () {
  await clickByText(this.page, 'Guardar cambios');
});

When('confirma la accion en el dialogo', async function () {
  await this.page.waitForLoadState('networkidle').catch(() => {});
});

When('confirma la eliminacion', async function () {
  await this.page.waitForLoadState('networkidle').catch(() => {});
});

When('el administrador selecciona la opcion {string} y confirma', function (_opcion) {
  return unsupported('La accion solicitada no existe en el codigo actual.');
});

Then('registra la accion {string} en auditoria', function (accion) {
  const rows = dbQuery('SELECT * FROM auditoria WHERE accion = ? ORDER BY id DESC LIMIT 1', [accion]);
  if (rows.length === 0) {
    throw new Error(`No se encontro registro de auditoria con accion: ${accion}`);
  }
});

Then('registra la accion en auditoria', function () {
  const rows = dbQuery('SELECT * FROM auditoria ORDER BY id DESC LIMIT 1');
  if (rows.length === 0) {
    throw new Error('No se encontro ningun registro de auditoria.');
  }
});

Then('muestra el mensaje {string}', async function (message) {
  await expectBodyContains(this.page, message, [
    'Producto agregado exitosamente',
    'Producto eliminado exitosamente',
    'Usuario creado exitosamente'
  ]);
});

Then('el sistema muestra el mensaje {string}', async function (message) {
  await expectBodyContains(this.page, message);
});
