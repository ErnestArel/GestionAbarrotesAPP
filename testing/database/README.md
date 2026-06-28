# Base de datos de pruebas funcionales

Las pruebas funcionales usan por defecto:

- Host: `127.0.0.1`
- Base: `gestion_abarrotes_test`
- Usuario: `root`
- Password: vacio

Crear y sembrar la base:

```powershell
mysql -u root -p < testing\database\schema.sql
mysql -u root -p gestion_abarrotes_test < testing\database\seed.sql
```

Si tu MySQL no usa `root` sin password, ejecuta las pruebas con variables:

```powershell
$env:DB_HOST="127.0.0.1"
$env:DB_NAME="gestion_abarrotes_test"
$env:DB_USER="tu_usuario"
$env:DB_PASS="tu_password"
npm.cmd run test:functional
```

Credenciales sembradas:

- `admin` / `password123`
- `admin@tienda.com` / `Admin123`
