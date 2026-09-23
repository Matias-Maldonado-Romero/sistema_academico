<header class="mb-3 d-flex justify-content-between align-items-center">
    <!-- Botón hamburguesa para dispositivos móviles -->
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <div></div>

    <!-- Botón de Cerrar Sesión con ruta absoluta al archivo salir.php -->
    <div>
        <a href="<?php echo BASE_URL; ?>vistas/modulos/salir.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>
</header>