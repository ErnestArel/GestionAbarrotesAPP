const { BeforeAll, AfterAll, Before, After, Status } = require('@cucumber/cucumber');
const { chromium } = require('playwright');
const { spawn, spawnSync } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');

let browser;
let phpServer;

const rootDir = path.resolve(__dirname, '..', '..');
const runtimeDir = path.join(rootDir, 'testing', '.runtime', 'app');
const baseUrl = process.env.BASE_URL || 'http://127.0.0.1:8080/tiendaAbarrotes';
const phpBinary = process.env.PHP_BINARY || 'php';

const testDbEnv = {
  DB_HOST: process.env.DB_HOST || '127.0.0.1',
  DB_NAME: process.env.DB_NAME || 'gestion_abarrotes_test',
  DB_USER: process.env.DB_USER || 'root',
  DB_PASS: process.env.DB_PASS || 'root'
};

function runPhp(code, options = {}) {
  const result = spawnSync(phpBinary, ['-r', code], {
    cwd: rootDir,
    env: { ...process.env, ...testDbEnv },
    encoding: 'utf8',
    ...options
  });

  if (result.status !== 0) {
    throw new Error((result.stderr || result.stdout || 'PHP command failed').trim());
  }

  return result.stdout;
}

function resetTestDatabase() {
  const schemaPath = path.join(rootDir, 'testing', 'database', 'schema.sql').replace(/\\/g, '/');
  const seedPath = path.join(rootDir, 'testing', 'database', 'seed.sql').replace(/\\/g, '/');

  runPhp(`
    $pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';charset=utf8mb4', getenv('DB_USER'), getenv('DB_PASS'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec(file_get_contents('${schemaPath}'));
    $pdo->exec(file_get_contents('${seedPath}'));
  `);
}

function prepareRuntimeApp() {
  fs.rmSync(runtimeDir, { recursive: true, force: true });
  fs.mkdirSync(runtimeDir, { recursive: true });

  const ignored = new Set(['.git', 'node_modules', 'vendor', 'testing', 'build']);
  for (const item of fs.readdirSync(rootDir, { withFileTypes: true })) {
    if (ignored.has(item.name)) {
      continue;
    }

    const source = path.join(rootDir, item.name);
    const target = path.join(runtimeDir, item.name);
    fs.cpSync(source, target, { recursive: true });
  }

  const runtimeTestingDir = path.join(runtimeDir, 'testing');
  fs.mkdirSync(runtimeTestingDir, { recursive: true });
  fs.cpSync(path.join(rootDir, 'testing', 'server'), path.join(runtimeTestingDir, 'server'), { recursive: true });

  const databasePath = path.join(runtimeDir, 'config', 'Database.php');
  const patchedDatabase = `<?php
class Database {
    private static $instance = null;
    private $conexion;

    private $host;
    private $usuario;
    private $clave;
    private $base;

    private function __construct() {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->usuario = getenv('DB_USER') ?: 'root';
        $this->clave = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'root';
        $this->base = getenv('DB_NAME') ?: 'gestion_abarrotes_test';

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->base};charset=utf8mb4";
            $this->conexion = new PDO($dsn, $this->usuario, $this->clave);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexion: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function consultar($sql, $params = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta: " . $e->getMessage());
        }
    }

    public function ejecutar($sql, $params = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Error al ejecutar: " . $e->getMessage());
        }
    }

    public function ultimoId() {
        return $this->conexion->lastInsertId();
    }

    private function __clone() {}
    public function __wakeup() {
        throw new Exception("No se puede deserializar un Singleton");
    }
}
?>`;

  fs.writeFileSync(databasePath, patchedDatabase, 'utf8');
}

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
  resetTestDatabase();
  prepareRuntimeApp();

  if (!process.env.BASE_URL) {
    phpServer = spawn(
      phpBinary,
      ['-S', '127.0.0.1:8080', 'testing/server/router.php'],
      {
        cwd: runtimeDir,
        env: { ...process.env, ...testDbEnv },
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
