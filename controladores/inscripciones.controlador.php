<?php

class ControladorInscripciones {

    public function ctrGestionarSolicitudOnline() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION["csrf_solicitud_inscripcion"])) {
            $_SESSION["csrf_solicitud_inscripcion"] = bin2hex(random_bytes(32));
        }

        $mensaje = "";
        $tipo = "success";

        if (isset($_POST["solicitudOnline"])) {
            $token = $_POST["csrf"] ?? "";
            if (!is_string($token)
                || !hash_equals($_SESSION["csrf_solicitud_inscripcion"], $token)) {
                $mensaje = "El formulario expiró. Recarga la página e inténtalo de nuevo.";
                $tipo = "danger";
            } else {
                $texto = static function ($campo) {
                    $valor = $_POST[$campo] ?? "";
                    return is_string($valor) ? trim($valor) : "";
                };
                $persona = [
                    "nombre" => $texto("nombre"),
                    "apellido" => $texto("apellido"),
                    "ci" => $texto("ci"),
                    "telefono" => $texto("telefono"),
                    "correo" => $texto("correo"),
                    "username" => $texto("username"),
                    "password" => is_string($_POST["password"] ?? null) ? $_POST["password"] : ""
                ];
                $perfil = [
                    "rude" => $texto("rude"),
                    "fecha_nacimiento" => $texto("fecha_nacimiento"),
                    "genero" => $texto("genero"),
                    "direccion" => $texto("direccion")
                ];
                $idCurso = filter_input(INPUT_POST, "id_curso", FILTER_VALIDATE_INT);
                $idGestion = filter_input(INPUT_POST, "id_gestion", FILTER_VALIDATE_INT);
                $confirmacion = is_string($_POST["confirmar_password"] ?? null)
                    ? $_POST["confirmar_password"]
                    : "";

                if ($persona["nombre"] === "" || $persona["apellido"] === "" || $persona["ci"] === ""
                    || $persona["correo"] === "" || $persona["username"] === "" || $persona["password"] === "") {
                    $mensaje = "Completa los campos obligatorios.";
                    $tipo = "danger";
                } elseif (mb_strlen($persona["nombre"]) > 100 || mb_strlen($persona["apellido"]) > 100) {
                    $mensaje = "El nombre y el apellido no pueden superar los 100 caracteres.";
                    $tipo = "danger";
                } elseif (!preg_match('/^[A-Za-z0-9-]{4,20}$/', $persona["ci"])) {
                    $mensaje = "El CI solo admite letras, números y guion (4 a 20 caracteres).";
                    $tipo = "danger";
                } elseif (!filter_var($persona["correo"], FILTER_VALIDATE_EMAIL) || mb_strlen($persona["correo"]) > 100) {
                    $mensaje = "Escribe un correo electrónico válido.";
                    $tipo = "danger";
                } elseif (!preg_match('/^[A-Za-z0-9._-]{4,40}$/', $persona["username"])) {
                    $mensaje = "El usuario debe tener de 4 a 40 caracteres: letras, números, punto, guion o guion bajo.";
                    $tipo = "danger";
                } elseif (strlen($persona["password"]) < 8 || $persona["password"] !== $confirmacion) {
                    $mensaje = "La contraseña debe tener al menos 8 caracteres y coincidir con su confirmación.";
                    $tipo = "danger";
                } elseif ($persona["telefono"] !== "" && !preg_match('/^[0-9+\s-]{6,20}$/', $persona["telefono"])) {
                    $mensaje = "Escribe un teléfono válido.";
                    $tipo = "danger";
                } elseif ($perfil["rude"] !== "" && !preg_match('/^[A-Za-z0-9-]{5,30}$/', $perfil["rude"])) {
                    $mensaje = "El RUDE solo admite letras, números y guion (5 a 30 caracteres).";
                    $tipo = "danger";
                } elseif ($perfil["fecha_nacimiento"] !== "" && (
                    !($fechaNacimiento = DateTime::createFromFormat("Y-m-d", $perfil["fecha_nacimiento"]))
                    || $fechaNacimiento->format("Y-m-d") !== $perfil["fecha_nacimiento"]
                    || $perfil["fecha_nacimiento"] > date("Y-m-d")
                )) {
                    $mensaje = "La fecha de nacimiento no es válida.";
                    $tipo = "danger";
                } elseif ($perfil["genero"] !== "" && !in_array($perfil["genero"], ["Masculino", "Femenino"], true)) {
                    $mensaje = "Selecciona un género válido.";
                    $tipo = "danger";
                } elseif (mb_strlen($perfil["direccion"]) > 500) {
                    $mensaje = "La dirección no puede superar los 500 caracteres.";
                    $tipo = "danger";
                } elseif (!$idCurso || !$idGestion) {
                    $mensaje = "Selecciona un curso y una gestión activa.";
                    $tipo = "danger";
                } else {
                    $persona["password"] = password_hash($persona["password"], PASSWORD_DEFAULT);
                    foreach (["telefono"] as $campo) {
                        $persona[$campo] = $persona[$campo] !== "" ? $persona[$campo] : null;
                    }
                    foreach ($perfil as $campo => $valor) {
                        $perfil[$campo] = $valor !== "" ? $valor : null;
                    }

                    $resultado = ModeloInscripciones::mdlCrearSolicitudOnline(
                        $persona,
                        $perfil,
                        $idCurso,
                        $idGestion
                    );
                    if ($resultado === true) {
                        $mensaje = "La solicitud se registró correctamente y quedó pendiente de revisión.";
                        $_SESSION["csrf_solicitud_inscripcion"] = bin2hex(random_bytes(32));
                    } elseif ($resultado === "duplicado") {
                        $mensaje = "El CI, correo o nombre de usuario ya está registrado.";
                        $tipo = "warning";
                    } elseif ($resultado === "rude_duplicado") {
                        $mensaje = "Ese RUDE ya pertenece a otro estudiante.";
                        $tipo = "warning";
                    } else {
                        $mensaje = "No se pudo enviar la solicitud. Revisa los datos e inténtalo de nuevo.";
                        $tipo = "danger";
                    }
                }
            }
        }

        return [
            "mensaje" => $mensaje,
            "tipo" => $tipo,
            "csrf" => $_SESSION["csrf_solicitud_inscripcion"],
            "cursos" => ModeloInscripciones::mdlMostrarCursos(),
            "gestiones" => ModeloInscripciones::mdlMostrarGestiones()
        ];
    }

    private function ctrTokenValido() {
        $enviado = $_POST["csrf"] ?? "";
        return !empty($_SESSION["csrf_inscripciones"])
            && is_string($enviado)
            && hash_equals($_SESSION["csrf_inscripciones"], $enviado);
    }

    public function ctrGestionarInscripcion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION["csrf_inscripciones"])) {
            $_SESSION["csrf_inscripciones"] = bin2hex(random_bytes(32));
        }

        $mensaje = "";
        $tipo = "success";

        if (isset($_POST["actualizarEstadoInscripcion"])) {
            $idInscripcion = filter_input(INPUT_POST, "id_inscripcion", FILTER_VALIDATE_INT);
            $mapaEstados = ["aprobada" => "activo", "rechazada" => "rechazado"];
            $estado = is_string($_POST["estado"] ?? null) ? strtolower(trim($_POST["estado"])) : "";

            if (!$this->ctrTokenValido()) {
                $mensaje = "El formulario expiró. Recarga la página e inténtalo de nuevo.";
                $tipo = "danger";
            } elseif (!$idInscripcion || !isset($mapaEstados[$estado])) {
                $mensaje = "No se pudo actualizar el estado de la solicitud.";
                $tipo = "danger";
            } elseif (ModeloInscripciones::mdlActualizarEstadoInscripcion($idInscripcion, $mapaEstados[$estado])) {
                $mensaje = $estado === "aprobada" ? "La inscripción fue aprobada." : "La inscripción fue rechazada.";
            } else {
                $mensaje = "No se pudo actualizar la solicitud (puede que ya haya sido resuelta).";
                $tipo = "danger";
            }
        }

        if (isset($_POST["nuevaInscripcion"])) {
            $idEstudiante = filter_input(INPUT_POST, "id_estudiante", FILTER_VALIDATE_INT);
            $idCurso = filter_input(INPUT_POST, "id_curso", FILTER_VALIDATE_INT);
            $idGestion = filter_input(INPUT_POST, "id_gestion", FILTER_VALIDATE_INT);

            if (!$this->ctrTokenValido()) {
                $mensaje = "El formulario expiró. Recarga la página e inténtalo de nuevo.";
                $tipo = "danger";
            } elseif (!$idEstudiante || !$idCurso || !$idGestion) {
                $mensaje = "Faltan datos para registrar la inscripción.";
                $tipo = "danger";
            } elseif (ModeloInscripciones::mdlExisteInscripcion($idEstudiante, $idCurso, $idGestion)) {
                $mensaje = "Este estudiante ya tiene una inscripción en esta gestión.";
                $tipo = "warning";
            } elseif (ModeloInscripciones::mdlGuardarInscripcion([
                "id_estudiante" => $idEstudiante,
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
            "cursos" => ModeloInscripciones::mdlMostrarCursos(),
            "gestiones" => ModeloInscripciones::mdlMostrarGestiones(),
            "inscripciones" => ModeloInscripciones::mdlMostrarInscripciones(),
            "csrf" => $_SESSION["csrf_inscripciones"]
        ];
    }

    public function ctrMostrarResumenEstudiantes() {
        return [
            "inscritos" => ModeloInscripciones::mdlContarEstudiantesInscritos(),
            "pendientes" => ModeloInscripciones::mdlContarEstudiantesSinInscripcion()
        ];
    }
}
