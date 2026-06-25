<?php

require_once __DIR__ . '/../config/Database.php';

class Proveedor {

    private $db;

    public function __construct() {

        $this->db =
            Database::getInstance();
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER TODOS
    |--------------------------------------------------------------------------
    */

    public function obtenerTodos() {

        $sql = "

            SELECT *

            FROM proveedores

            WHERE estado = 1

            ORDER BY razon_social ASC

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

            SELECT *

            FROM proveedores

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
    | BUSCAR POR RUC
    |--------------------------------------------------------------------------
    */

    public function buscarPorRuc($ruc) {

        $sql = "

            SELECT *

            FROM proveedores

            WHERE ruc = :ruc

            AND estado = 1

        ";

        $resultado =
            $this->db->consultar(

                $sql,

                [
                    ':ruc' => $ruc
                ]
            );

        return count($resultado) > 0
            ? $resultado[0]
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCAR
    |--------------------------------------------------------------------------
    */

    public function buscar($termino) {

        $sql = "

            SELECT *

            FROM proveedores

            WHERE (

                razon_social LIKE :termino
                OR ruc LIKE :termino
                OR contacto LIKE :termino

            )

            AND estado = 1

            ORDER BY razon_social ASC

        ";

        return $this->db->consultar(

            $sql,

            [
                ':termino' =>
                    "%{$termino}%"
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    public function crear($datos) {

        $sql = "

            INSERT INTO proveedores (

                ruc,
                razon_social,
                contacto,
                telefono,
                email,
                direccion

            )

            VALUES (

                :ruc,
                :razon_social,
                :contacto,
                :telefono,
                :email,
                :direccion

            )

        ";

        return $this->db->ejecutar(

            $sql,

            [

                ':ruc' =>
                    $datos['ruc'],

                ':razon_social' =>
                    $datos['razon_social'],

                ':contacto' =>
                    $datos['contacto'],

                ':telefono' =>
                    $datos['telefono'],

                ':email' =>
                    $datos['email'],

                ':direccion' =>
                    $datos['direccion']
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function actualizar($id, $datos) {

        $sql = "

            UPDATE proveedores

            SET

                ruc = :ruc,
                razon_social = :razon_social,
                contacto = :contacto,
                telefono = :telefono,
                email = :email,
                direccion = :direccion

            WHERE id = :id

        ";

        return $this->db->ejecutar(

            $sql,

            [

                ':ruc' =>
                    $datos['ruc'],

                ':razon_social' =>
                    $datos['razon_social'],

                ':contacto' =>
                    $datos['contacto'],

                ':telefono' =>
                    $datos['telefono'],

                ':email' =>
                    $datos['email'],

                ':direccion' =>
                    $datos['direccion'],

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

            UPDATE proveedores

            SET estado = 0

            WHERE id = :id

        ";

        return $this->db->ejecutar(

            $sql,

            [
                ':id' => $id
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CONTAR PRODUCTOS
    |--------------------------------------------------------------------------
    */

    public function contarProductos($id) {

        $sql = "

            SELECT COUNT(*) AS total

            FROM productos

            WHERE proveedor_id = :id

            AND estado = 1

        ";

        $resultado =
            $this->db->consultar(

                $sql,

                [
                    ':id' => $id
                ]
            );

        return $resultado[0]['total'];
    }
}

?>