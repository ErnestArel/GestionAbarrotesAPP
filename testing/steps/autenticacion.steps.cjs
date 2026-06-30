const { Given, When, Then } = require('@cucumber/cucumber');
const { expect } = require('@playwright/test');

const { loginAsAdmin } = require('../support/helpers.cjs');

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

When('ingresa el correo {string} y la contrasena {string}', async function (usuario, clave) {
  await this.page.locator('input[name="usuario"]').fill(usuario);
  await this.page.locator('input[name="clave"]').fill(clave);
});

Then('el sistema valida las credenciales', async function () {
  await expect(this.page).toHaveURL(/dashboard\.php/);
});

Then('redirige al dashboard', async function () {
  await expect(this.page).toHaveURL(/dashboard\.php/);
});

Then('muestra un mensaje de bienvenida', async function () {
  await expect(this.page.locator('body')).toContainText(/Bienvenido/i);
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
