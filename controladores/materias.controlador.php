<?php

class ControladorMaterias {

    public function ctrGestionarMaterias() {
        $mensaje = "";
        $tipo = "success";

        if (isset($_POST["guardarMateria"])) {
            $idMateria = filter_input(INPUT_POST, "id_materia", FILTER_VALIDATE_INT) ?: 0;
            $nombre = trim($_POST["nombre_materia"] ?? "");
            $descripcion = trim($_POST["descripcion"] ?? "");
            $campo = trim($_POST["campo"] ?? "");
            $longitudNombre = preg_match_all('/./us', $nombre);
            $longitudDescripcion = preg_match_all('/./us', $descripcion);
            $longitudCampo = preg_match_all('/./us', $campo);

            if ($nombre === "") {
                $mensaje = "El nombre de la materia es obligatorio.";
                $tipo = "danger";
            } elseif ($longitudNombre === false || $longitudDescripcion === false || $longitudCampo === false
                || $longitudNombre > 100 || $longitudDescripcion > 150 || $longitudCampo > 50) {
                $mensaje = "Uno o más campos superan la longitud permitida.";
                $tipo = "danger";
            } elseif (ModeloMaterias::mdlExisteNombre($nombre, $idMateria)) {
                $mensaje = "Ya existe una materia con ese nombre.";
                $tipo = "warning";
            } elseif (ModeloMaterias::mdlGuardarMateria([
                "id_materia" => $idMateria,
                "nombre_materia" => $nombre,
                "descripcion" => $descripcion !== "" ? $descripcion : null,
                "campo" => $campo !== "" ? $campo : null
            ])) {
                $mensaje = $idMateria > 0
                    ? "La materia se actualizó correctamente."
                    : "La materia se registró correctamente.";
            } else {
                $mensaje = "No se pudo guardar la materia. Revisa los datos e inténtalo de nuevo.";
                $tipo = "danger";
            }
        } elseif (isset($_POST["cambiarEstadoMateria"])) {
            $idMateria = filter_input(INPUT_POST, "id_materia", FILTER_VALIDATE_INT);
            $estado = filter_input(INPUT_POST, "nuevo_estado", FILTER_VALIDATE_INT);

            if (!$idMateria || !in_array($estado, [0, 1], true)) {
                $mensaje = "La materia seleccionada no es válida.";
                $tipo = "danger";
            } elseif (ModeloMaterias::mdlCambiarEstado($idMateria, $estado)) {
                $mensaje = $estado === 1
                    ? "La materia se activó correctamente."
                    : "La materia se desactivó correctamente.";
            } else {
                $mensaje = "No se pudo cambiar el estado de la materia.";
                $tipo = "danger";
            }
        }

        $materiaEditar = null;
        if (isset($_GET["editar"])) {
            $idEditar = filter_input(INPUT_GET, "editar", FILTER_VALIDATE_INT);
            if ($idEditar) {
                $materiaEditar = ModeloMaterias::mdlMostrarMateria($idEditar);
            }
        }

        return [
            "mensaje" => $mensaje,
            "tipo" => $tipo,
            "materias" => ModeloMaterias::mdlMostrarMaterias(),
            "materiaEditar" => $materiaEditar
        ];
    }
}