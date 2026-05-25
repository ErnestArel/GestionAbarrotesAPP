<?php
class Database {
    private static $instance = null;
    private $conexion;
    
    private $host = 'localhost';
    private $usuario = 'root';
    private $clave = '';
    private $base = 'tiendaAbarrotes';
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->base};charset=utf8";
            $this->conexion = new PDO($dsn, $this->usuario, $this->clave);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
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
?>