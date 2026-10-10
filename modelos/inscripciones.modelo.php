<?php

class ModeloInscripciones {

    private static function mdlCursoSecundariaValido($conexion, $idCurso) {
        $consulta = $conexion->prepare(
            "SELECT 1
            FROM curso
            WHERE id_curso = :id_curso
              AND estado = 1
              AND paralelo IN ('A', 'B')
            LIMIT 1"
        );
        $consulta->execute([":id_curso" => (int) $idCurso]);
        return (bool) $consulta->fetchColumn();
    }

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

    public static function mdlMostrarCursos() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT id_curso, nombre_curso, paralelo, estado
                FROM curso
                WHERE estado = 1 AND paralelo IN ('A', 'B')
                ORDER BY nombre_curso, paralelo, id_curso"
            );
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            error_log("[ModeloInscripciones::mdlMostrarCursos] " . $exception->getMessage());
            return [];
        }
    }

    public static function mdlMostrarEstudiantesEnCurso($idCurso) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT DISTINCT e.id_estudiante, u.nombre, u.apellido
                FROM inscripcion i
                INNER JOIN estudiante e ON e.id_estudiante = i.id_estudiante
                INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
                INNER JOIN gestion g ON g.id_gestion = i.id_gestion
                WHERE i.id_curso = :id_curso
                  AND i.estado = 'activo'
                  AND g.estado = 'activa'
                ORDER BY u.apellido, u.nombre"
            );
            $consulta->execute([":id_curso" => (int) $idCurso]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            error_log("[ModeloInscripciones::mdlMostrarEstudiantesEnCurso] " . $exception->getMessage());
            return [];
        }
    }

    public static function mdlMostrarGestiones() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->query(
                "SELECT id_gestion, `año` AS anio, estado
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
                        CONCAT(c.nombre_curso, ' ', c.paralelo) AS curso,
                        g.`año` AS anio,
                        i.fecha_inscripcion,
                        COALESCE(i.estado, 'pendiente') AS estado
                FROM inscripcion i
                INNER JOIN estudiante e ON e.id_estudiante = i.id_estudiante
                INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
                INNER JOIN curso c ON c.id_curso = i.id_curso
                INNER JOIN gestion g ON g.id_gestion = i.id_gestion
                ORDER BY u.apellido, u.nombre, i.fecha_inscripcion DESC, i.id_inscripcion DESC"
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
            if (!self::mdlCursoSecundariaValido($conexion, $datos["id_curso"])) {
                return false;
            }
            $verificar = $conexion->prepare(
                "SELECT 1 FROM gestion WHERE id_gestion = :id AND estado = 'activa'"
            );
            $verificar->execute([":id" => (int) $datos["id_gestion"]]);
            if (!$verificar->fetchColumn()) {
                return false;
            }
            $consulta = $conexion->prepare(
                "INSERT INTO inscripcion (id_estudiante, id_curso, id_gestion, fecha_inscripcion, estado)
                VALUES (:id_estudiante, :id_curso, :id_gestion, :fecha_inscripcion, :estado)"
            );
            return $consulta->execute([
                ":id_estudiante" => $datos["id_estudiante"],
                ":id_curso" => $datos["id_curso"],
                ":id_gestion" => $datos["id_gestion"],
                ":fecha_inscripcion" => $datos["fecha_inscripcion"],
                ":estado" => $datos["estado"] ?? "pendiente"
            ]);
        } catch (Throwable $exception) {
            error_log("[ModeloInscripciones::mdlGuardarInscripcion] " . $exception->getMessage());
            return false;
        }
    }

    public static function mdlCrearSolicitudOnline($persona, $perfil, $idCurso, $idGestion) {
        $conexion = null;

        try {
            $conexion = Database::getConnection();
            $conexion->beginTransaction();

            $consulta = $conexion->prepare(
                "SELECT 1 FROM usuarios
                 WHERE ci = :ci OR correo = :correo OR username = :username
                 LIMIT 1"
            );
            $consulta->execute([
                ":ci" => $persona["ci"],
                ":correo" => $persona["correo"],
                ":username" => $persona["username"]
            ]);
            if ($consulta->fetchColumn()) {
                $conexion->rollBack();
                return "duplicado";
            }

            if ($perfil["rude"] !== null) {
                $consulta = $conexion->prepare("SELECT 1 FROM estudiante WHERE rude = :rude LIMIT 1");
                $consulta->execute([":rude" => $perfil["rude"]]);
                if ($consulta->fetchColumn()) {
                    $conexion->rollBack();
                    return "rude_duplicado";
                }
            }

            if (!self::mdlCursoSecundariaValido($conexion, $idCurso)) {
                throw new RuntimeException("El curso seleccionado no está disponible.");
            }

            $consulta = $conexion->prepare("SELECT 1 FROM gestion WHERE id_gestion = :id AND estado = 'activa'");
            $consulta->execute([":id" => $idGestion]);
            if (!$consulta->fetchColumn()) {
                throw new RuntimeException("La gestión seleccionada no está activa.");
            }

            $consulta = $conexion->prepare(
                "INSERT INTO usuarios
                    (nombre, apellido, ci, telefono, correo, username, password, rol, estado)
                 VALUES
                    (:nombre, :apellido, :ci, :telefono, :correo, :username, :password, 'estudiante', 'activo')"
            );
            $consulta->execute([
                ":nombre" => $persona["nombre"],
                ":apellido" => $persona["apellido"],
                ":ci" => $persona["ci"],
                ":telefono" => $persona["telefono"],
                ":correo" => $persona["correo"],
                ":username" => $persona["username"],
                ":password" => $persona["password"]
            ]);
            $idUsuario = (int) $conexion->lastInsertId();

            $consulta = $conexion->prepare(
                "INSERT INTO estudiante (id_usuario, rude, fecha_nacimiento, genero, direccion)
                 VALUES (:id_usuario, :rude, :fecha_nacimiento, :genero, :direccion)"
            );
            $consulta->execute([
                ":id_usuario" => $idUsuario,
                ":rude" => $perfil["rude"],
                ":fecha_nacimiento" => $perfil["fecha_nacimiento"],
                ":genero" => $perfil["genero"],
                ":direccion" => $perfil["direccion"]
            ]);
            $idEstudiante = (int) $conexion->lastInsertId();

            $consulta = $conexion->prepare(
                "INSERT INTO inscripcion
                    (id_estudiante, id_curso, id_gestion, fecha_inscripcion, estado)
                 VALUES
                    (:id_estudiante, :id_curso, :id_gestion, :fecha_inscripcion, 'pendiente')"
            );
            $consulta->execute([
                ":id_estudiante" => $idEstudiante,
                ":id_curso" => $idCurso,
                ":id_gestion" => $idGestion,
                ":fecha_inscripcion" => date("Y-m-d")
            ]);

            $conexion->commit();
            return true;
        } catch (PDOException $exception) {
            if ($conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            if ($exception->getCode() === "23000") {
                return str_contains($exception->getMessage(), "'rude'")
                    ? "rude_duplicado"
                    : "duplicado";
            }
            error_log("[ModeloInscripciones::mdlCrearSolicitudOnline] " . $exception->getMessage());
            return false;
        } catch (Throwable $exception) {
            if ($conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("[ModeloInscripciones::mdlCrearSolicitudOnline] " . $exception->getMessage());
            return false;
        }
    }

    public static function mdlActualizarEstadoInscripcion($idInscripcion, $estado) {
        if (!in_array($estado, ["activo", "rechazado"], true)) {
            return false;
        }
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "UPDATE inscripcion
                SET estado = :estado
                WHERE id_inscripcion = :id_inscripcion
                AND estado = 'pendiente'"
            );
            $consulta->execute([
                ":estado" => $estado,
                ":id_inscripcion" => (int) $idInscripcion
            ]);
            return $consulta->rowCount() > 0;
        } catch (Throwable $exception) {
            error_log("[ModeloInscripciones::mdlActualizarEstadoInscripcion] " . $exception->getMessage());
            return false;
        }
    }

    public static function mdlExisteInscripcion($idEstudiante, $idCurso, $idGestion) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT 1
                FROM inscripcion
                WHERE id_estudiante = :id_estudiante
                AND id_gestion = :id_gestion
                LIMIT 1"
            );
            $consulta->execute([
                ":id_estudiante" => (int) $idEstudiante,
                ":id_gestion" => (int) $idGestion
            ]);
            return (bool) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            error_log("[ModeloInscripciones::mdlExisteInscripcion] " . $exception->getMessage());
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
                WHERE g.estado = 'activa' AND i.estado = 'activo'"
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
