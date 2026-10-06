<?php

class ControladorInscripciones {

    public function ctrGestionarInscripcion() {
        $mensaje = "";
        $tipo = "success";

        if (isset($_POST["actualizarEstadoInscripcion"])) {
            $idInscripcion = filter_input(INPUT_POST, "id_inscripcion", FILTER_VALIDATE_INT);
            $estado = isset($_POST["estado"]) ? strtolower(trim($_POST["estado"])) : "";

            if (!$idInscripcion || !in_array($estado, ["aprobada", "rechazada"], true)) {
                $mensaje = "No se pudo actualizar el estado de la solicitud.";
                $tipo = "danger";
            } elseif (ModeloInscripciones::mdlActualizarEstadoInscripcion($idInscripcion, $estado)) {
                $mensaje = $estado === "aprobada" ? "La inscripción fue aprobada." : "La inscripción fue rechazada.";
            } else {
                $mensaje = "No se pudo actualizar la solicitud.";
                $tipo = "danger";
            }
        }

        if (isset($_POST["nuevaInscripcion"])) {
            $idEstudiante = filter_input(INPUT_POST, "id_estudiante", FILTER_VALIDATE_INT);
            $idCarrera = filter_input(INPUT_POST, "id_carrera", FILTER_VALIDATE_INT);
            $idCurso = filter_input(INPUT_POST, "id_curso", FILTER_VALIDATE_INT);
            $idGestion = filter_input(INPUT_POST, "id_gestion", FILTER_VALIDATE_INT);

            if (!$idEstudiante || !$idCarrera || !$idCurso || !$idGestion) {
                $mensaje = "Faltan datos para registrar la inscripción.";
                $tipo = "danger";
            } elseif (ModeloInscripciones::mdlExisteInscripcion($idEstudiante, $idCurso, $idGestion)) {
                $mensaje = "Este estudiante ya está inscrito en ese curso y gestión.";
                $tipo = "warning";
            } elseif (ModeloInscripciones::mdlGuardarInscripcion([
                "id_estudiante" => $idEstudiante,
                "id_carrera" => $idCarrera,
                "id_curso" => $idCurso,
                "id_gestion" => $idGestion,
                "fecha_inscripcion" => date("Y-m-d"),
                "estado" => "pendiente"
            ])) {
                $mensaje = "La inscripción se registró correctamente.";
            } else {
                $mensaje = "No se pudo registrar la inscripción. Inténtalo de nuevo.";
                $tipo = "danger";
            }
        }

        return [
            "mensaje" => $mensaje,
            "tipo" => $tipo,
            "estudiantes" => ModeloInscripciones::mdlMostrarEstudiantes(),
            "carreras" => ModeloInscripciones::mdlMostrarCarreras(),
            "cursos" => ModeloInscripciones::mdlMostrarCursos(),
            "gestiones" => ModeloInscripciones::mdlMostrarGestiones(),
            "inscripciones" => ModeloInscripciones::mdlMostrarInscripciones()
        ];
    }

    public function ctrMostrarResumenEstudiantes() {
        return [
            "inscritos" => ModeloInscripciones::mdlContarEstudiantesInscritos(),
            "pendientes" => ModeloInscripciones::mdlContarEstudiantesSinInscripcion()
        ];
    }
}
