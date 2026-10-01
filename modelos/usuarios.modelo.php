<?php

class ModeloUsuarios {

    static public function mdlMostrarUsuarios($tabla = "usuarios", $item = null, $valor = null) {
        if ($tabla != "usuarios") return false;

        try {
            $conexion = Database::getConnection();
            if ($item !== null) {
                if (!in_array($item, ["username", "ci", "correo"])) {
                    return false;
                }

                $stmt = $conexion->prepare(
                    "SELECT * FROM usuarios WHERE $item = :valor LIMIT 1"
                );
                $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetch() ?: false;
            }

            $stmt = $conexion->prepare("SELECT * FROM usuarios ORDER BY id_usuario DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    static public function mdlRegistrarUsuario($datos) {
        try {
            $conexion = Database::getConnection();
            $stmt = $conexion->prepare(
                "INSERT INTO usuarios
                    (nombre, apellido, ci, correo, username, password, rol, estado)
                VALUES
                    (:nombre, :apellido, :ci, :correo, :username, :password, :rol, 'activo')"
            );

            return $stmt->execute([
                ":nombre" => $datos["nombre"],
                ":apellido" => $datos["apellido"],
                ":ci" => $datos["ci"],
                ":correo" => $datos["correo"],
                ":username" => $datos["username"],
                ":password" => $datos["password"],
                ":rol" => $datos["rol"]
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    static public function mdlEditarUsuario($datos) {
        try {
            $conexion = Database::getConnection();
            if (!$conexion) {
                return false;
            }

            if ($datos["password"] != "") {
                $stmt = $conexion->prepare(
                    "UPDATE usuarios SET nombre = :nombre, apellido = :apellido,
                        correo = :correo, password = :password, rol = :rol
                    WHERE id_usuario = :id_usuario"
                );
                $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
            } else {
                $stmt = $conexion->prepare(
                    "UPDATE usuarios SET nombre = :nombre, apellido = :apellido,
                        correo = :correo, rol = :rol
                    WHERE id_usuario = :id_usuario"
                );
            }

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $datos["apellido"], PDO::PARAM_STR);
            $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
            $stmt->bindParam(":rol", $datos["rol"], PDO::PARAM_STR);
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    static public function mdlCambiarEstadoUsuario($idUsuario, $estado) {
        try {
            $conexion = Database::getConnection();
            $stmt = $conexion->prepare(
                "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id_usuario"
            );
            $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
            $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}