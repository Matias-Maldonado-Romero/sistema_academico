<?php

class ModeloMaterias {

    static public function mdlMostrarMaterias() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT id_materia, nombre_materia, descripcion, campo, estado
                FROM materia
                ORDER BY estado DESC, nombre_materia"
            );
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            return [];
        }
    }

    static public function mdlMostrarMateria($idMateria) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT id_materia, nombre_materia, descripcion, campo
                FROM materia
                WHERE id_materia = :id_materia
                LIMIT 1"
            );
            $consulta->execute([":id_materia" => $idMateria]);
            return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $exception) {
            return null;
        }
    }

    static public function mdlExisteNombre($nombre, $idExcluir = 0) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT id_materia FROM materia
                WHERE nombre_materia = :nombre AND id_materia <> :id_excluir
                LIMIT 1"
            );
            $consulta->execute([
                ":nombre" => $nombre,
                ":id_excluir" => $idExcluir
            ]);
            return (bool) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            return false;
        }
    }

    static public function mdlGuardarMateria($datos) {
        try {
            $conexion = Database::getConnection();
            if ($datos["id_materia"] > 0) {
                $consulta = $conexion->prepare(
                    "UPDATE materia
                    SET nombre_materia = :nombre, descripcion = :descripcion, campo = :campo
                    WHERE id_materia = :id_materia"
                );
                return $consulta->execute([
                    ":nombre" => $datos["nombre_materia"],
                    ":descripcion" => $datos["descripcion"],
                    ":campo" => $datos["campo"],
                    ":id_materia" => $datos["id_materia"]
                ]);
            }

            $consulta = $conexion->prepare(
                "INSERT INTO materia (nombre_materia, descripcion, campo)
                VALUES (:nombre, :descripcion, :campo)"
            );
            return $consulta->execute([
                ":nombre" => $datos["nombre_materia"],
                ":descripcion" => $datos["descripcion"],
                ":campo" => $datos["campo"]
            ]);
        } catch (Throwable $exception) {
            return false;
        }
    }

    static public function mdlCambiarEstado($idMateria, $estado) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "UPDATE materia SET estado = :estado WHERE id_materia = :id_materia"
            );
            return $consulta->execute([
                ":estado" => $estado,
                ":id_materia" => $idMateria
            ]);
        } catch (Throwable $exception) {
            return false;
        }
    }
}