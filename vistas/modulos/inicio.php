<?php $resumenEstudiantes = (new ControladorInscripciones())->ctrMostrarResumenEstudiantes(); ?>

<div class="page-heading home-heading">
    <div>
        <span class="home-eyebrow">UNIDAD EDUCATIVA</span>
        <h3>Bienvenidos a nuestra institución</h3>
        <p>Una comunidad educativa enfocada en formar estudiantes responsables, capaces y con valores.</p>
    </div>
    <div class="home-date">
        <i class="bi bi-calendar3" aria-hidden="true"></i>
        <span>Hoy <strong><?php echo date("d/m/Y"); ?></strong></span>
    </div>
</div>

<div class="page-content home-dashboard">
    <section class="home-welcome" aria-labelledby="welcome-title">
        <div class="home-welcome-copy">
            <span class="home-welcome-label">INSTITUTO EDUCATIVO</span>
            <h1 id="welcome-title">Formamos jóvenes con excelencia y disciplina.</h1>
            <p>En esta institución promovemos el aprendizaje, la conducta escolar y el crecimiento integral de cada estudiante.</p>
            <div class="d-flex gap-3 mt-4 flex-wrap">
                <a href="index.php?ruta=inscripciones#form-inscripcion" class="btn btn-primary btn-lg">
                    <i class="bi bi-pencil-square me-2" aria-hidden="true"></i>Inscribirse
                </a>
                <a href="index.php?ruta=inscripciones" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-file-earmark-text me-2" aria-hidden="true"></i>Ver solicitudes
                </a>
            </div>
        </div>
    </section>

    <div class="home-section-heading">
        <div>
            <span class="home-eyebrow">ESTADO ACADÉMICO</span>
            <h4>Resumen de estudiantes</h4>
        </div>
        <span class="home-section-note">Gestiones activas</span>
    </div>

    <section class="home-stats-grid" aria-label="Resumen de estudiantes en gestiones activas">
        <article class="home-stat home-stat-enrolled">
            <span class="home-stat-icon"><i class="bi bi-person-check-fill" aria-hidden="true"></i></span>
            <div>
                <span class="home-stat-label">Estudiantes inscritos</span>
                <strong class="home-stat-value"><?php echo (int) $resumenEstudiantes["inscritos"]; ?></strong>
                <span class="home-stat-detail">Con inscripción en una gestión activa</span>
            </div>
        </article>
        <article class="home-stat home-stat-pending">
            <span class="home-stat-icon"><i class="bi bi-person-exclamation" aria-hidden="true"></i></span>
            <div>
                <span class="home-stat-label">Pendientes de inscripción</span>
                <strong class="home-stat-value"><?php echo (int) $resumenEstudiantes["pendientes"]; ?></strong>
                <span class="home-stat-detail">Registrados sin inscripción activa</span>
            </div>
        </article>
    </section>
</div>