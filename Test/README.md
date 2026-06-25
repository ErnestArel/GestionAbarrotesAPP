# Pruebas unitarias con PHPUnit

Esta carpeta contiene una suite standalone para los 30 casos solicitados desde `Diseño de Test Cases (1).xlsx`.

## Ejecutar

Desde la carpeta `Test`:

```bash
C:\xampp\php\php.exe tools\phpunit.phar --configuration phpunit.xml
```

O desde la raiz del proyecto:

```bash
C:\xampp\php\php.exe Test\tools\phpunit.phar --configuration Test\phpunit.xml
```

El proyecto no trae `composer.json`, por eso se usa `Test/tools/phpunit.phar` localmente. El workflow de GitHub Actions descarga PHPUnit 9 durante la ejecucion. Las pruebas usan dobles de base de datos en memoria; no se conectan a la base de datos real.

## Casos cubiertos

Incluidos como asserts reales:

- CP-001, CP-008, CP-013, CP-018, CP-019, CP-021, CP-023
- CP-033, CP-036, CP-040
- CP-045, CP-049, CP-050, CP-053, CP-055

Incluidos como `SKIP` porque el modulo requerido no existe en el codigo actual:

- Sesion/auditoria via controlador con `headers` y `exit`: CP-006, CP-085, CP-086
- Categorias: CP-029, CP-030, CP-076, CP-077
- Movimientos de stock: CP-025, CP-058, CP-084
- Alertas por vencimiento: CP-026, CP-028
- Reactivacion de proveedor: CP-043, CP-044

CP-037 queda como assert de requisito del Excel: espera eliminacion fisica de proveedor. El codigo actual hace baja logica (`UPDATE proveedores SET estado = 0`), por lo que esa prueba debe fallar hasta alinear requisito o implementacion.

## Ejecucion en Actions

Las pruebas se ejecutan automaticamente al hacer push a main 