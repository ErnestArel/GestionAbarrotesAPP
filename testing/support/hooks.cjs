const { BeforeAll, AfterAll, Before, After, Status } = require('@cucumber/cucumber');
const { chromium } = require('playwright');
const { spawn } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');

let browser;
let phpServer;

const rootDir = path.resolve(__dirname, '..', '..');
const baseUrl = process.env.BASE_URL || 'http://127.0.0.1:8080/tiendaAbarrotes';
const phpBinary = process.env.PHP_BINARY || 'php';

async function waitForServer(url, timeoutMs = 15000) {
  const startedAt = Date.now();

  while (Date.now() - startedAt < timeoutMs) {
    try {
      const response = await fetch(url);
      if (response.status < 500) {
        return;
      }
    } catch (_error) {
      await new Promise((resolve) => setTimeout(resolve, 250));
    }
  }

  throw new Error(`No se pudo iniciar la app en ${url}`);
}

BeforeAll(async function () {
  fs.mkdirSync(path.join(rootDir, 'testing', 'reports'), { recursive: true });

  if (!process.env.BASE_URL) {
    phpServer = spawn(
      phpBinary,
      ['-S', '127.0.0.1:8080', 'testing/server/router.php'],
      {
        cwd: rootDir,
        env: process.env,
        stdio: ['ignore', 'pipe', 'pipe']
      }
    );

    phpServer.stdout.on('data', (data) => process.stdout.write(`[php] ${data}`));
    phpServer.stderr.on('data', (data) => process.stderr.write(`[php] ${data}`));

    await waitForServer(baseUrl);
  }

  browser = await chromium.launch({
    headless: process.env.PLAYWRIGHT_HEADLESS !== 'false'
  });
});

AfterAll(async function () {
  if (browser) {
    await browser.close();
  }

  if (phpServer) {
    phpServer.kill();
  }
});

Before(async function () {
  this.browser = browser;
  this.context = await browser.newContext({
    baseURL: this.baseUrl,
    viewport: { width: 1366, height: 768 },
    ignoreHTTPSErrors: true
  });
  this.page = await this.context.newPage();
});

After(async function (scenario) {
  if (scenario.result?.status === Status.FAILED && this.page) {
    const safeName = scenario.pickle.name.replace(/[^a-z0-9]+/gi, '-').toLowerCase();
    await this.page.screenshot({
      path: path.join(rootDir, 'testing', 'reports', `${safeName}.png`),
      fullPage: true
    });
  }

  if (this.context) {
    await this.context.close();
  }
});
