<?php
// Asegúrate de que la sesión esté iniciada al principio de todo
if (session_status() === PHP_SESSION_NONE) {
    session_status(); // o session_start(); según lo tengas
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Académico - Instituto Tecnológico</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Global (¡AHORA SÍ CARGAN SIEMPRE, INCLUYENDO EL LOGIN!) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/app.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/favicon.svg" type="image/x-icon">
    
    <!-- CSS específico para la página de Login (Mazer) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/pages/auth.css">
</head>
<body>

    <?php
    if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {
        
        echo '<div id="app" class="app-wrapper">';

            // 1. Menú Lateral
            include "vistas/modulos/sidebar.php";
            
            // 2. Contenedor del contenido dinámico
            echo '<div id="main" class="main-wrapper">';
                
                // Cabecera superior interna
                include "vistas/modulos/cabecera.php";

                // Lista blanca de módulos permitidos (Seguridad contra LFI)
                $rutasPermitidas = [
                    "inicio",
                    "usuarios",
                    "carreras",
                    "materias",
                    "grupos",
                    "inscripciones",
                    "calificaciones",
                    "salir"
                ];

                // Enrutamiento modular
                if (isset($_GET["ruta"])) {
                    if (in_array($_GET["ruta"], $rutasPermitidas)) {
                        include "vistas/modulos/" . $_GET["ruta"] . ".php";
                    } else {
                        include "vistas/modulos/404.php";
                    }
                } else {
                    include "vistas/modulos/inicio.php";
                }

                // Pie de página
                include "vistas/modulos/footer.php";

            echo '</div>'; // Cierre .main-wrapper

        echo '</div>'; // Cierre #app

    } else {
        // Usuario no autenticado: Carga el módulo de login limpio dentro de esta estructura
        include "vistas/modulos/login.php";
    }
    ?>

    <!-- SCRIPTS GLOBALES -->
    <script src="<?php echo BASE_URL; ?>assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

</body>
</html>