<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * ============================================================================
 * MODELO USUARIO
 * ============================================================================
 */

class Usuario {

    private $db;

    public function __construct() {

        $this->db =
            Database::getInstance();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTENTICAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function autenticar($usuario, $clave) {

        $sql = "

            SELECT

                id,
                usuario,
                clave,
                nombre_completo,
                rol,
                estado

            FROM usuarios

            WHERE usuario = :usuario

            AND estado = 1

        ";

        $resultado =
            $this->db->consultar(

                $sql,

                [
                    ':usuario' => $usuario
                ]
            );

        if (count($resultado) > 0) {

            $usuarioData = $resultado[0];

            if (

                password_verify(
                    $clave,
                    $usuarioData['clave']
                )

            ) {

                return $usuarioData;
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER TODOS
    |--------------------------------------------------------------------------
    */

    public function obtenerTodos() {

        $sql = "

            SELECT

                id,
                usuario,
                nombre_completo,
                email,
                rol,
                estado,
                fecha_registro

            FROM usuarios

            ORDER BY fecha_registro DESC

        ";

        return $this->db->consultar($sql);
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerPorId($id) {

        $sql = "

            SELECT

                id,
                usuario,
                nombre_completo,
                email,
                rol,
                estado

            FROM usuarios

            WHERE id = :id

        ";

        $resultado =
            $this->db->consultar(

                $sql,

                [
                    ':id' => $id
                ]
            );

        return count($resultado) > 0
            ? $resultado[0]
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function crear($datos) {

        $claveHash =
            password_hash(

                $datos['clave'],
                PASSWORD_DEFAULT
            );

        $sql = "

            INSERT INTO usuarios (

                usuario,
                clave,
                nombre_completo,
                email,
                rol

            )

            VALUES (

                :usuario,
                :clave,
                :nombre,
                :email,
                :rol

            )

        ";

        return $this->db->ejecutar(

            $sql,

            [

                ':usuario' =>
                    $datos['usuario'],

                ':clave' =>
                    $claveHash,

                ':nombre' =>
                    $datos['nombre_completo'],

                ':email' =>
                    $datos['email'],

                ':rol' =>
                    $datos['rol']
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function actualizar($id, $datos) {

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CON CONTRASEÑA
        |--------------------------------------------------------------------------
        */

        if (!empty($datos['clave'])) {

            $claveHash =
                password_hash(

                    $datos['clave'],
                    PASSWORD_DEFAULT
                );

            $sql = "

                UPDATE usuarios

                SET

                    usuario = :usuario,
                    nombre_completo = :nombre,
                    email = :email,
                    rol = :rol,
                    clave = :clave

                WHERE id = :id

            ";

            return $this->db->ejecutar(

                $sql,

                [

                    ':usuario' =>
                        $datos['usuario'],

                    ':nombre' =>
                        $datos['nombre_completo'],

                    ':email' =>
                        $datos['email'],

                    ':rol' =>
                        $datos['rol'],

                    ':clave' =>
                        $claveHash,

                    ':id' =>
                        $id
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR SIN CAMBIAR CONTRASEÑA
        |--------------------------------------------------------------------------
        */

        $sql = "

            UPDATE usuarios

            SET

                usuario = :usuario,
                nombre_completo = :nombre,
                email = :email,
                rol = :rol

            WHERE id = :id

        ";

        return $this->db->ejecutar(

            $sql,

            [

                ':usuario' =>
                    $datos['usuario'],

                ':nombre' =>
                    $datos['nombre_completo'],

                ':email' => 
                    $datos['email'],

                ':rol' =>
                    $datos['rol'],

                ':id' =>
                    $id
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado($id, $estado) {

        $sql = "

            UPDATE usuarios

            SET estado = :estado

            WHERE id = :id

        ";

        return $this->db->ejecutar(

            $sql,

            [

                ':estado' =>
                    $estado,

                ':id' =>
                    $id
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function eliminar($id) {

        $sql = "

            DELETE FROM usuarios

            WHERE id = :id

        ";

        return $this->db->ejecutar(

            $sql,

            [
                ':id' => $id
            ]
        );
    }
}

?>