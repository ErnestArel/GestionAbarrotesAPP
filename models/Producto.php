<?php
require_once __DIR__ . '/../config/Database.php';

class Producto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function obtenerTodos() {
        $sql = "SELECT p.*, pr.razon_social as proveedor_nombre 
                FROM productos p
                LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
                WHERE p.estado = 1 
                ORDER BY p.nombre ASC";
        return $this->db->consultar($sql);
    }
    
    public function buscar($termino) {
        $sql = "SELECT p.*, pr.razon_social as proveedor_nombre 
                FROM productos p
                LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
                WHERE (p.nombre LIKE :termino OR p.codigo LIKE :termino OR p.categoria LIKE :termino) 
                AND p.estado = 1 
                ORDER BY p.nombre ASC";
        return $this->db->consultar($sql, [':termino' => "%$termino%"]);
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $resultado = $this->db->consultar($sql, [':id' => $id]);
        return count($resultado) > 0 ? $resultado[0] : null;
    }
    
    public function buscarPorCodigo($codigo) {
        $sql = "SELECT p.*, pr.razon_social as proveedor_nombre 
                FROM productos p
                LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
                WHERE p.codigo = :codigo AND p.estado = 1";
        $resultado = $this->db->consultar($sql, [':codigo' => $codigo]);
        return count($resultado) > 0 ? $resultado[0] : null;
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO productos (codigo, nombre, categoria, precio_compra, precio_venta, stock, stock_minimo, proveedor_id, fecha_vencimiento) 
                VALUES (:codigo, :nombre, :categoria, :precio_compra, :precio_venta, :stock, :stock_minimo, :proveedor_id, :fecha_venc)";
        
        return $this->db->ejecutar($sql, [
            ':codigo' => $datos['codigo'],
            ':nombre' => $datos['nombre'],
            ':categoria' => $datos['categoria'],
            ':precio_compra' => $datos['precio_compra'],
            ':precio_venta' => $datos['precio_venta'],
            ':stock' => $datos['stock'],
            ':stock_minimo' => $datos['stock_minimo'],
            ':proveedor_id' => $datos['proveedor_id'] ?: null,
            ':fecha_venc' => $datos['fecha_vencimiento'] ?: null
        ]);
    }
    
    public function actualizar($id, $datos) {
        $sql = "UPDATE productos 
                SET codigo = :codigo, nombre = :nombre, categoria = :categoria,
                    precio_compra = :precio_compra, precio_venta = :precio_venta,
                    stock = :stock, stock_minimo = :stock_minimo,
                    proveedor_id = :proveedor_id, fecha_vencimiento = :fecha_venc
                WHERE id = :id";
        
        return $this->db->ejecutar($sql, [
            ':codigo' => $datos['codigo'],
            ':nombre' => $datos['nombre'],
            ':categoria' => $datos['categoria'],
            ':precio_compra' => $datos['precio_compra'],
            ':precio_venta' => $datos['precio_venta'],
            ':stock' => $datos['stock'],
            ':stock_minimo' => $datos['stock_minimo'],
            ':proveedor_id' => $datos['proveedor_id'] ?: null,
            ':fecha_venc' => $datos['fecha_vencimiento'] ?: null,
            ':id' => $id
        ]);
    }
    
    public function eliminar($id) {
        $sql = "UPDATE productos SET estado = 0 WHERE id = :id";
        return $this->db->ejecutar($sql, [':id' => $id]);
    }
    
    public function obtenerCategorias() {
        $sql = "SELECT DISTINCT categoria FROM productos WHERE estado = 1 ORDER BY categoria ASC";
        return $this->db->consultar($sql);
    }
    
    public function stockBajo() {
        $sql = "SELECT * FROM productos WHERE stock < stock_minimo AND estado = 1 ORDER BY stock ASC";
        return $this->db->consultar($sql);
    }
}
?>