<?php
if (session_status() === PHP_SESSION_NONE) {
    session_status(); 
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
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/app.css">
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

                if (isset($_GET["ruta"])) {
                    if (in_array($_GET["ruta"], $rutasPermitidas)) {
                        include "vistas/modulos/" . $_GET["ruta"] . ".php";
                    } else {
                        include "vistas/modulos/404.php";
                    }
                } else {
                    include "vistas/modulos/inicio.php";
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

</body>
</html>