<?php

class ControladorEstudiantes {

    // Roles que pueden registrar y editar estudiantes y tutores
    const ROLES_PERMITIDOS = ["admin", "director", "secretaria"];

    // Deben coincidir con el ENUM de estudiante.genero
    const GENEROS = ["Masculino", "Femenino"];

    const PARENTESCOS = ["Padre", "Madre", "Abuelo/a", "Tío/a", "Hermano/a", "Tutor legal", "Otro"];

    /*=============================================
    UTILIDADES INTERNAS
    =============================================*/
    private function ctrPuedeGestionar() {
        $rol = strtolower(trim((string) ($_SESSION["rol"] ?? "")));
        return isset($_SESSION["iniciarSesion"])
            && $_SESSION["iniciarSesion"] === "ok"
            && in_array($rol, self::ROLES_PERMITIDOS, true);
    }

    // Token anti-CSRF: la vista lo imprime en cada formulario
    static public function ctrToken() {
        if (empty($_SESSION["csrf_estudiantes"])) {
            $_SESSION["csrf_estudiantes"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf_estudiantes"];
    }

    private function ctrTokenValido() {
        $enviado = $_POST["csrf"] ?? "";
        return !empty($_SESSION["csrf_estudiantes"])
            && is_string($enviado)
            && hash_equals($_SESSION["csrf_estudiantes"], $enviado);
    }

    // Convierte "" en null para guardar NULL en la BD
    private function ctrNulo($valor) {
        return $valor !== "" ? $valor : null;
    }

    // Contraseña temporal de 8 caracteres (se muestra una sola vez al registrar)
    private function ctrGenerarPassword() {
        return bin2hex(random_bytes(4));
    }

    // El usuario se arma con el CI; si ya existe, agrega un número
    private function ctrGenerarUsername($ci) {
        $base = strtolower(preg_replace('/[^A-Za-z0-9._-]/', '', $ci));
        $base = substr(str_pad($base, 4, "0"), 0, 40);

        $username = $base;
        $contador = 2;

        while (ModeloEstudiantes::mdlExisteUsuarioCampo("username", $username) && $contador < 50) {
            $username = $base . $contador;
            $contador++;
        }

        return $username;
    }

    // Si no se indica correo se guarda uno técnico, porque la BD exige correo único
    private function ctrCorreoFinal($persona) {
        return $persona["correo"] !== ""
            ? $persona["correo"]
            : strtolower($persona["ci"]) . "@sin-correo.local";
    }

    /*=============================================
    VALIDACIONES
    Devuelven un texto con el error, o null si todo está bien.
    =============================================*/
    private function ctrValidarPersona($p, $idUsuarioExcluir = 0) {

        if ($p["nombre"] === "" || $p["apellido"] === "" || $p["ci"] === "") {
            return "Completa el nombre, el apellido y el CI.";
        }

        if (mb_strlen($p["nombre"]) > 100 || mb_strlen($p["apellido"]) > 100) {
            return "El nombre y el apellido no pueden superar los 100 caracteres.";
        }

        if (!preg_match('/^[A-Za-z0-9-]{4,20}$/', $p["ci"])) {
            return "El CI solo admite letras, números y guion (4 a 20 caracteres).";
        }

        if ($p["telefono"] !== "" && !preg_match('/^[0-9+\s-]{6,20}$/', $p["telefono"])) {
            return "Escribe un teléfono válido.";
        }

        if ($p["correo"] !== "" && (!filter_var($p["correo"], FILTER_VALIDATE_EMAIL) || mb_strlen($p["correo"]) > 100)) {
            return "Escribe un correo válido o déjalo vacío.";
        }

        if (ModeloEstudiantes::mdlExisteUsuarioCampo("ci", $p["ci"], $idUsuarioExcluir)) {
            return "Ese CI ya está registrado.";
        }

        if ($p["correo"] !== "" && ModeloEstudiantes::mdlExisteUsuarioCampo("correo", $p["correo"], $idUsuarioExcluir)) {
            return "Ese correo ya está registrado.";
        }

        return null;
    }

    private function ctrValidarPerfil($f, $idEstudianteExcluir = 0) {

        if ($f["rude"] !== "") {
            if (!preg_match('/^[A-Za-z0-9-]{5,30}$/', $f["rude"])) {
                return "El RUDE solo admite letras, números y guion (5 a 30 caracteres).";
            }
            if (ModeloEstudiantes::mdlExisteRude($f["rude"], $idEstudianteExcluir)) {
                return "Ese RUDE ya pertenece a otro estudiante.";
            }
        }

        if ($f["fecha_nacimiento"] !== "") {
            $fecha = DateTime::createFromFormat("Y-m-d", $f["fecha_nacimiento"]);
            if (!$fecha || $fecha->format("Y-m-d") !== $f["fecha_nacimiento"]) {
                return "La fecha de nacimiento no es válida.";
            }
            if ($f["fecha_nacimiento"] > date("Y-m-d")) {
                return "La fecha de nacimiento no puede ser futura.";
            }
        }

        if ($f["genero"] !== "" && !in_array($f["genero"], self::GENEROS, true)) {
            return "Selecciona un género válido.";
        }

        if (mb_strlen($f["direccion"]) > 500) {
            return "La dirección no puede superar los 500 caracteres.";
        }

        return null;
    }

    /*=============================================
    LISTA: crear y editar estudiantes
    =============================================*/
    public function ctrGestionarEstudiantes() {
        $mensaje = "";
        $tipo = "success";

        if (isset($_POST["crearEstudiante"])) {

            if (!$this->ctrPuedeGestionar()) {
                $mensaje = "No tienes permiso para registrar estudiantes.";
                $tipo = "danger";
            } elseif (!$this->ctrTokenValido()) {
                $mensaje = "El formulario expiró. Recarga la página e inténtalo de nuevo.";
                $tipo = "danger";
            } else {
                $persona = [
                    "nombre"   => trim($_POST["nuevoNombre"] ?? ""),
                    "apellido" => trim($_POST["nuevoApellido"] ?? ""),
                    "ci"       => trim($_POST["nuevoCi"] ?? ""),
                    "telefono" => trim($_POST["nuevoTelefono"] ?? ""),
                    "correo"   => trim($_POST["nuevoCorreo"] ?? "")
                ];
                $perfil = [
                    "rude"             => trim($_POST["nuevoRude"] ?? ""),
                    "fecha_nacimiento" => trim($_POST["nuevaFechaNacimiento"] ?? ""),
                    "genero"           => trim($_POST["nuevoGenero"] ?? ""),
                    "direccion"        => trim($_POST["nuevaDireccion"] ?? "")
                ];

                $error = $this->ctrValidarPersona($persona) ?? $this->ctrValidarPerfil($perfil);

                if ($error !== null) {
                    $mensaje = $error;
                    $tipo = "danger";
                } else {
                    $password = $this->ctrGenerarPassword();
                    $persona["correo"]   = $this->ctrCorreoFinal($persona);
                    $persona["username"] = $this->ctrGenerarUsername($persona["ci"]);
                    $persona["password"] = password_hash($password, PASSWORD_DEFAULT);
                    $persona["telefono"] = $this->ctrNulo($persona["telefono"]);

                    foreach ($perfil as $clave => $valor) {
                        $perfil[$clave] = $this->ctrNulo($valor);
                    }

                    if (ModeloEstudiantes::mdlCrearEstudiante($persona, $perfil)) {
                        $mensaje = "Estudiante registrado. Usuario: " . $persona["username"]
                            . " · Contraseña temporal: " . $password
                            . ". Anótala ahora, no se volverá a mostrar.";
                    } else {
                        $mensaje = "No se pudo registrar al estudiante. Inténtalo de nuevo.";
                        $tipo = "danger";
                    }
                }
            }
        }

        if (isset($_POST["editarEstudiante"])) {

            $idEstudiante = filter_input(INPUT_POST, "id_estudiante", FILTER_VALIDATE_INT);
            $actual = $idEstudiante ? ModeloEstudiantes::mdlObtenerEstudiante($idEstudiante) : false;

            if (!$this->ctrPuedeGestionar()) {
                $mensaje = "No tienes permiso para editar estudiantes.";
                $tipo = "danger";
            } elseif (!$this->ctrTokenValido()) {
                $mensaje = "El formulario expiró. Recarga la página e inténtalo de nuevo.";
                $tipo = "danger";
            } elseif (!$actual) {
                $mensaje = "El estudiante que intentas editar no existe.";
                $tipo = "danger";
            } else {
                $persona = [
                    "nombre"   => trim($_POST["nombre"] ?? ""),
                    "apellido" => trim($_POST["apellido"] ?? ""),
                    "ci"       => trim($_POST["ci"] ?? ""),
                    "telefono" => trim($_POST["telefono"] ?? ""),
                    "correo"   => ""   // el correo se administra desde Usuarios
                ];
                $perfil = [
                    "rude"             => trim($_POST["rude"] ?? ""),
                    "fecha_nacimiento" => trim($_POST["fecha_nacimiento"] ?? ""),
                    "genero"           => trim($_POST["genero"] ?? ""),
                    "direccion"        => trim($_POST["direccion"] ?? "")
                ];

                $error = $this->ctrValidarPersona($persona, (int) $actual["id_usuario"])
                    ?? $this->ctrValidarPerfil($perfil, (int) $idEstudiante);

                if ($error !== null) {
                    $mensaje = $error;
                    $tipo = "danger";
                } else {
                    $persona["telefono"] = $this->ctrNulo($persona["telefono"]);

                    foreach ($perfil as $clave => $valor) {
                        $perfil[$clave] = $this->ctrNulo($valor);
                    }

                    if (ModeloEstudiantes::mdlEditarEstudiante($idEstudiante, (int) $actual["id_usuario"], $persona, $perfil)) {
                        $mensaje = "Los datos del estudiante se actualizaron.";
                    } else {
                        $mensaje = "No se pudieron actualizar los datos.";
                        $tipo = "danger";
                    }
                }
            }
        }

        return [
            "mensaje"        => $mensaje,
            "tipo"           => $tipo,
            "puedeGestionar" => $this->ctrPuedeGestionar(),
            "generos"        => self::GENEROS,
            "estudiantes"    => ModeloEstudiantes::mdlMostrarEstudiantes()
        ];
    }

    /*=============================================
    TUTORES DE UN ESTUDIANTE
    Devuelve null si el estudiante no existe.
    =============================================*/
    public function ctrGestionarTutores($idEstudiante) {
        $mensaje = "";
        $tipo = "success";

        $estudiante = ModeloEstudiantes::mdlObtenerEstudiante($idEstudiante);

        if (!$estudiante) {
            return null;
        }

        $accion = null;
        foreach (["vincularTutor", "crearTutor", "quitarTutor"] as $nombreAccion) {
            if (isset($_POST[$nombreAccion])) {
                $accion = $nombreAccion;
                break;
            }
        }

        if ($accion !== null) {

            if (!$this->ctrPuedeGestionar()) {
                $mensaje = "No tienes permiso para administrar tutores.";
                $tipo = "danger";
            } elseif (!$this->ctrTokenValido()) {
                $mensaje = "El formulario expiró. Recarga la página e inténtalo de nuevo.";
                $tipo = "danger";
            } elseif ($accion === "vincularTutor") {

                $idTutor = filter_input(INPUT_POST, "id_tutor", FILTER_VALIDATE_INT);
                $parentesco = trim($_POST["parentesco"] ?? "");

                if (!$idTutor || !in_array($parentesco, self::PARENTESCOS, true)) {
                    $mensaje = "Selecciona el tutor y el parentesco.";
                    $tipo = "danger";
                } elseif (!ModeloEstudiantes::mdlEsTutorActivo($idTutor)) {
                    $mensaje = "El usuario seleccionado no es un tutor activo.";
                    $tipo = "danger";
                } elseif (ModeloEstudiantes::mdlExisteVinculo($idTutor, $idEstudiante)) {
                    $mensaje = "Ese tutor ya está vinculado a este estudiante.";
                    $tipo = "warning";
                } elseif (ModeloEstudiantes::mdlVincularTutor($idTutor, $idEstudiante, $parentesco)) {
                    $mensaje = "El tutor se vinculó correctamente.";
                } else {
                    $mensaje = "No se pudo vincular al tutor.";
                    $tipo = "danger";
                }

            } elseif ($accion === "crearTutor") {

                $persona = [
                    "nombre"   => trim($_POST["tutorNombre"] ?? ""),
                    "apellido" => trim($_POST["tutorApellido"] ?? ""),
                    "ci"       => trim($_POST["tutorCi"] ?? ""),
                    "telefono" => trim($_POST["tutorTelefono"] ?? ""),
                    "correo"   => trim($_POST["tutorCorreo"] ?? "")
                ];
                $parentesco = trim($_POST["tutorParentesco"] ?? "");

                $error = $this->ctrValidarPersona($persona);

                if ($error === null && !in_array($parentesco, self::PARENTESCOS, true)) {
                    $error = "Selecciona el parentesco.";
                }

                if ($error !== null) {
                    $mensaje = $error === "Ese CI ya está registrado."
                        ? $error . " Si ya es tutor, vincúlalo desde «Vincular tutor existente»."
                        : $error;
                    $tipo = "danger";
                } else {
                    $password = $this->ctrGenerarPassword();
                    $persona["correo"]   = $this->ctrCorreoFinal($persona);
                    $persona["username"] = $this->ctrGenerarUsername($persona["ci"]);
                    $persona["password"] = password_hash($password, PASSWORD_DEFAULT);
                    $persona["telefono"] = $this->ctrNulo($persona["telefono"]);

                    if (ModeloEstudiantes::mdlCrearTutor($persona, $idEstudiante, $parentesco)) {
                        $mensaje = "Tutor registrado y vinculado. Usuario: " . $persona["username"]
                            . " · Contraseña temporal: " . $password
                            . ". Anótala ahora, no se volverá a mostrar.";
                    } else {
                        $mensaje = "No se pudo registrar al tutor. Inténtalo de nuevo.";
                        $tipo = "danger";
                    }
                }

            } elseif ($accion === "quitarTutor") {

                $idVinculo = filter_input(INPUT_POST, "id_tutor_estudiante", FILTER_VALIDATE_INT);

                if ($idVinculo && ModeloEstudiantes::mdlQuitarTutor($idVinculo, $idEstudiante)) {
                    $mensaje = "Se quitó el vínculo con el tutor.";
                } else {
                    $mensaje = "No se pudo quitar al tutor.";
                    $tipo = "danger";
                }
            }
        }

        return [
            "mensaje"        => $mensaje,
            "tipo"           => $tipo,
            "puedeGestionar" => $this->ctrPuedeGestionar(),
            "parentescos"    => self::PARENTESCOS,
            "estudiante"     => $estudiante,
            "tutores"        => ModeloEstudiantes::mdlMostrarTutoresEstudiante($idEstudiante),
            "disponibles"    => ModeloEstudiantes::mdlMostrarTutoresDisponibles($idEstudiante)
        ];
    }
}
