<?php
$gruposMateria = [
    [
        "materia" => "Matemáticas",
        "descripcion" => "Repaso de ejercicios, dudas y tareas.",
        "color" => "primary",
        "icono" => "bi-calculator",
        "link" => "https://chat.whatsapp.com/ABC123XYZ"
    ],
    [
        "materia" => "Física",
        "descripcion" => "Temas, fórmulas y apoyo para exámenes.",
        "color" => "success",
        "icono" => "bi-atom",
        "link" => "https://chat.whatsapp.com/DEF456XYZ"
    ],
    [
        "materia" => "Química",
        "descripcion" => "Apuntes, prácticas y resolución de dudas.",
        "color" => "warning",
        "icono" => "bi-flask",
        "link" => "https://chat.whatsapp.com/GHI789XYZ"
    ],
    [
        "materia" => "Programación",
        "descripcion" => "Código, proyectos y ayuda técnica.",
        "color" => "info",
        "icono" => "bi-code-slash",
        "link" => "https://chat.whatsapp.com/JKL012XYZ"
    ],
    [
        "materia" => "Inglés",
        "descripcion" => "Práctica oral, tareas y vocabulario.",
        "color" => "danger",
        "icono" => "bi-globe",
        "link" => "https://chat.whatsapp.com/MNO345XYZ"
    ],
    [
        "materia" => "Historia",
        "descripcion" => "Material de estudio y discusión de temas.",
        "color" => "secondary",
        "icono" => "bi-book",
        "link" => "https://chat.whatsapp.com/PQR678XYZ"
    ],
];
?>
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Grupos de materias</h3>
                <p class="text-subtitle text-muted">Únete a los grupos de WhatsApp de cada materia y consulta avisos, tareas y apoyo académico.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first text-md-end mb-3 mb-md-0">
                <span class="badge bg-light-primary text-primary"><?php echo count($gruposMateria); ?> materias</span>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        <div class="row g-4">
            <?php foreach ($gruposMateria as $grupo): ?>
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="<?php echo htmlspecialchars($grupo["link"], ENT_QUOTES, "UTF-8"); ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm text-white bg-<?php echo htmlspecialchars($grupo["color"], ENT_QUOTES, "UTF-8"); ?> group-card">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="icon-box border border-white border-opacity-25 rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi <?php echo htmlspecialchars($grupo["icono"], ENT_QUOTES, "UTF-8"); ?> fs-4"></i>
                                        </span>
                                        <span class="badge bg-white text-dark px-2 py-1">WhatsApp</span>
                                    </div>
                                    <h4 class="card-title mb-2"><?php echo htmlspecialchars($grupo["materia"], ENT_QUOTES, "UTF-8"); ?></h4>
                                    <p class="mb-0 opacity-75"><?php echo htmlspecialchars($grupo["descripcion"], ENT_QUOTES, "UTF-8"); ?></p>
                                </div>
                                <div class="mt-4 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Entrar al grupo</span>
                                    <i class="bi bi-arrow-up-right-circle fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<style>
    .group-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        min-height: 220px;
        border-radius: 18px;
        overflow: hidden;
    }

    .group-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18) !important;
    }

    .icon-box {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        background: rgba(255, 255, 255, 0.15);
    }

    .icon-box .bi {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        line-height: 1;
    }

    .group-card .card-title {
        font-size: 1.4rem;
        font-weight: 700;
    }
</style>