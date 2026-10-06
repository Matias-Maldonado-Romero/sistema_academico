<?php
$materias = [
    "Matemática",
    "Lengua y Literatura",
    "Ciencias Naturales",
    "Ciencias Sociales",
    "Inglés",
    "Educación Física"
];

$nombresEstudiantes = [
    "Ana Martínez",
    "Mateo González",
    "Sofía Rodríguez",
    "Lucas Fernández",
    "Valentina López",
    "Thiago Pérez",
    "Camila Sánchez",
    "Benjamín Ramírez",
    "Isabella Torres",
    "Joaquín Flores",
    "Martina Acosta",
    "Santiago Ruiz",
    "Emilia Díaz",
    "Tomás Castro",
    "Mía Herrera",
    "Nicolás Medina",
    "Julieta Romero",
    "Franco Silva"
];

$coloresCursos = ["primary", "success", "info", "warning", "danger", "secondary"];
$cursos = [];
$indiceCurso = 0;

for ($grado = 1; $grado <= 6; $grado++) {
    foreach (["A", "B", "C"] as $indiceAula => $aula) {
        $alumnos = [];
        $cantidadAlumnos = 2 + (($grado + $indiceAula) % 2);

        for ($indiceAlumno = 0; $indiceAlumno < $cantidadAlumnos; $indiceAlumno++) {
            $indiceNombre = ($indiceCurso * 3 + $indiceAlumno) % count($nombresEstudiantes);
            $notas = [];

            foreach ($materias as $indiceMateria => $materia) {
                $notas[$materia] = 60 + (($indiceCurso * 7 + $indiceAlumno * 11 + $indiceMateria * 5) % 41);
            }

            $alumnos[] = [
                "nombre" => $nombresEstudiantes[$indiceNombre],
                "notas" => $notas
            ];
        }

        $cursos[] = [
            "nombre" => $grado . "° Secundaria",
            "aula" => $aula,
            "color" => $coloresCursos[($indiceAula + $grado - 1) % count($coloresCursos)],
            "alumnos" => $alumnos
        ];
        $indiceCurso++;
    }
}
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 order-md-1 order-last">
                <h3>Cursos y calificaciones</h3>
                <p class="text-subtitle text-muted">Alumnos organizados por curso y aula. Las calificaciones mostradas son datos de muestra.</p>
            </div>
            <div class="col-12 col-md-4 order-md-2 order-first text-md-end mb-3 mb-md-0">
                <span class="badge bg-light-primary text-primary"><?php echo count($cursos); ?> cursos</span>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        <div class="row g-4">
            <?php foreach ($cursos as $curso): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm course-card">
                        <div class="card-header bg-<?php echo htmlspecialchars($curso["color"], ENT_QUOTES, "UTF-8"); ?> text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white"><?php echo htmlspecialchars($curso["nombre"], ENT_QUOTES, "UTF-8"); ?></h5>
                            <span class="badge bg-white text-dark">Aula <?php echo htmlspecialchars($curso["aula"], ENT_QUOTES, "UTF-8"); ?></span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Alumno</th>
                                            <th scope="col" class="text-end">Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($curso["alumnos"] as $alumno): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($alumno["nombre"], ENT_QUOTES, "UTF-8"); ?></td>
                                                <td class="text-end">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary btn-ver-notas"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalNotasAlumno"
                                                        data-estudiante="<?php echo htmlspecialchars($alumno["nombre"], ENT_QUOTES, "UTF-8"); ?>"
                                                        data-curso="<?php echo htmlspecialchars($curso["nombre"] . " - Aula " . $curso["aula"], ENT_QUOTES, "UTF-8"); ?>"
                                                        data-notas="<?php echo htmlspecialchars((string) json_encode($alumno["notas"], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, "UTF-8"); ?>"
                                                    >
                                                        Ver notas
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<div class="modal fade" id="modalNotasAlumno" tabindex="-1" aria-labelledby="tituloModalNotas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="tituloModalNotas">Calificaciones del alumno</h5>
                    <p class="text-muted small mb-0" id="cursoModalNotas"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <h6 id="nombreModalNotas" class="mb-3"></h6>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Materia</th>
                                <th scope="col" class="text-end">Nota</th>
                            </tr>
                        </thead>
                        <tbody id="tablaNotasAlumno"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
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

<script>
    document.querySelectorAll(".btn-ver-notas").forEach(function (boton) {
        boton.addEventListener("click", function () {
            const notas = JSON.parse(this.dataset.notas);
            const cuerpoTabla = document.getElementById("tablaNotasAlumno");

            document.getElementById("nombreModalNotas").textContent = this.dataset.estudiante;
            document.getElementById("cursoModalNotas").textContent = this.dataset.curso;
            cuerpoTabla.replaceChildren();

            Object.entries(notas).forEach(function ([materia, nota]) {
                const fila = document.createElement("tr");
                const celdaMateria = document.createElement("td");
                const celdaNota = document.createElement("td");

                celdaMateria.textContent = materia;
                celdaNota.textContent = nota;
                celdaNota.className = "text-end fw-semibold";
                fila.append(celdaMateria, celdaNota);
                cuerpoTabla.appendChild(fila);
            });
        });
    });
</script>
