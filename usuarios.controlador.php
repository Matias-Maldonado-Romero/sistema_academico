<?php

class ControladorUsuarios {

    private function ctrEsAdministrador() {
        $rol = strtolower(trim((string) ($_SESSION["rol"] ?? "")));
        return isset($_SESSION["iniciarSesion"])
            && $_SESSION["iniciarSesion"] === "ok"
            && in_array($rol, ["admin", "administrador"], true);
    }

    public function ctrIngresoUsuario() {

        if (isset($_POST["ingEmail"])) {

            $tabla = "usuarios";
            $item = "username"; 
            $valor = trim($_POST["ingEmail"]);

            $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

            // Verificamos si el usuario existe 
            if ($respuesta && $respuesta["username"] == $valor) {

                if (strtolower(trim((string) $respuesta["estado"])) !== "activo") {
                    echo '<br><div class="alert alert-danger text-center">Esta cuenta está deshabilitada.</div>';
                    return;
                }

                if ($_POST["ingPassword"] == $respuesta["password"]) {

                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }

                    // Variables de Sesión
                    $_SESSION["iniciarSesion"] = "ok";
                    $_SESSION["id"]            = $respuesta["id_usuario"];
                    $_SESSION["nombre"]        = $respuesta["nombre"];
                    $_SESSION["apellido"]      = $respuesta["apellido"];
                    $_SESSION["username"]      = $respuesta["username"];
                    $_SESSION["rol"]           = isset($respuesta["rol"]) ? $respuesta["rol"] : 'Usuario';

                    echo '<script>
                        window.location = "index.php?ruta=inicio";
                    </script>';

                } else {
                    echo '<br><div class="alert alert-danger text-center">Contraseña incorrecta.</div>';
                }

            } else {
                echo '<br><div class="alert alert-danger text-center">El usuario no existe</div>';
            }
        }
    }

    public function ctrRegistroUsuario() {

        if (!isset($_POST["regUsername"])) {
            return;
        }

        $nombre = trim($_POST["regNombre"] ?? "");
        $apellido = trim($_POST["regApellido"] ?? "");
        $ci = trim($_POST["regCi"] ?? "");
        $correo = trim($_POST["regCorreo"] ?? "");
        $username = trim($_POST["regUsername"] ?? "");
        $password = $_POST["regPassword"] ?? "";
        $confirmacion = $_POST["regConfirmarPassword"] ?? "";
        $sesionAdministrador = $this->ctrEsAdministrador();
        $rolesPermitidos = ["Admin", "Docente", "Secretaria"];
        $rol = $sesionAdministrador ? trim($_POST["regRol"] ?? "Docente") : "Docente";

        if ($nombre === "" || $apellido === "" || $ci === "" || $correo === "" || $username === "" || $password === "") {
            $GLOBALS["registroError"] = "Completa todos los campos.";
            return;
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $GLOBALS["registroError"] = "Escribe un correo válido.";
            return;
        }

        if ($password !== $confirmacion) {
            $GLOBALS["registroError"] = "Las contraseñas no coinciden.";
            return;
        }

        if (!in_array($rol, $rolesPermitidos, true)) {
            $GLOBALS["registroError"] = "Selecciona un rol valido.";
            return;
        }

        $usuarioExistente = ModeloUsuarios::mdlMostrarUsuarios("usuarios", "username", $username);
        if ($usuarioExistente) {
            $GLOBALS["registroError"] = "Ese nombre de usuario ya está registrado.";
            return;
        }

        $ciExistente = ModeloUsuarios::mdlMostrarUsuarios("usuarios", "ci", $ci);
        if ($ciExistente) {
            $GLOBALS["registroError"] = "Ese CI ya está registrado.";
            return;
        }

        $correoExistente = ModeloUsuarios::mdlMostrarUsuarios("usuarios", "correo", $correo);
        if ($correoExistente) {
            $GLOBALS["registroError"] = "Ese correo ya está registrado.";
            return;
        }

        $registro = ModeloUsuarios::mdlRegistrarUsuario([
            "nombre" => $nombre,
            "apellido" => $apellido,
            "ci" => $ci,
            "correo" => $correo,
            "username" => $username,
            "password" => $password,
            "rol" => $rol
        ]);

        if ($registro) {
            $GLOBALS["registroExitoso"] = true;
        } else {
            $GLOBALS["registroError"] = "No se pudo crear la cuenta. Inténtalo de nuevo.";
        }
    }

    public function ctrCrearUsuario() {
        if (!isset($_POST["crearUsuario"])) {
            return;
        }

        if (!$this->ctrEsAdministrador()) {
            $GLOBALS["usuarioMensaje"] = "No tienes permiso para crear usuarios";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        $nombre = trim($_POST["nuevoNombre"] ?? "");
        $apellido = trim($_POST["nuevoApellido"] ?? "");
        $ci = trim($_POST["nuevoCi"] ?? "");
        $correo = trim($_POST["nuevoCorreo"] ?? "");
        $username = trim($_POST["nuevoUsername"] ?? "");
        $password = $_POST["nuevoPassword"] ?? "";
        $rol = trim($_POST["nuevoRol"] ?? "");
        $rolesPermitidos = ["Admin", "Docente", "Secretaria", "Usuario"];

        if ($nombre === "" || $apellido === "" || $ci === "" || $correo === "" || $username === "" || $password === "") {
            $GLOBALS["usuarioMensaje"] = "Completa todos los campos.";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $GLOBALS["usuarioMensaje"] = "Escribe un correo válido.";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if (!in_array($rol, $rolesPermitidos, true)) {
            $GLOBALS["usuarioMensaje"] = "Selecciona un rol válido.";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if (ModeloUsuarios::mdlMostrarUsuarios("usuarios", "username", $username)) {
            $GLOBALS["usuarioMensaje"] = "Ese nombre de usuario ya está registrado.";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if (ModeloUsuarios::mdlMostrarUsuarios("usuarios", "ci", $ci)) {
            $GLOBALS["usuarioMensaje"] = "Ese CI ya está registrado.";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if (ModeloUsuarios::mdlMostrarUsuarios("usuarios", "correo", $correo)) {
            $GLOBALS["usuarioMensaje"] = "Ese correo ya está registrado.";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        $creado = ModeloUsuarios::mdlRegistrarUsuario([
            "nombre" => $nombre,
            "apellido" => $apellido,
            "ci" => $ci,
            "correo" => $correo,
            "username" => $username,
            "password" => $password,
            "rol" => $rol
        ]);

        $GLOBALS["usuarioMensaje"] = $creado
            ? "El usuario se creó correctamente"
            : "No se pudo crear el usuario";
        $GLOBALS["usuarioMensajeTipo"] = $creado ? "success" : "danger";
    }

    public function ctrEditarUsuario() {
        if (!isset($_POST["editarUsuario"])) {
            return;
        }

        if (!$this->ctrEsAdministrador()) {
            $GLOBALS["usuarioMensaje"] = "No tienes permiso para editar usuarios";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        $idUsuario = (int) ($_POST["id_usuario"] ?? 0);
        $nombre = trim($_POST["nombre"] ?? "");
        $apellido = trim($_POST["apellido"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $rol = trim($_POST["rol"] ?? "");
        $rolesPermitidos = ["Admin", "Administrador", "Docente", "Secretaria", "Usuario"];

        if ($idUsuario < 1 || $nombre === "" || $apellido === "" || $correo === "") {
            $GLOBALS["usuarioMensaje"] = "Completa nombre, apellido y correo.";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $GLOBALS["usuarioMensaje"] = "Escribe un correo valido";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if (!in_array($rol, $rolesPermitidos, true)) {
            $GLOBALS["usuarioMensaje"] = "El rol seleccionado no es valido";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        $resultado = ModeloUsuarios::mdlEditarUsuario([
            "id_usuario" => $idUsuario,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "correo" => $correo,
            "rol" => $rol,
            "password" => $_POST["password"] ?? ""
        ]);

        $GLOBALS["usuarioMensaje"] = $resultado
            ? "Los datos del usuario se actualizaron"
            : "No se pudieron actualizar los datos";
        $GLOBALS["usuarioMensajeTipo"] = $resultado ? "success" : "danger";
    }

    public function ctrCambiarEstadoUsuario() {
        if (!isset($_POST["cambiarEstadoUsuario"])) {
            return;
        }

        if (!$this->ctrEsAdministrador()) {
            $GLOBALS["usuarioMensaje"] = "No tienes permiso para cambiar el estado de usuarios";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        $idUsuario = (int) ($_POST["id_usuario"] ?? 0);
        $estado = trim($_POST["estado"] ?? "");
        if ($idUsuario < 1 || !in_array($estado, ["activo", "inactivo"], true)) {
            $GLOBALS["usuarioMensaje"] = "El usuario o estado seleccionado no es valido";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        if ($idUsuario === (int) ($_SESSION["id"] ?? 0) && $estado === "inactivo") {
            $GLOBALS["usuarioMensaje"] = "No puedes deshabilitar tu propia cuenta";
            $GLOBALS["usuarioMensajeTipo"] = "danger";
            return;
        }

        $resultado = ModeloUsuarios::mdlCambiarEstadoUsuario($idUsuario, $estado);
        $GLOBALS["usuarioMensaje"] = $resultado
            ? "El estado del usuario se actualizo"
            : "No se pudo cambiar el estado ";
        $GLOBALS["usuarioMensajeTipo"] = $resultado ? "success" : "danger";
    }
}
?>  