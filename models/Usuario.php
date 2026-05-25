<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Modelo Usuario - Con PDO
 */
class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Autenticar usuario
     */
    public function autenticar($usuario, $clave) {
        $sql = "SELECT id, usuario, clave, nombre_completo, rol, estado 
                FROM usuarios 
                WHERE usuario = :usuario AND estado = 1";
        
        $resultado = $this->db->consultar($sql, [':usuario' => $usuario]);
        
        if (count($resultado) > 0) {
            $usuarioData = $resultado[0];
            if (password_verify($clave, $usuarioData['clave'])) {
                return $usuarioData;
            }
        }
        return false;
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos() {
        $sql = "SELECT id, usuario, nombre_completo, rol, estado, fecha_registro 
                FROM usuarios 
                ORDER BY fecha_registro DESC";
        return $this->db->consultar($sql);
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT id, usuario, nombre_completo, rol, estado 
                FROM usuarios 
                WHERE id = :id";
        
        $resultado = $this->db->consultar($sql, [':id' => $id]);
        return count($resultado) > 0 ? $resultado[0] : null;
    }
    
    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        $claveHash = password_hash($datos['clave'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (usuario, clave, nombre_completo, rol) 
                VALUES (:usuario, :clave, :nombre, :rol)";
        
        return $this->db->ejecutar($sql, [
            ':usuario' => $datos['usuario'],
            ':clave' => $claveHash,
            ':nombre' => $datos['nombre_completo'],
            ':rol' => $datos['rol']
        ]);
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        if (!empty($datos['clave'])) {
            $claveHash = password_hash($datos['clave'], PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios 
                    SET usuario = :usuario, 
                        nombre_completo = :nombre, 
                        rol = :rol,
                        clave = :clave
                    WHERE id = :id";
            
            return $this->db->ejecutar($sql, [
                ':usuario' => $datos['usuario'],
                ':nombre' => $datos['nombre_completo'],
                ':rol' => $datos['rol'],
                ':clave' => $claveHash,
                ':id' => $id
            ]);
        } else {
            $sql = "UPDATE usuarios 
                    SET usuario = :usuario, 
                        nombre_completo = :nombre, 
                        rol = :rol
                    WHERE id = :id";
            
            return $this->db->ejecutar($sql, [
                ':usuario' => $datos['usuario'],
                ':nombre' => $datos['nombre_completo'],
                ':rol' => $datos['rol'],
                ':id' => $id
            ]);
        }
    }
    
    /**
     * Cambiar estado del usuario
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE usuarios SET estado = :estado WHERE id = :id";
        return $this->db->ejecutar($sql, [':estado' => $estado, ':id' => $id]);
    }
    
    /**
     * Eliminar usuario
     */
    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        return $this->db->ejecutar($sql, [':id' => $id]);
    }
}
?>