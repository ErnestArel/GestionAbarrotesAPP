const { Given, When, Then } = require('@cucumber/cucumber');

const { unsupported } = require('../support/helpers.cjs');

Given('se encuentra en el modulo de movimientos', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('el administrador accede al formulario de movimientos', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('el producto {string} no aparece en el selector de productos', function (_sku) {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('selecciona un producto existente {string}', function (_producto) {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('selecciona un proveedor valido', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('ingresa la cantidad {int}', function (_cantidad) {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('el sistema incrementa el stock del producto en {int} unidades', function (_cantidad) {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('guarda el movimiento en el historial con fecha actual, cantidad y proveedor', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Given('se ha registrado una entrada de {int} unidades para {string} con proveedor {string}', function (_cantidad, _producto, _proveedor) {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('el administrador consulta el historial de movimientos', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('el historial muestra la entrada con producto, cantidad, fecha, proveedor y tipo {string}', function (_tipo) {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});
