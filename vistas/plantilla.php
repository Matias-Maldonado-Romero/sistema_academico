<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rutas y roles permitidos (config/rutas.php). También las usa el sidebar.
$rutas = require "config/rutas.php";
$rolActual = strtolower(trim((string) ($_SESSION["rol"] ?? "")));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Académico - Instituto Tecnológico</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/app.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/pages/inicio.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/favicon.svg" type="image/x-icon">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/pages/auth.css">
</head>
<body>

    <?php
    if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {
        
        echo '<div id="app" class="app-wrapper">';

            include "vistas/modulos/sidebar.php";
            
            echo '<div id="main" class="main-wrapper">';
                
                include "vistas/modulos/cabecera.php";

                $rutaSolicitada = $_GET["ruta"] ?? "inicio";

                if (!is_string($rutaSolicitada)
                    || !isset($rutas[$rutaSolicitada])
                    || !is_file("vistas/modulos/" . $rutaSolicitada . ".php")) {

                    // La ruta no existe o no está registrada en config/rutas.php
                    include "vistas/modulos/404.php";

                } elseif ($rutaSolicitada !== "salir"
                    && !in_array($rolActual, $rutas[$rutaSolicitada]["roles"], true)) {

                    // La ruta existe, pero este rol no puede entrar.
                    // Como los controladores corren dentro de la vista, esto también
                    // bloquea los POST: nadie ejecuta acciones de un módulo que no puede ver.
                    include "vistas/modulos/403.php";

                } else {
                    include "vistas/modulos/" . $rutaSolicitada . ".php";
                }

                include "vistas/modulos/footer.php";

            echo '</div>'; 

        echo '</div>'; 

    } else {
        if (isset($_GET["ruta"]) && $_GET["ruta"] === "registro") {
            include "vistas/modulos/registro.php";
        } else {
            include "vistas/modulos/login.php";
        }
    }
    ?>

    <script src="<?php echo BASE_URL; ?>assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/vendors/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/confirmaciones.js"></script>

</body>
</html>