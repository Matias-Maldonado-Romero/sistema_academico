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

            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetch();

        } else {
            $stmt = Database::getConnection()->prepare("SELECT * FROM $tabla");

            $stmt->execute();

            return $stmt->fetchAll();
        }

        $stmt = null;
    }
}
?>