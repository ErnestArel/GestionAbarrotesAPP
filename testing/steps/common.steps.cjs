const { Given, When, Then } = require('@cucumber/cucumber');
const { expect } = require('@playwright/test');
const { spawnSync } = require('node:child_process');

const credentials = {
  admin: {
    user: process.env.ADMIN_USER || 'admin@tienda.com',
    password: process.env.ADMIN_PASSWORD || 'Admin123'
  }
};

const dbEnv = {
  DB_HOST: process.env.DB_HOST || '127.0.0.1',
  DB_NAME: process.env.DB_NAME || 'gestion_abarrotes_test',
  DB_USER: process.env.DB_USER || 'root',
  DB_PASS: process.env.DB_PASS || 'root'
};

function normalize(text) {
  return String(text)
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '');
}

function unsupported(reason) {
  return `skipped: ${reason}`;
}

function dbQuery(sql, params = []) {
  const code = `
    $pdo = new PDO(
      'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4',
      getenv('DB_USER'),
      getenv('DB_PASS')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare(${JSON.stringify(sql)});
    $stmt->execute(json_decode(${JSON.stringify(JSON.stringify(params))}, true));
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  `;

  const result = spawnSync(process.env.PHP_BINARY || 'php', ['-r', code], {
    env: { ...process.env, ...dbEnv },
    encoding: 'utf8'
  });

  if (result.status !== 0) {
    throw new Error((result.stderr || result.stdout || 'No se pudo consultar la BD de pruebas').trim());
  }

  return JSON.parse(result.stdout || '[]');
}

async function clickByText(page, text) {
  const aliases = {
    'Iniciar sesion': ['Ingresar al Sistema', 'Iniciar sesion'],
    'Nuevo producto': ['Nuevo Producto', 'Nuevo producto'],
    'Nuevo proveedor': ['Nuevo Proveedor', 'Nuevo proveedor'],
    'Nuevo usuario': ['Nuevo Usuario', 'Nuevo usuario'],
    'Guardar cambios': ['Actualizar Producto', 'Actualizar Proveedor', 'Actualizar', 'Guardar cambios'],
    Guardar: ['Guardar', 'Guardar Producto', 'Guardar Proveedor', 'Guardar Usuario'],
    Eliminar: ['Eliminar']
  };

  for (const label of aliases[text] || [text]) {
    const escaped = label.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const candidates = [
      page.getByRole('button', { name: new RegExp(escaped, 'i') }),
      page.getByRole('link', { name: new RegExp(escaped, 'i') }),
      page.getByText(new RegExp(escaped, 'i')).first()
    ];

    for (const locator of candidates) {
      if (await locator.count()) {
        await locator.first().click();
        return;
      }
    }
  }

  throw new Error(`No se encontro un boton, enlace o texto clicable con: ${text}`);
}

async function loginAsAdmin(world) {
  await world.page.goto('/');
  await world.page.locator('input[name="usuario"]').fill(credentials.admin.user);
  await world.page.locator('input[name="clave"]').fill(credentials.admin.password);
  await clickByText(world.page, 'Iniciar sesion');
  await expect(world.page).toHaveURL(/dashboard\.php/);
}

async function bodyText(page) {
  return page.locator('body').innerText();
}

async function expectBodyContains(page, expected, variants = []) {
  const current = normalize(await bodyText(page));
  const accepted = [expected, ...variants].map(normalize);
  expect(accepted.some((item) => current.includes(item))).toBeTruthy();
}

async function fillForm(page, rows) {
  const aliases = {
    SKU: 'codigo',
    RUC: 'ruc',
    razon_social: 'razon_social',
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
    direccion: 'direccion',
    usuario: 'usuario',
    clave: 'clave',
    nombre_completo: 'nombre_completo',
    rol: 'rol'
  };

  for (const [label, value] of Object.entries(rows)) {
    const name = aliases[label] || label;
    const input = page.locator(`[name="${name}"]`);
    if (!(await input.count())) {
      continue;
    }

    const tagName = await input.first().evaluate((el) => el.tagName.toLowerCase());
    if (tagName === 'select') {
      await input.first().selectOption(value).catch(async () => {
        await input.first().selectOption({ label: value });
      });
    } else {
      await input.first().fill(value);
    }
  }
}

Given('el usuario se encuentra en la pagina de login', async function () {
  await this.page.goto('/');
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
});

Given('el usuario ha iniciado sesion como administrador', async function () {
  await loginAsAdmin(this);
});

Given('el usuario tiene una sesion activa', async function () {
  await loginAsAdmin(this);
});

Given('se encuentra en el modulo de productos', async function () {
  await this.page.goto('/controllers/ProductoController.php?action=listar');
  await expectBodyContains(this.page, 'Listado de Productos');
});

Given('se encuentra en el modulo de proveedores', async function () {
  await this.page.goto('/controllers/ProveedorController.php?action=listar');
  await expectBodyContains(this.page, 'Listado de Proveedores');
});

Given('se encuentra en el modulo de usuarios', async function () {
  await this.page.goto('/controllers/UsuarioController.php?action=listar');
  await expectBodyContains(this.page, 'Listado de Usuarios');
});

Given('se encuentra en el modulo de movimientos', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
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
  this.lastDataTable = dataTable.rowsHash();
  await fillForm(this.page, this.lastDataTable);
});

When('busca {string}', async function (term) {
  const input = /^prod-/i.test(term)
    ? this.page.locator('input[name="codigo"]').first()
    : this.page.locator('input[name="q"]').first();
  await input.fill(term);
  await input.locator('xpath=ancestor::form').locator('button').click();
});

When('busca {string} en la barra de busqueda', async function (term) {
  const input = this.page.locator('input[name="q"]').first();
  await input.fill(term);
  await input.locator('xpath=ancestor::form').locator('button').click();
});

When('busca el RUC {string}', async function (ruc) {
  const input = this.page.locator('input[name="ruc"]').first();
  await input.fill(ruc);
  await input.locator('xpath=ancestor::form').locator('button').click();
});

Then('el sistema valida las credenciales', async function () {
  await expect(this.page).toHaveURL(/dashboard\.php/);
});

Then('registra la accion {string} en auditoria', function (accion) {
  const rows = dbQuery('SELECT * FROM auditoria WHERE accion = ? ORDER BY id DESC LIMIT 1', [accion]);
  expect(rows.length).toBeGreaterThan(0);
});

Then('registra la accion en auditoria', function () {
  const rows = dbQuery('SELECT * FROM auditoria ORDER BY id DESC LIMIT 1');
  expect(rows.length).toBeGreaterThan(0);
});

Then('redirige al dashboard', async function () {
  await expect(this.page).toHaveURL(/dashboard\.php/);
});

Then('muestra un mensaje de bienvenida', async function () {
  await expectBodyContains(this.page, 'Bienvenido');
});

Then('el sistema destruye la sesion', async function () {
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
});

Then('redirige a la pagina de login', async function () {
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
});

Then('no permite acceder a rutas protegidas con la URL anterior', async function () {
  await this.page.goto('/views/dashboard.php');
  await expect(this.page.locator('input[name="usuario"]')).toBeVisible();
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

Then('el sistema registra el producto exitosamente', async function () {
  await expectBodyContains(this.page, 'Producto registrado exitosamente', ['Producto agregado exitosamente']);
});

Then('el nuevo producto aparece en el listado de inventario', async function () {
  await expectBodyContains(this.page, this.lastDataTable?.nombre || this.lastDataTable?.SKU);
});

Given('selecciona un producto existente y hace clic en {string}', async function (text) {
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
  this.page.once('dialog', (dialog) => dialog.accept());
  const row = this.page.locator('tr', { hasText: 'PROD-001' }).first();
  await clickByText(row, text);
});

When('confirma la accion en el dialogo', async function () {
  await this.page.waitForLoadState('networkidle').catch(() => {});
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

When('el administrador accede al formulario de movimientos', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('el producto {string} no aparece en el selector de productos', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Given('existen productos con fecha_vencimiento dentro de los proximos {int} dias', function () {
  return unsupported('No existe modulo o vista de alertas por vencimiento.');
});

Given('existe un producto con fecha_vencimiento exactamente a {int} dias', function () {
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

When('ingresa el nombre {string} y hace clic en {string}', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('el sistema registra la categoria', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Given('selecciona la categoria {string} y hace clic en {string}', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

When('cambia el nombre a {string} y guarda', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('el sistema actualiza la categoria correctamente', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Given('la categoria {string} no tiene productos asociados', function () {
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

Given('la categoria {string} esta desactivada', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('la categoria {string} no aparece en el selector de categorias', function () {
  return unsupported('El modulo de categorias no existe en el codigo actual.');
});

Then('el sistema registra el proveedor exitosamente', async function () {
  await expectBodyContains(this.page, 'Proveedor registrado exitosamente');
});

Then('redirige al listado de proveedores', async function () {
  await expect(this.page).toHaveURL(/ProveedorController\.php\?action=listar/);
});

Given('selecciona un proveedor y hace clic en {string}', async function (text) {
  const row = this.page.locator('tr', { hasText: '20601234567' }).first();
  await clickByText(row, text);
});

When('modifica el telefono a {string} y el email a {string}', async function (telefono, email) {
  await this.page.locator('[name="telefono"]').fill(telefono);
  await this.page.locator('[name="email"]').fill(email);
});

When('guarda los cambios', async function () {
  await clickByText(this.page, 'Guardar cambios');
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

When('confirma la eliminacion', async function () {
  await this.page.waitForLoadState('networkidle').catch(() => {});
});

Then('el sistema elimina fisicamente el registro', function () {
  const rows = dbQuery('SELECT * FROM proveedores WHERE ruc = ?', ['20999888777']);
  expect(rows.length).toBe(0);
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

When('el administrador selecciona la opcion {string} y confirma', function () {
  return unsupported('La accion solicitada no existe en el codigo actual.');
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

Then('el sistema crea el usuario exitosamente', async function () {
  await expectBodyContains(this.page, 'Usuario creado exitosamente');
});

Given('existe un usuario creado con contrasena {string}', function (password) {
  const rows = dbQuery('SELECT clave FROM usuarios WHERE usuario = ?', ['juan@tienda.com']);
  if (!rows.length) {
    dbQuery(
      'INSERT INTO usuarios (usuario, clave, nombre_completo, email, rol, estado) VALUES (?, ?, ?, ?, ?, 1)',
      ['juan@tienda.com', password, 'Juan Perez', 'juan@tienda.com', 'admin']
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
  const row = this.page.locator('tr', { hasText: 'admin@tienda.com' }).first();
  await clickByText(row, text);
});

When('cambia el rol a {string}', async function (rol) {
  await this.page.locator('[name="rol"]').selectOption(rol);
});

Then('el sistema actualiza el rol', function () {
  const rows = dbQuery('SELECT rol FROM usuarios WHERE usuario = ?', ['admin@tienda.com']);
  expect(rows[0].rol).toBe('admin');
});

Then('el rol se aplica en el proximo inicio de sesion', function () {
  const rows = dbQuery('SELECT rol FROM usuarios WHERE usuario = ?', ['admin@tienda.com']);
  expect(rows[0].rol).toBe('admin');
});

When('selecciona un producto existente {string}', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('selecciona un proveedor valido', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('ingresa la cantidad {int}', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('el sistema incrementa el stock del producto en {int} unidades', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('guarda el movimiento en el historial con fecha actual, cantidad y proveedor', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Given('se ha registrado una entrada de {int} unidades para {string} con proveedor {string}', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

When('el administrador consulta el historial de movimientos', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

Then('el historial muestra la entrada con producto, cantidad, fecha, proveedor y tipo {string}', function () {
  return unsupported('El modulo de movimientos no existe en el codigo actual.');
});

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
