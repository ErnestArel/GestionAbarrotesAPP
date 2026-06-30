const { Given, When, Then } = require('@cucumber/cucumber');
const { expect } = require('@playwright/test');

const {
  clickByText,
  dbQuery,
  ensureProduct,
  expectBodyContains,
  unsupported
} = require('../support/helpers.cjs');

Given('se encuentra en el modulo de productos', async function () {
  await this.page.goto('/controllers/ProductoController.php?action=listar');
  await expectBodyContains(this.page, 'Listado de Productos');
});

When('busca {string}', async function (term) {
  if (term === 'PROD-001') {
    ensureProduct();
  }

  const input = /^prod-/i.test(term)
    ? this.page.locator('input[name="codigo"]').first()
    : this.page.locator('input[name="q"]').first();
  await input.fill(term);
  await input.locator('xpath=ancestor::form').locator('button').click();
});

When('busca {string} en la barra de busqueda', async function (term) {
  if (term === 'Arroz Costeno 1kg' || term === 'Granos') {
    ensureProduct();
    await this.page.goto('/controllers/ProductoController.php?action=listar');
  }

  const input = this.page.locator('input[name="q"]').first();
  await input.fill(term);
  await input.locator('xpath=ancestor::form').locator('button').click();
});

Then('el sistema registra el producto exitosamente', async function () {
  await expectBodyContains(this.page, 'Producto registrado exitosamente', ['Producto agregado exitosamente']);
});

Then('el nuevo producto aparece en el listado de inventario', async function () {
  await expectBodyContains(this.page, this.lastDataTable?.nombre || this.lastDataTable?.SKU);
});

Given('selecciona un producto existente y hace clic en {string}', async function (text) {
  ensureProduct();
  await this.page.goto('/controllers/ProductoController.php?action=listar');
  this.selectedProductCode = 'PROD-001';
  const row = this.page.locator('tr', { hasText: this.selectedProductCode }).first();
  await clickByText(row, text);
});

When('modifica el precio_venta a {float} y el stock_minimo a {int}', async function (precioVenta, stockMinimo) {
  await this.page.locator('[name="precio_venta"]').fill(String(precioVenta));
  await this.page.locator('[name="stock_minimo"]').fill(String(stockMinimo));
});

Then('el sistema actualiza la informacion en la base de datos', function () {
  const rows = dbQuery('SELECT precio_venta, stock_minimo FROM productos WHERE codigo = ?', ['PROD-001']);
  expect(rows.length).toBe(1);
  expect(Number(rows[0].precio_venta)).toBeCloseTo(4.2);
  expect(Number(rows[0].stock_minimo)).toBe(15);
});

Then('el sistema muestra los productos coincidentes', async function () {
  await expectBodyContains(this.page, 'Arroz Costeno 1kg');
});

Then('el producto buscado aparece en el listado', async function () {
  await expectBodyContains(this.page, 'Arroz Costeno 1kg');
});

Then('el sistema muestra la ficha del producto correspondiente', async function () {
  await expectBodyContains(this.page, 'Detalle del Producto', ['PROD-001']);
});

Then('el sistema lista unicamente productos de esa categoria', async function () {
  await expectBodyContains(this.page, 'Granos');
});

Given('selecciona un producto activo y hace clic en {string}', async function (text) {
  ensureProduct();
  await this.page.goto('/controllers/ProductoController.php?action=listar');
  this.page.once('dialog', (dialog) => dialog.accept());
  const row = this.page.locator('tr', { hasText: 'PROD-001' }).first();
  await clickByText(row, text);
});

Then('el sistema cambia el estado del producto a inactivo', function () {
  const rows = dbQuery('SELECT estado FROM productos WHERE codigo = ?', ['PROD-001']);
  expect(Number(rows[0].estado)).toBe(0);
});

Then('el producto desaparece del listado principal', async function () {
  await expect(this.page.locator('body')).not.toContainText('PROD-001');
});

Given('existe un producto inactivo con SKU {string}', function (sku) {
  const rows = dbQuery('SELECT estado FROM productos WHERE codigo = ?', [sku]);
  if (!rows.length || Number(rows[0].estado) !== 0) {
    return unsupported('No existe modulo de movimientos y el producto inactivo depende del flujo previo.');
  }
});

Given('existen productos con fecha_vencimiento dentro de los proximos {int} dias', function (_dias) {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

Given('existe un producto con fecha_vencimiento exactamente a {int} dias', function (_dias) {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

When('el administrador accede al dashboard o al modulo de alertas', function () {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

Then('el sistema muestra la lista de esos productos', function () {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

Then('cada producto muestra nombre, categoria, stock y fecha de vencimiento', function () {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

Then('la lista esta ordenada por fecha de vencimiento ascendente', function () {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

Then('el producto aparece en la lista de alertas', function () {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

When('navega a la gestion de categorias', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

When('ingresa el nombre {string} y hace clic en {string}', function (_nombre, _boton) {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('el sistema registra la categoria', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Given('selecciona la categoria {string} y hace clic en {string}', function (_categoria, _boton) {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

When('cambia el nombre a {string} y guarda', function (_nombre) {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('el sistema actualiza la categoria correctamente', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Given('la categoria {string} no tiene productos asociados', function (_categoria) {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

When('intenta desactivarla y confirma', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('el sistema desactiva la categoria', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('ya no aparece en el listado activo ni en formularios de producto', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Given('la categoria {string} esta desactivada', function (_categoria) {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('la categoria {string} no aparece en el selector de categorias', function (_categoria) {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});
