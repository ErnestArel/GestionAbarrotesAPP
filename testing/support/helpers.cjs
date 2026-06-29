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
  throw new Error(reason);
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

function phpValue(code) {
  const result = spawnSync(process.env.PHP_BINARY || 'php', ['-r', code], {
    env: { ...process.env, ...dbEnv },
    encoding: 'utf8'
  });

  if (result.status !== 0) {
    throw new Error((result.stderr || result.stdout || 'No se pudo ejecutar PHP').trim());
  }

  return result.stdout.trim();
}

async function clickByText(page, text) {
  const aliases = {
    'Iniciar sesion': ['Ingresar al Sistema', 'Iniciar sesion'],
    'Nuevo producto': ['Nuevo Producto', 'Nuevo producto'],
    'Nuevo proveedor': ['Nuevo Proveedor', 'Nuevo proveedor'],
    'Nuevo usuario': ['Nuevo Usuario', 'Nuevo usuario'],
    'Guardar cambios': ['Actualizar Producto', 'Actualizar Proveedor', 'Actualizar', 'Guardar cambios'],
    Guardar: ['Guardar', 'Guardar Producto', 'Guardar Proveedor', 'Guardar Usuario'],
    'Cerrar sesion': ['Cerrar Sesion', 'Cerrar Sesión'],
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

    const allClickables = page.locator('a, button, input[type="submit"]');
    const total = await allClickables.count();
    for (let index = 0; index < total; index += 1) {
      const item = allClickables.nth(index);
      const rawText = await item.evaluate((el) => el.innerText || el.value || el.textContent || '');
      if (normalize(rawText).includes(normalize(label))) {
        await item.click();
        return;
      }
    }
  }

  if (text === 'Guardar' || text === 'Guardar cambios') {
    const primaryButton = page.locator('button.btn-primary, input.btn-primary, .btn-primary').last();
    if (await primaryButton.count()) {
      await primaryButton.click();
      return;
    }

    const submit = page.locator('button[type="submit"], input[type="submit"]').last();
    if (await submit.count()) {
      await submit.click();
      return;
    }

    const form = page.locator('form').last();
    if (await form.count()) {
      await form.evaluate((currentForm) => {
        if (typeof currentForm.requestSubmit === 'function') {
          currentForm.requestSubmit();
        } else {
          currentForm.submit();
        }
      });
      return;
    }
  }

  if (text === 'Editar') {
    const edit = page.locator('a.btn-edit, .btn-edit').first();
    if (await edit.count()) {
      await edit.click();
      return;
    }
  }

  if (text === 'Eliminar') {
    const del = page.locator('a.btn-delete, .btn-delete').first();
    if (await del.count()) {
      await del.click();
      return;
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

function ensureProduct() {
  const rows = dbQuery('SELECT id FROM productos WHERE codigo = ?', ['PROD-001']);
  if (!rows.length) {
    dbQuery(
      'INSERT INTO productos (codigo, nombre, categoria, precio_compra, precio_venta, stock, stock_minimo, proveedor_id, fecha_vencimiento, estado) VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, 1)',
      ['PROD-001', 'Arroz Costeno 1kg', 'Granos', '2.50', '3.80', 100, 20, '2026-12-31']
    );
  }
}

function ensureProvider() {
  const rows = dbQuery('SELECT id FROM proveedores WHERE ruc = ?', ['20601234567']);
  if (!rows.length) {
    dbQuery(
      'INSERT INTO proveedores (ruc, razon_social, contacto, telefono, email, direccion, estado) VALUES (?, ?, ?, ?, ?, ?, 1)',
      ['20601234567', 'Distribuidora XYZ', 'Juan Perez', '987654321', 'ventas@xyz.com', 'Av. Principal 123']
    );
  }
}

function ensureUser() {
  const rows = dbQuery('SELECT id FROM usuarios WHERE usuario = ?', ['juan@tienda.com']);
  if (!rows.length) {
    const hash = phpValue(`echo password_hash(${JSON.stringify('Admin123')}, PASSWORD_DEFAULT);`);
    dbQuery(
      'INSERT INTO usuarios (usuario, clave, nombre_completo, email, rol, estado) VALUES (?, ?, ?, ?, ?, 1)',
      ['juan@tienda.com', hash, 'Juan Perez', 'juan@tienda.com', 'admin']
    );
  }
}

module.exports = {
  clickByText,
  dbQuery,
  ensureProduct,
  ensureProvider,
  ensureUser,
  expectBodyContains,
  fillForm,
  loginAsAdmin,
  phpValue,
  unsupported
};
