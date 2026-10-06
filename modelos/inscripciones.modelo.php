<?php

class ModeloInscripciones {

    public static function mdlMostrarEstudiantes() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT e.id_estudiante, u.nombre, u.apellido, e.rude
                FROM estudiante e
                INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
                ORDER BY u.apellido, u.nombre"
            );
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            return [];
        }
    }

    public static function mdlMostrarCarreras() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT id_carrera, nombre_carrera
                FROM carreras
                WHERE estado = 1
                ORDER BY nombre_carrera"
            );
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            return [];
        }
    }

    public static function mdlMostrarCursos() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT id_curso, nombre_curso, nivel, paralelo, estado
                FROM curso
                WHERE estado = 1
                ORDER BY nivel, nombre_curso, paralelo"
            );
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            return [];
        }
    }

    public static function mdlMostrarGestiones() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT id_gestion, anio, estado
                FROM gestion
                WHERE estado = 'activa'
                ORDER BY anio DESC"
            );
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            return [];
        }
    }

    public static function mdlMostrarInscripciones() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT i.id_inscripcion,
                        CONCAT(u.apellido, ', ', u.nombre) AS estudiante,
                        e.rude,
                        ca.nombre_carrera AS carrera,
                        CONCAT(c.nombre_curso, ' ', c.paralelo) AS curso,
                        g.anio,
                        i.fecha_inscripcion,
                        COALESCE(i.estado, 'pendiente') AS estado
                FROM inscripcion i
                INNER JOIN estudiante e ON e.id_estudiante = i.id_estudiante
                INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
                INNER JOIN curso c ON c.id_curso = i.id_curso
                INNER JOIN gestion g ON g.id_gestion = i.id_gestion
                LEFT JOIN carreras ca ON ca.id_carrera = i.id_carrera
                ORDER BY i.fecha_inscripcion DESC, i.id_inscripcion DESC"
            );
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            return [];
        }
    }

    public static function mdlGuardarInscripcion($datos) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "INSERT INTO inscripcion (id_estudiante, id_carrera, id_curso, id_gestion, fecha_inscripcion, estado)
                VALUES (:id_estudiante, :id_carrera, :id_curso, :id_gestion, :fecha_inscripcion, :estado)"
            );
            return $consulta->execute([
                ":id_estudiante" => $datos["id_estudiante"],
                ":id_carrera" => $datos["id_carrera"],
                ":id_curso" => $datos["id_curso"],
                ":id_gestion" => $datos["id_gestion"],
                ":fecha_inscripcion" => $datos["fecha_inscripcion"],
                ":estado" => $datos["estado"] ?? "pendiente"
            ]);
        } catch (Throwable $exception) {
            return false;
        }
    }

    public static function mdlActualizarEstadoInscripcion($idInscripcion, $estado) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "UPDATE inscripcion SET estado = :estado WHERE id_inscripcion = :id_inscripcion"
            );
            return $consulta->execute([
                ":estado" => $estado,
                ":id_inscripcion" => $idInscripcion
            ]);
        } catch (Throwable $exception) {
            return false;
        }
    }

    public static function mdlExisteInscripcion($idEstudiante, $idCurso, $idGestion) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT id_inscripcion
                FROM inscripcion
                WHERE id_estudiante = :id_estudiante
                  AND id_curso = :id_curso
                  AND id_gestion = :id_gestion
                LIMIT 1"
            );
            $consulta->execute([
                ":id_estudiante" => $idEstudiante,
                ":id_curso" => $idCurso,
                ":id_gestion" => $idGestion
            ]);
            return (bool) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            return false;
        }
    }

    public static function mdlContarEstudiantesInscritos() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT COUNT(DISTINCT i.id_estudiante)
                FROM inscripcion i
                INNER JOIN gestion g ON g.id_gestion = i.id_gestion
                WHERE g.estado = 'activa'"
            );
            return (int) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            return 0;
        }
    }

    public static function mdlContarEstudiantesSinInscripcion() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT COUNT(*)
                FROM estudiante e
                LEFT JOIN inscripcion i ON i.id_estudiante = e.id_estudiante
                LEFT JOIN gestion g ON g.id_gestion = i.id_gestion AND g.estado = 'activa'
                WHERE g.id_gestion IS NULL"
            );
            return (int) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            return 0;
        }
    }
}
