<?php
// Archivo: controladores/usuarios.controlador.php

class ControladorUsuarios {

    public function ctrIngresoUsuario() {

        if (isset($_POST["ingEmail"])) {

            $tabla = "usuarios";
            $item = "username"; 
            $valor = trim($_POST["ingEmail"]);

            $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

            // Verificamos si el usuario existe 
            if ($respuesta && $respuesta["username"] == $valor) {

                // CAMBIO AQUÍ: Comparamos la contraseña en texto plano directamente
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
                    $_SESSION["rol"]           = isset($respuesta["rol"]) ? $respuesta["rol"] : 'Admin';

                    echo '<script>
                        window.location = "index.php?ruta=inicio";
                    </script>';

                } else {
                    echo '<br><div class="alert alert-danger text-center">Contraseña incorrecta.</div>';
                }

            } else {
                echo '<br><div class="alert alert-danger text-center">El usuario no existe.</div>';
            }
        }
    }
}
?>