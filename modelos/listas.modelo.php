<?php

class ModeloListas {

    static public function mdlMostrarTablaAcademica($tabla) {
        $tablasPermitidas = [
            "carreras",
            "materias",
            "grupos",
            "inscripciones",
            "calificaciones"
        ];

        if (!in_array($tabla, $tablasPermitidas, true)) {
            return ["disponible" => false, "columnas" => [], "registros" => []];
        }

        try {
            $conexion = Database::getConnection();
            if (!$conexion) {
                return ["disponible" => false, "columnas" => [], "registros" => []];
            }

            $consulta = $conexion->prepare(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :tabla"
            );
            $consulta->bindParam(":tabla", $tabla, PDO::PARAM_STR);
            $consulta->execute();

            if ((int) $consulta->fetchColumn() === 0) {
                return ["disponible" => false, "columnas" => [], "registros" => []];
            }

            $columnas = $conexion->query("SHOW COLUMNS FROM `" . $tabla . "`")->fetchAll(PDO::FETCH_COLUMN);
            $registros = $conexion->query("SELECT * FROM `" . $tabla . "`")->fetchAll(PDO::FETCH_ASSOC);

            return ["disponible" => true, "columnas" => $columnas, "registros" => $registros];
        } catch (Throwable $exception) {
            return ["disponible" => false, "columnas" => [], "registros" => []];
        }
    }
}