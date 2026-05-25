<?php
require_once __DIR__ . '/../config/Database.php';

class Auditoria {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function registrar($usuario_id, $accion, $modulo, $descripcion) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        $sql = "INSERT INTO auditoria (usuario_id, accion, modulo, descripcion, ip) 
                VALUES (:usuario_id, :accion, :modulo, :descripcion, :ip)";
        
        return $this->db->ejecutar($sql, [
            ':usuario_id' => $usuario_id,
            ':accion' => $accion,
            ':modulo' => $modulo,
            ':descripcion' => $descripcion,
            ':ip' => $ip
        ]);
    }
    
    public function obtenerTodos($limite = 100) {
        $sql = "SELECT a.*, u.usuario, u.nombre_completo 
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.fecha DESC
                LIMIT :limite";
        
        $stmt = $this->db->getConexion()->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function filtrar($usuario_id = null, $modulo = null, $fecha_inicio = null, $fecha_fin = null) {
        $sql = "SELECT a.*, u.usuario, u.nombre_completo 
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if ($usuario_id) {
            $sql .= " AND a.usuario_id = :usuario_id";
            $params[':usuario_id'] = $usuario_id;
        }
        
        if ($modulo) {
            $sql .= " AND a.modulo = :modulo";
            $params[':modulo'] = $modulo;
        }
        
        if ($fecha_inicio) {
            $sql .= " AND DATE(a.fecha) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fecha_inicio;
        }
        
        if ($fecha_fin) {
            $sql .= " AND DATE(a.fecha) <= :fecha_fin";
            $params[':fecha_fin'] = $fecha_fin;
        }
        
        $sql .= " ORDER BY a.fecha DESC LIMIT 500";
        
        return $this->db->consultar($sql, $params);
    }
}
?>