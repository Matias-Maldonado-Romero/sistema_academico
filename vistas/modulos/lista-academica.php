<?php
$tablaAcademica = ModeloListas::mdlMostrarTablaAcademica($listaAcademica["tabla"]);
?>
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?php echo htmlspecialchars($listaAcademica["titulo"], ENT_QUOTES, "UTF-8"); ?></h3>
                <p class="text-subtitle text-muted"><?php echo htmlspecialchars($listaAcademica["descripcion"], ENT_QUOTES, "UTF-8"); ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first text-md-end mb-3 mb-md-0">
                <span class="badge bg-light-secondary text-secondary"><?php echo count($tablaAcademica["registros"]); ?> registros</span>
            </div>
        </div>
    </div>
</div>
<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><?php echo htmlspecialchars($listaAcademica["titulo"], ENT_QUOTES, "UTF-8"); ?></h4>
            </div>
            <div class="card-body">
                <?php if (!$tablaAcademica["disponible"]): ?>
                    <div class="alert alert-light-info mb-0" role="status">
                        No se encontró la tabla <strong><?php echo htmlspecialchars($listaAcademica["tabla"], ENT_QUOTES, "UTF-8"); ?></strong> en la base de datos seleccionada.
                    </div>
                <?php elseif (empty($tablaAcademica["columnas"])): ?>
                    <p class="text-muted mb-0">La tabla no contiene columnas para mostrar.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <?php foreach ($tablaAcademica["columnas"] as $columna): ?>
                                        <th scope="col"><?php echo htmlspecialchars(ucwords(str_replace("_", " ", $columna)), ENT_QUOTES, "UTF-8"); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tablaAcademica["registros"])): ?>
                                    <tr><td colspan="<?php echo count($tablaAcademica["columnas"]); ?>" class="text-center text-muted py-4">No hay registros todavía.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($tablaAcademica["registros"] as $registro): ?>
                                        <tr>
                                            <?php foreach ($tablaAcademica["columnas"] as $columna): ?>
                                                <td><?php echo htmlspecialchars((string) ($registro[$columna] ?? "—"), ENT_QUOTES, "UTF-8"); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>