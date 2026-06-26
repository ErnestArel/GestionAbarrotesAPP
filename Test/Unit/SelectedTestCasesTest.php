<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
//Prueba
final class SelectedTestCasesTest extends TestCase
{
    private function modelWithFakeDb(string $className, FakeDatabase $db): object
    {
        $ref = new ReflectionClass($className);
        $model = $ref->newInstanceWithoutConstructor();
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($model, $db);

        return $model;
    }

    private function lastExecution(FakeDatabase $db): array
    {
        if (empty($db->ejecuciones)) {
            throw new RuntimeException('Expected at least one database execution.');
        }

        return $db->ejecuciones[count($db->ejecuciones) - 1];
    }

    public function testCp001LoginCredencialesValidas(): void
    {
        $db = new FakeDatabase();
        $db->queueConsultar([[
            'id' => 1,
            'usuario' => 'admin@tienda.com',
            'clave' => password_hash('Admin123', PASSWORD_DEFAULT),
            'nombre_completo' => 'Administrador',
            'rol' => 'admin',
            'estado' => 1,
        ]]);

        /** @var Usuario $usuario */
        $usuario = $this->modelWithFakeDb(Usuario::class, $db);
        $result = $usuario->autenticar('admin@tienda.com', 'Admin123');

        $this->assertSame('admin@tienda.com', $result['usuario']);
    }

    public function testCp008RegistraProductoValido(): void
    {
        $db = new FakeDatabase();
        /** @var Producto $producto */
        $producto = $this->modelWithFakeDb(Producto::class, $db);

        $producto->crear($this->productoValido());

        $exec = $this->lastExecution($db);
        $this->assertSame('PROD-001', $exec['params'][':codigo']);
    }

    public function testCp013ActualizaPrecioProducto(): void
    {
        $db = new FakeDatabase();
        /** @var Producto $producto */
        $producto = $this->modelWithFakeDb(Producto::class, $db);

        $datos = $this->productoValido();
        $datos['precio_venta'] = '5.50';
        $producto->actualizar(10, $datos);

        $exec = $this->lastExecution($db);
        $this->assertSame('5.50', $exec['params'][':precio_venta']);
    }

    public function testCp018BuscaProductoNombre(): void
    {
        $db = new FakeDatabase();
        $db->queueConsultar([['codigo' => 'PROD-001', 'nombre' => 'Arroz Extra']]);
        /** @var Producto $producto */
        $producto = $this->modelWithFakeDb(Producto::class, $db);

        $result = $producto->buscar('Arroz Extra');

        $this->assertSame('Arroz Extra', $result[0]['nombre']);
    }

    public function testCp019BuscaProductoSkuExacto(): void
    {
        $db = new FakeDatabase();
        $db->queueConsultar([['codigo' => 'PROD-001', 'nombre' => 'Arroz Extra']]);
        /** @var Producto $producto */
        $producto = $this->modelWithFakeDb(Producto::class, $db);

        $result = $producto->buscarPorCodigo('PROD-001');

        $this->assertSame('PROD-001', $result['codigo']);
    }

    public function testCp023DesactivaProductoActivo(): void
    {
        $db = new FakeDatabase();
        /** @var Producto $producto */
        $producto = $this->modelWithFakeDb(Producto::class, $db);

        $producto->eliminar(10);

        $exec = $this->lastExecution($db);
        $this->assertStringContainsString('UPDATE productos SET estado = 0', $exec['sql']);
    }

    public function testCp029RegistraCategoriaValida(): void
    {
        $this->markTestSkipped('No Categoria model/controller/table interaction exists in the current codebase.');
    }

    public function testCp030ActualizaCategoriaExistente(): void
    {
        $this->markTestSkipped('No Categoria model/controller/table interaction exists in the current codebase.');
    }

    public function testCp033RegistraProveedorRucValido(): void
    {
        $db = new FakeDatabase();
        /** @var Proveedor $proveedor */
        $proveedor = $this->modelWithFakeDb(Proveedor::class, $db);

        $proveedor->crear($this->proveedorValido());

        $exec = $this->lastExecution($db);
        $this->assertSame('20601234567', $exec['params'][':ruc']);
    }

    public function testCp036ActualizaTelefonoProveedor(): void
    {
        $db = new FakeDatabase();
        /** @var Proveedor $proveedor */
        $proveedor = $this->modelWithFakeDb(Proveedor::class, $db);

        $datos = $this->proveedorValido();
        $datos['telefono'] = '912345678';
        $proveedor->actualizar(5, $datos);

        $exec = $this->lastExecution($db);
        $this->assertSame('912345678', $exec['params'][':telefono']);
    }

    public function testCp040BuscaProveedorRucExistente(): void
    {
        $db = new FakeDatabase();
        $db->queueConsultar([['ruc' => '20601234567', 'razon_social' => 'Distribuidora XYZ SAC']]);
        /** @var Proveedor $proveedor */
        $proveedor = $this->modelWithFakeDb(Proveedor::class, $db);

        $result = $proveedor->buscarPorRuc('20601234567');

        $this->assertSame('20601234567', $result['ruc']);
    }

    public function testCp045CreaUsuarioAdministrador(): void
    {
        $db = new FakeDatabase();
        /** @var Usuario $usuario */
        $usuario = $this->modelWithFakeDb(Usuario::class, $db);

        $usuario->crear($this->usuarioValido());

        $exec = $this->lastExecution($db);
        $this->assertSame('juan@tienda.com', $exec['params'][':usuario']);
    }

    public function testCp049HasheaClaveUsuario(): void
    {
        $db = new FakeDatabase();
        /** @var Usuario $usuario */
        $usuario = $this->modelWithFakeDb(Usuario::class, $db);

        $usuario->crear($this->usuarioValido());

        $hash = $this->lastExecution($db)['params'][':clave'];
        $this->assertTrue($hash !== 'Admin123' && password_verify('Admin123', $hash));
    }

    public function testCp055ActualizaRolUsuario(): void
    {
        $db = new FakeDatabase();
        /** @var Usuario $usuario */
        $usuario = $this->modelWithFakeDb(Usuario::class, $db);

        $usuario->actualizar(9, [
            'usuario' => 'juan@tienda.com',
            'nombre_completo' => 'Juan Perez',
            'email' => 'juan@tienda.com',
            'rol' => 'admin',
            'clave' => '',
        ]);

        $exec = $this->lastExecution($db);
        $this->assertSame('admin', $exec['params'][':rol']);
    }

    public function testCp058RegistraEntradaStock(): void
    {
        $this->markTestSkipped('No Movimiento/stock entry model or controller exists in the current codebase.');
    }

    public function testCp084HistorialRegistraEntradaStock(): void
    {
        $this->markTestSkipped('No Movimiento history model or controller exists in the current codebase.');
    }

    public function testCp085AuditoriaRegistraCreacionProducto(): void
    {
        $this->markTestSkipped('ProductoController::crear() depends on HTTP globals, real model construction, header redirects and exit; no isolated callable unit is available.');
    }

    public function testCp006LogoutSesionActiva(): void
    {
        $this->markTestSkipped('AuthController::logout() sends headers, destroys session and exits; no isolated callable unit is available.');
    }

    public function testCp021BuscaProductoCategoria(): void
    {
        $db = new FakeDatabase();
        $db->queueConsultar([['codigo' => 'PROD-001', 'categoria' => 'Granos']]);
        /** @var Producto $producto */
        $producto = $this->modelWithFakeDb(Producto::class, $db);

        $result = $producto->buscar('Granos');

        $this->assertSame('Granos', $result[0]['categoria']);
    }

    public function testCp025ProductoInactivoOcultoMovimientos(): void
    {
        $this->markTestSkipped('No Movimiento forms/selectors exist in the current codebase.');
    }

    public function testCp026AlertaProductosProximosVencer(): void
    {
        $this->markTestSkipped('No expiration alert query/controller/view exists in the current codebase.');
    }

    public function testCp028AlertaVencimientoDiaTreinta(): void
    {
        $this->markTestSkipped('No expiration alert query/controller/view exists in the current codebase.');
    }

    public function testCp037EliminaProveedorFisicamente(): void
    {
        $db = new FakeDatabase();
        /** @var Proveedor $proveedor */
        $proveedor = $this->modelWithFakeDb(Proveedor::class, $db);

        $proveedor->eliminar(12);

        $this->assertStringContainsString(
            'DELETE FROM proveedores',
            $this->lastExecution($db)['sql'],
            'Excel case expects physical delete, but current implementation performs logical delete.'
        );
    }

    public function testCp043ReactivaProveedorInactivo(): void
    {
        $this->markTestSkipped('No provider reactivation action exists in Proveedor model/controller.');
    }

    public function testCp044ProveedorReactivadoVisibleMovimientos(): void
    {
        $this->markTestSkipped('No provider reactivation action or Movimiento forms/selectors exist in the current codebase.');
    }

    public function testCp050DesactivaUsuario(): void
    {
        $db = new FakeDatabase();
        /** @var Usuario $usuario */
        $usuario = $this->modelWithFakeDb(Usuario::class, $db);

        $usuario->cambiarEstado(15, 0);

        $exec = $this->lastExecution($db);
        $this->assertSame(0, $exec['params'][':estado']);
    }

    public function testCp053ActivaUsuarioInactivo(): void
    {
        $db = new FakeDatabase();
        /** @var Usuario $usuario */
        $usuario = $this->modelWithFakeDb(Usuario::class, $db);

        $usuario->cambiarEstado(15, 1);

        $exec = $this->lastExecution($db);
        $this->assertSame(1, $exec['params'][':estado']);
    }

    public function testCp076DesactivaCategoriaLibre(): void
    {
        $this->markTestSkipped('No Categoria model/controller/table interaction exists in the current codebase.');
    }

    public function testCp077CategoriaInactivaOcultaProductoNuevo(): void
    {
        $this->markTestSkipped('Product form uses a hard-coded category array; no Categoria active/inactive data source exists.');
    }

    public function testCp086AuditoriaRegistraEliminacionProveedor(): void
    {
        $this->markTestSkipped('ProveedorController::eliminar() depends on HTTP globals, real model construction, header redirects and exit; no isolated callable unit is available.');
    }

    private function productoValido(): array
    {
        return [
            'codigo' => 'PROD-001',
            'nombre' => 'Arroz Extra',
            'categoria' => 'Granos',
            'precio_compra' => '3.50',
            'precio_venta' => '5.00',
            'stock' => '100',
            'stock_minimo' => '10',
            'proveedor_id' => '7',
            'fecha_vencimiento' => '2026-12-31',
        ];
    }

    private function proveedorValido(): array
    {
        return [
            'ruc' => '20601234567',
            'razon_social' => 'Distribuidora XYZ SAC',
            'contacto' => 'Juan Perez',
            'telefono' => '987654321',
            'email' => 'contacto@xyz.com',
            'direccion' => 'Av. Principal 123',
        ];
    }

    private function usuarioValido(): array
    {
        return [
            'usuario' => 'juan@tienda.com',
            'clave' => 'Admin123',
            'nombre_completo' => 'Juan Perez',
            'email' => 'juan@tienda.com',
            'rol' => 'admin',
        ];
    }
}
