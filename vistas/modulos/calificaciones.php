<?php
$cursos = ModeloInscripciones::mdlMostrarCursos();
$coloresCursos = ["primary", "success", "info", "warning", "danger", "secondary"];
$ordinales = [
    "1" => "Primero",
    "2" => "Segundo",
    "3" => "Tercero",
    "4" => "Cuarto",
    "5" => "Quinto",
    "6" => "Sexto",
];
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 order-md-1 order-last">
                <h3>Cursos y calificaciones</h3>
                <p class="text-subtitle text-muted">Listado de estudiantes aprobados en cada curso de secundaria de la gestión activa.</p>
            </div>
            <div class="col-12 col-md-4 order-md-2 order-first text-md-end mb-3 mb-md-0">
                <span class="badge bg-light-primary text-primary"><?php echo count($cursos); ?> cursos</span>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        <?php if (empty($cursos)): ?>
            <div class="alert alert-danger" role="alert">
                No se pudieron cargar los cursos. Revisa la conexión y la estructura de la base de datos.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($cursos as $indiceCurso => $curso): ?>
                    <?php
                    $estudiantes = ModeloInscripciones::mdlMostrarEstudiantesEnCurso($curso["id_curso"]);
                    $color = $coloresCursos[$indiceCurso % count($coloresCursos)];
                    $numero = trim($curso["nombre_curso"]);
                    $nombreMostrar = $ordinales[$numero] ?? $numero;
                    ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 shadow-sm course-card">
                            <div class="card-header bg-<?php echo htmlspecialchars($color, ENT_QUOTES, "UTF-8"); ?> text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white"><?php echo htmlspecialchars($nombreMostrar, ENT_QUOTES, "UTF-8"); ?></h5>
                                <span class="badge bg-white text-dark">Paralelo <?php echo htmlspecialchars($curso["paralelo"], ENT_QUOTES, "UTF-8"); ?></span>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                    <span class="text-muted fw-semibold">Estudiantes aprobados</span>
                                    <span class="badge bg-light-primary text-primary fs-6 px-3 py-2"><?php echo count($estudiantes); ?></span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr><th scope="col">Estudiante</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($estudiantes)): ?>
                                                <tr><td class="text-center text-muted py-3">Aún no hay estudiantes aprobados en este curso.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($estudiantes as $estudiante): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($estudiante["apellido"] . ", " . $estudiante["nombre"], ENT_QUOTES, "UTF-8"); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<style>
    .course-card {
        border: 0;
        border-radius: 12px;
        overflow: hidden;
    }

    .course-card .card-header {
        min-height: 62px;
    }

    .course-card .table th,
    .course-card .table td {
        vertical-align: middle;
    }
</style>
