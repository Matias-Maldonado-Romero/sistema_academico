<header class="mb-3 d-flex justify-content-between align-items-center">
    <button
        type="button"
        class="burger-btn btn btn-link d-block p-0"
        aria-label="Mostrar u ocultar el menú"
        aria-controls="sidebar"
        aria-expanded="true">
        <i class="bi bi-justify fs-3" aria-hidden="true"></i>
    </button>

    <div class="d-flex align-items-center gap-2 me-auto ms-3">
        <span class="fw-semibold">
            <?php echo htmlspecialchars((string) ($_SESSION["username"] ?? ""), ENT_QUOTES, "UTF-8"); ?>
        </span>
        <span class="badge bg-secondary">
            <?php echo htmlspecialchars(ucfirst(strtolower((string) ($_SESSION["rol"] ?? ""))), ENT_QUOTES, "UTF-8"); ?>
        </span>
    </div>

    <div>
        <a href="<?php echo BASE_URL; ?>vistas/modulos/salir.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>
</header>