<?php

class ModeloEstudiantes {

    // Los errores quedan en el log de PHP (XAMPP: apache/logs/error.log)
    private static function mdlRegistrarError($metodo, Throwable $exception) {
        error_log("[ModeloEstudiantes::$metodo] " . $exception->getMessage());
    }

    // Si una consulta falla dentro de una transacción, se corta y se hace rollback
    private static function mdlEjecutar($consulta, $parametros = []) {
        if (!$consulta->execute($parametros)) {
            throw new RuntimeException("No se pudo ejecutar la consulta.");
        }
    }

    /*=============================================
    LISTA DE ESTUDIANTES
    Incluye el curso actual (gestión activa) y la cantidad de tutores
    =============================================*/
    public static function mdlMostrarEstudiantes() {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT e.id_estudiante, e.id_usuario, e.rude, e.fecha_nacimiento,
                        e.genero, e.direccion,
                        u.nombre, u.apellido, u.ci, u.telefono, u.correo, u.estado,
                        (SELECT CONCAT(c.nombre_curso, ' ', c.paralelo)
                           FROM inscripcion i
                           INNER JOIN curso c ON c.id_curso = i.id_curso
                           INNER JOIN gestion g ON g.id_gestion = i.id_gestion
                          WHERE i.id_estudiante = e.id_estudiante
                            AND g.estado = 'activa'
                            AND i.estado <> 'retirado'
                          ORDER BY g.`año` DESC
                          LIMIT 1) AS curso_actual,
                        (SELECT COUNT(*)
                           FROM tutor_estudiante te
                          WHERE te.id_estudiante = e.id_estudiante) AS total_tutores
                 FROM estudiante e
                 INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
                 ORDER BY u.apellido, u.nombre"
            );
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return [];
        }
    }

    public static function mdlObtenerEstudiante($idEstudiante) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT e.id_estudiante, e.id_usuario, e.rude, e.fecha_nacimiento,
                        e.genero, e.direccion,
                        u.nombre, u.apellido, u.ci, u.telefono, u.correo
                 FROM estudiante e
                 INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
                 WHERE e.id_estudiante = :id
                 LIMIT 1"
            );
            $consulta->execute([":id" => (int) $idEstudiante]);
            return $consulta->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return false;
        }
    }

    /*=============================================
    VERIFICACIONES DE DUPLICADOS
    Ante un error devuelven true (falla "cerrado": no deja guardar).
    =============================================*/
    public static function mdlExisteUsuarioCampo($campo, $valor, $excluirIdUsuario = 0) {
        if (!in_array($campo, ["username", "ci", "correo"], true)) {
            return true;
        }

        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT 1 FROM usuarios
                 WHERE $campo = :valor AND id_usuario <> :id
                 LIMIT 1"
            );
            $consulta->execute([":valor" => $valor, ":id" => (int) $excluirIdUsuario]);
            return (bool) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return true;
        }
    }

    public static function mdlExisteRude($rude, $excluirIdEstudiante = 0) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT 1 FROM estudiante
                 WHERE rude = :rude AND id_estudiante <> :id
                 LIMIT 1"
            );
            $consulta->execute([":rude" => $rude, ":id" => (int) $excluirIdEstudiante]);
            return (bool) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return true;
        }
    }

    /*=============================================
    CREAR ESTUDIANTE
    Crea la cuenta (usuarios, rol estudiante) y su perfil (estudiante)
    en una transacción. Devuelve el id_estudiante o false.
    =============================================*/
    public static function mdlCrearEstudiante($persona, $perfil) {
        $conexion = null;

        try {
            $conexion = Database::getConnection();
            $conexion->beginTransaction();

            $consulta = $conexion->prepare(
                "INSERT INTO usuarios
                    (nombre, apellido, ci, telefono, correo, username, password, rol, estado)
                 VALUES
                    (:nombre, :apellido, :ci, :telefono, :correo, :username, :password, 'estudiante', 'activo')"
            );
            self::mdlEjecutar($consulta, [
                ":nombre"   => $persona["nombre"],
                ":apellido" => $persona["apellido"],
                ":ci"       => $persona["ci"],
                ":telefono" => $persona["telefono"],
                ":correo"   => $persona["correo"],
                ":username" => $persona["username"],
                ":password" => $persona["password"]
            ]);

            $idUsuario = (int) $conexion->lastInsertId();

            $consulta = $conexion->prepare(
                "INSERT INTO estudiante (id_usuario, rude, fecha_nacimiento, genero, direccion)
                 VALUES (:id_usuario, :rude, :fecha_nacimiento, :genero, :direccion)"
            );
            self::mdlEjecutar($consulta, [
                ":id_usuario"       => $idUsuario,
                ":rude"             => $perfil["rude"],
                ":fecha_nacimiento" => $perfil["fecha_nacimiento"],
                ":genero"           => $perfil["genero"],
                ":direccion"        => $perfil["direccion"]
            ]);

            $idEstudiante = (int) $conexion->lastInsertId();

            $conexion->commit();
            return $idEstudiante;
        } catch (Throwable $exception) {
            if ($conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return false;
        }
    }

    /*=============================================
    EDITAR ESTUDIANTE
    Actualiza datos personales (nombre, apellido, CI, teléfono) y el perfil.
    Correo, usuario, contraseña y rol se administran desde Usuarios.
    =============================================*/
    public static function mdlEditarEstudiante($idEstudiante, $idUsuario, $persona, $perfil) {
        $conexion = null;

        try {
            $conexion = Database::getConnection();
            $conexion->beginTransaction();

            $consulta = $conexion->prepare(
                "UPDATE usuarios
                 SET nombre = :nombre, apellido = :apellido, ci = :ci, telefono = :telefono
                 WHERE id_usuario = :id_usuario"
            );
            self::mdlEjecutar($consulta, [
                ":nombre"     => $persona["nombre"],
                ":apellido"   => $persona["apellido"],
                ":ci"         => $persona["ci"],
                ":telefono"   => $persona["telefono"],
                ":id_usuario" => (int) $idUsuario
            ]);

            $consulta = $conexion->prepare(
                "UPDATE estudiante
                 SET rude = :rude, fecha_nacimiento = :fecha_nacimiento,
                     genero = :genero, direccion = :direccion
                 WHERE id_estudiante = :id_estudiante"
            );
            self::mdlEjecutar($consulta, [
                ":rude"             => $perfil["rude"],
                ":fecha_nacimiento" => $perfil["fecha_nacimiento"],
                ":genero"           => $perfil["genero"],
                ":direccion"        => $perfil["direccion"],
                ":id_estudiante"    => (int) $idEstudiante
            ]);

            $conexion->commit();
            return true;
        } catch (Throwable $exception) {
            if ($conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return false;
        }
    }

    /*=============================================
    TUTORES
    Un tutor es un usuario con rol "tutor"; se enlaza al estudiante
    mediante la tabla tutor_estudiante (con el parentesco).
    =============================================*/
    public static function mdlMostrarTutoresEstudiante($idEstudiante) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT te.id_tutor_estudiante, te.parentesco,
                        u.id_usuario, u.nombre, u.apellido, u.ci, u.telefono, u.correo
                 FROM tutor_estudiante te
                 INNER JOIN usuarios u ON u.id_usuario = te.id_usuario
                 WHERE te.id_estudiante = :id
                 ORDER BY u.apellido, u.nombre"
            );
            $consulta->execute([":id" => (int) $idEstudiante]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return [];
        }
    }

    // Tutores activos que todavía NO están vinculados a este estudiante
    public static function mdlMostrarTutoresDisponibles($idEstudiante) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT u.id_usuario, u.nombre, u.apellido, u.ci
                 FROM usuarios u
                 WHERE u.rol = 'tutor'
                   AND u.estado = 'activo'
                   AND NOT EXISTS (
                        SELECT 1 FROM tutor_estudiante te
                        WHERE te.id_usuario = u.id_usuario
                          AND te.id_estudiante = :id
                   )
                 ORDER BY u.apellido, u.nombre"
            );
            $consulta->execute([":id" => (int) $idEstudiante]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return [];
        }
    }

    public static function mdlEsTutorActivo($idUsuario) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT 1 FROM usuarios
                 WHERE id_usuario = :id AND rol = 'tutor' AND estado = 'activo'
                 LIMIT 1"
            );
            $consulta->execute([":id" => (int) $idUsuario]);
            return (bool) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return false;
        }
    }

    public static function mdlExisteVinculo($idUsuario, $idEstudiante) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "SELECT 1 FROM tutor_estudiante
                 WHERE id_usuario = :usuario AND id_estudiante = :estudiante
                 LIMIT 1"
            );
            $consulta->execute([
                ":usuario"    => (int) $idUsuario,
                ":estudiante" => (int) $idEstudiante
            ]);
            return (bool) $consulta->fetchColumn();
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return true;
        }
    }

    public static function mdlVincularTutor($idUsuario, $idEstudiante, $parentesco) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "INSERT INTO tutor_estudiante (id_usuario, id_estudiante, parentesco)
                 VALUES (:usuario, :estudiante, :parentesco)"
            );
            return $consulta->execute([
                ":usuario"    => (int) $idUsuario,
                ":estudiante" => (int) $idEstudiante,
                ":parentesco" => $parentesco
            ]);
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return false;
        }
    }

    // Crea la cuenta del tutor (rol tutor) y lo vincula al estudiante, en una transacción
    public static function mdlCrearTutor($persona, $idEstudiante, $parentesco) {
        $conexion = null;

        try {
            $conexion = Database::getConnection();
            $conexion->beginTransaction();

            $consulta = $conexion->prepare(
                "INSERT INTO usuarios
                    (nombre, apellido, ci, telefono, correo, username, password, rol, estado)
                 VALUES
                    (:nombre, :apellido, :ci, :telefono, :correo, :username, :password, 'tutor', 'activo')"
            );
            self::mdlEjecutar($consulta, [
                ":nombre"   => $persona["nombre"],
                ":apellido" => $persona["apellido"],
                ":ci"       => $persona["ci"],
                ":telefono" => $persona["telefono"],
                ":correo"   => $persona["correo"],
                ":username" => $persona["username"],
                ":password" => $persona["password"]
            ]);

            $idUsuario = (int) $conexion->lastInsertId();

            $consulta = $conexion->prepare(
                "INSERT INTO tutor_estudiante (id_usuario, id_estudiante, parentesco)
                 VALUES (:usuario, :estudiante, :parentesco)"
            );
            self::mdlEjecutar($consulta, [
                ":usuario"    => $idUsuario,
                ":estudiante" => (int) $idEstudiante,
                ":parentesco" => $parentesco
            ]);

            $conexion->commit();
            return true;
        } catch (Throwable $exception) {
            if ($conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return false;
        }
    }

    // Solo borra el vínculo; la cuenta del tutor se conserva (puede tener otros hijos)
    public static function mdlQuitarTutor($idTutorEstudiante, $idEstudiante) {
        try {
            $conexion = Database::getConnection();
            $consulta = $conexion->prepare(
                "DELETE FROM tutor_estudiante
                 WHERE id_tutor_estudiante = :id AND id_estudiante = :estudiante"
            );
            return $consulta->execute([
                ":id"         => (int) $idTutorEstudiante,
                ":estudiante" => (int) $idEstudiante
            ]);
        } catch (Throwable $exception) {
            self::mdlRegistrarError(__FUNCTION__, $exception);
            return false;
        }
    }
}
