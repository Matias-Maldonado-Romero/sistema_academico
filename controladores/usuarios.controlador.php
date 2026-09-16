<?php
// Archivo: controladores/usuarios.controlador.php

class ControladorUsuarios {

    /*=============================================
    INGRESO DE USUARIO (LOGIN)
    =============================================*/
    public function ctrIngresoUsuario() {

        if (isset($_POST["ingEmail"])) {

            // Validar que el correo tenga un formato válido y no contenga caracteres extraños
            if (filter_var($_POST["ingEmail"], FILTER_VALIDATE_EMAIL)) {

                $tabla = "usuarios";
                $item = "email";
                $valor = $_POST["ingEmail"];

                // Solicitamos la información al Modelo
                $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

                // 1. Verificamos si el usuario existe y está activo (estado = 1)
                if ($respuesta && $respuesta["email"] == $_POST["ingEmail"] && $respuesta["estado"] == 1) {

                    // 2. Verificamos la contraseña encriptada con password_verify()
                    if (password_verify($_POST["ingPassword"], $respuesta["password"])) {

                        // Variables de Sesión
                        $_SESSION["iniciarSesion"] = "ok";
                        $_SESSION["id"]            = $respuesta["id"];
                        $_SESSION["nombre"]        = $respuesta["nombre"];
                        $_SESSION["apellido"]      = $respuesta["apellido"];
                        $_SESSION["email"]         = $respuesta["email"];
                        $_SESSION["rol"]           = $respuesta["rol"];

                        // Redireccionamos a la página de inicio
                        echo '<script>
                            window.location = "inicio";
                        </script>';

                    } else {
                        echo '<br><div class="alert alert-danger text-center">Contraseña incorrecta.</div>';
                    }

                } else {
                    echo '<br><div class="alert alert-danger text-center">El usuario no existe o está inactivo.</div>';
                }

            } else {
                echo '<br><div class="alert alert-warning text-center">Formato de correo no válido.</div>';
            }
        }
    }
}
