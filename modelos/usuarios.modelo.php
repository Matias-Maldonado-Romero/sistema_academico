<?php
// Archivo: modelos/usuarios.modelo.php

class ModeloUsuarios {

    /*=============================================
    MOSTRAR / BUSCAR USUARIOS
    =============================================*/
    static public function mdlMostrarUsuarios($tabla, $item, $valor) {

        if ($item != null) {
            // Consulta preparada con PDO para evitar SQL Injection
            $stmt = Database::getConnection()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

            // Enlazamos el parámetro de forma segura
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);

            $stmt->execute();

            // Retornamos una sola fila (arreglo asociativo)
            return $stmt->fetch();

        } else {
            // Si no se pasa un filtro, retorna todos los usuarios
            $stmt = Database::getConnection()->prepare("SELECT * FROM $tabla");

            $stmt->execute();

            return $stmt->fetchAll();
        }

        // Cerramos la conexión (Buena práctica)
        $stmt = null;
    }
}