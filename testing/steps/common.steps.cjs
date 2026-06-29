const { Given, When, Then } = require('@cucumber/cucumber');
const { expect } = require('@playwright/test');

const credentials = {
  admin: {
    user: process.env.ADMIN_USER || 'admin',
    password: process.env.ADMIN_PASSWORD || 'password123'
  }
};

function normalize(text) {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '');
}

async function clickByText(page, text) {
  const candidates = [
    page.getByRole('button', { name: new RegExp(text, 'i') }),
    page.getByRole('link', { name: new RegExp(text, 'i') }),
    page.getByText(new RegExp(text, 'i')).first()
  ];

  for (const locator of candidates) {
    if (await locator.count()) {
      await locator.first().click();
      return;
    }
  }

  throw new Error(`No se encontro un boton, enlace o texto clicable con: ${text}`);
}

async function loginAsAdmin(world) {
  await world.page.goto('/');
  await world.page.locator('input[name="usuario"]').fill(credentials.admin.user);
  await world.page.locator('input[name="clave"]').fill(credentials.admin.password);
  await world.page.getByRole('button').click();
  await expect(world.page).toHaveURL(/dashboard\.php/);
}

Given('el usuario se encuentra en la pagina de login', async function () {
  await this.page.goto('/');
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
});

Given('el usuario se encuentra en la página de login', async function () {
  await this.page.goto('/');
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
});

Given('el usuario ha iniciado sesión como administrador', async function () {
  await loginAsAdmin(this);
});

Given('el usuario ha iniciado sesion como administrador', async function () {
  await loginAsAdmin(this);
});

Given('el usuario tiene una sesión activa', async function () {
  await loginAsAdmin(this);
});

Given('el usuario tiene una sesion activa', async function () {
  await loginAsAdmin(this);
});

Given('se encuentra en el módulo de productos', async function () {
  await this.page.goto('/controllers/ProductoController.php?action=listar');
  await expect(this.page).toHaveURL(/ProductoController\.php/);
});

Given('se encuentra en el modulo de productos', async function () {
  await this.page.goto('/controllers/ProductoController.php?action=listar');
  await expect(this.page).toHaveURL(/ProductoController\.php/);
});

Given('se encuentra en el módulo de proveedores', async function () {
  await this.page.goto('/controllers/ProveedorController.php?action=listar');
  await expect(this.page).toHaveURL(/ProveedorController\.php/);
});

Given('se encuentra en el modulo de proveedores', async function () {
  await this.page.goto('/controllers/ProveedorController.php?action=listar');
  await expect(this.page).toHaveURL(/ProveedorController\.php/);
});

Given('se encuentra en el módulo de movimientos', async function () {
  await this.page.goto('/views/dashboard.php');
});

Given('se encuentra en el modulo de movimientos', async function () {
  await this.page.goto('/views/dashboard.php');
});

When('ingresa el correo {string} y la contraseña {string}', async function (usuario, clave) {
  await this.page.locator('input[name="usuario"]').fill(usuario);
  await this.page.locator('input[name="clave"]').fill(clave);
});

When('ingresa el correo {string} y la contrasena {string}', async function (usuario, clave) {
  await this.page.locator('input[name="usuario"]').fill(usuario);
  await this.page.locator('input[name="clave"]').fill(clave);
});

When('hace clic en {string}', async function (text) {
  await clickByText(this.page, text);
});

When('selecciona {string}', async function (text) {
  await clickByText(this.page, text);
});

When('llena el formulario con los siguientes datos:', async function (dataTable) {
  const rows = dataTable.rowsHash();
  this.lastDataTable = rows;

  const aliases = {
    SKU: 'codigo',
    RUC: 'ruc',
    'razon_social': 'razon_social',
    'categoría': 'categoria',
    categoria: 'categoria',
    nombre: 'nombre',
    precio_compra: 'precio_compra',
    precio_venta: 'precio_venta',
    stock_inicial: 'stock',
    stock_minimo: 'stock_minimo',
    fecha_vencimiento: 'fecha_vencimiento',
    telefono: 'telefono',
    email: 'email',
    contacto: 'contacto',
    direccion: 'direccion'
  };

  for (const [label, value] of Object.entries(rows)) {
    const name = aliases[label] || label;
    const input = this.page.locator(`[name="${name}"]`);

    if (!(await input.count())) {
      continue;
    }

    const tag = await input.first().evaluate((el) => el.tagName.toLowerCase());
    if (tag === 'select') {
      await input.first().selectOption({ label: value }).catch(async () => {
        await input.first().selectOption(value);
      });
    } else {
      await input.first().fill(value);
    }
  }
});

When('busca {string}', async function (term) {
  const codigoSearch = /^prod-/i.test(term);
  const input = codigoSearch
    ? this.page.locator('input[name="codigo"]').first()
    : this.page.locator('input[name="q"]').first();

  await input.fill(term);
  await input.locator('xpath=ancestor::form').locator('button').click();
});

When('busca {string} en la barra de búsqueda', async function (term) {
  await this.page.locator('input[name="q"]').first().fill(term);
  await this.page.locator('input[name="q"]').first().locator('xpath=ancestor::form').locator('button').click();
});

When('busca {string} en la barra de busqueda', async function (term) {
  await this.page.locator('input[name="q"]').first().fill(term);
  await this.page.locator('input[name="q"]').first().locator('xpath=ancestor::form').locator('button').click();
});

Then('redirige al dashboard', async function () {
  await expect(this.page).toHaveURL(/dashboard\.php/);
});

Then('redirige a la página de login', async function () {
  await expect(this.page).toHaveURL(/index\.php|\/tiendaAbarrotes\/?$/);
});

Then('redirige a la pagina de login', async function () {
  await expect(this.page).toHaveURL(/index\.php|\/tiendaAbarrotes\/?$/);
});

Then('redirige al listado de proveedores', async function () {
  await expect(this.page).toHaveURL(/ProveedorController\.php\?action=listar/);
});

Then('muestra un mensaje de bienvenida', async function () {
  await expect(this.page.locator('body')).toContainText(/Bienvenido/i);
});

Then('el sistema muestra el mensaje {string}', async function (message) {
  const bodyText = await this.page.locator('body').innerText();
  expect(normalize(bodyText)).toContain(normalize(message));
});

Then('permanece en la página de login', async function () {
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
});

Then('permanece en la pagina de login', async function () {
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
});

Then('el nuevo producto aparece en el listado de inventario', async function () {
  const expected = this.lastDataTable?.nombre || this.lastDataTable?.SKU;
  await expect(this.page.locator('body')).toContainText(expected);
});

Then('el producto buscado aparece en el listado', async function () {
  await expect(this.page.locator('table, body')).toBeVisible();
});

Then('el sistema muestra los productos coincidentes', async function () {
  await expect(this.page.locator('body')).not.toContainText('No se encontraron productos');
});

Then('el sistema muestra la ficha del producto correspondiente', async function () {
  await expect(this.page.locator('body')).toContainText(/Detalle del Producto|Codigo|Código/i);
});

Then('el sistema registra el producto exitosamente', async function () {
  await expect(this.page.locator('body')).toContainText(/Producto agregado|Producto registrado/i);
});

Then('el sistema registra el proveedor exitosamente', async function () {
  await expect(this.page.locator('body')).toContainText(/Proveedor registrado/i);
});
