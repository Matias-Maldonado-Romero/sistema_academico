<?php
// 1. Iniciamos la sesión para poder destruirla correctamente (elimina el primer error)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Limpiamos todas las variables de sesión
$_SESSION = array();

// 3. Destruimos la sesión
session_destroy();

// 4. Redireccionamos usando una ruta relativa limpia (evita el error de BASE_URL)
echo '<script>
    window.location = "../../index.php";
</script>';
exit();
?>