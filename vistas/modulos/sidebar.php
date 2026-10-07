<?php
// $rutas y $rolActual vienen de vistas/plantilla.php
$rutaActual = (isset($_GET["ruta"]) && is_string($_GET["ruta"])) ? $_GET["ruta"] : "inicio";
?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="index.php?ruta=inicio"><img src="<?php echo BASE_URL; ?>assets/images/logo/logo.png" alt="Logo"></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">

                <?php foreach ($rutas as $clave => $datosRuta): ?>
                    <?php
                    // Solo se muestran las rutas del menú que el rol actual puede abrir
                    if (($datosRuta["menu"] ?? true) === false) {
                        continue;
                    }
                    if (!in_array($rolActual, $datosRuta["roles"], true)) {
                        continue;
                    }
                    ?>
                    <li class="sidebar-item <?php echo $rutaActual === $clave ? 'active' : ''; ?>">
                        <a href="index.php?ruta=<?php echo urlencode($clave); ?>" class="sidebar-link">
                            <i class="bi <?php echo htmlspecialchars($datosRuta["icono"], ENT_QUOTES, "UTF-8"); ?>"></i>
                            <span><?php echo htmlspecialchars($datosRuta["titulo"], ENT_QUOTES, "UTF-8"); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>

            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>