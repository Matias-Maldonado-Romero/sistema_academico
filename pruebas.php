<?php
/**
 * SCRIPT DE PRUEBAS - Sistema Académico
 * 
 * Cópialo a la raíz del proyecto (mismo nivel que index.php)
 * Accede a: http://localhost/tu-proyecto/pruebas.php
 * 
 * Verifica:
 * - Conexión a base de datos
 * - Existencia y contenido de tablas
 * - Datos problemáticos o inconsistentes
 * - Enums y estados disponibles
 * - Permisos de roles
 */

// Cargar configuración y clase de conexión
require_once "config/database.php";

$conexion = Database::getConnection();
$errores = [];
$advertencias = [];
$info = [];

// ============================================================
// 1. PRUEBA DE CONEXIÓN
// ============================================================
if (!$conexion) {
    $errores[] = "❌ No se puede conectar a la base de datos. Verifica config/database.php";
    die(renderHTML($errores, $advertencias, $info));
}
$info[] = "✅ Conexión a la base de datos: OK";

// ============================================================
// 2. VERIFICAR TABLAS
// ============================================================
$tablasRequeridas = [
    "usuarios", "docente", "estudiante", "tutor_estudiante",
    "curso", "materia", "gestion",
    "curso_docente", "inscripcion", "nota"
];

$tablasExistentes = [];
try {
    $result = $conexion->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()");
    $tablasExistentes = array_map(function($row) { return $row["TABLE_NAME"]; }, $result->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    $errores[] = "❌ No se pudo leer el esquema de la BD: " . $e->getMessage();
}

foreach ($tablasRequeridas as $tabla) {
    if (in_array($tabla, $tablasExistentes, true)) {
        $stmt = $conexion->query("SELECT COUNT(*) FROM $tabla");
        $count = (int) $stmt->fetchColumn();
        $info[] = "✅ Tabla `$tabla`: OK ($count registros)";
    } else {
        $errores[] = "❌ Tabla `$tabla`: NO EXISTE";
    }
}

// ============================================================
// 3. USUARIOS - Problemas comunes
// ============================================================
$info[] = "<strong>👥 USUARIOS</strong>";

$stmt = $conexion->query("SELECT id_usuario, nombre, apellido, ci, username, rol, estado FROM usuarios ORDER BY id_usuario");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $u) {
    $problemas = [];

    if (empty($u["ci"])) {
        $problemas[] = "sin CI";
    }
    if (empty($u["username"])) {
        $problemas[] = "sin username";
    }
    if (!in_array($u["rol"], ["admin", "director", "secretaria", "docente", "tutor", "estudiante"], true)) {
        $problemas[] = "rol inválido: " . $u["rol"];
    }
    if (!in_array($u["estado"], ["activo", "inactivo"], true)) {
        $problemas[] = "estado inválido: " . $u["estado"];
    }

    if (!empty($problemas)) {
        $advertencias[] = "⚠️ Usuario #{$u["id_usuario"]} ({$u["nombre"]} {$u["apellido"]}): " . implode(", ", $problemas);
    }
}

if (empty($usuarios)) {
    $errores[] = "❌ No hay usuarios. Crea al menos un admin en config/database.php o importa datos.";
} else {
    $info[] = "✅ Total de usuarios: " . count($usuarios);
}

// ============================================================
// 4. DOCENTES Y ESTUDIANTES - Huérfanos o desvinculados
// ============================================================
$info[] = "<strong>👨‍🏫 DOCENTES</strong>";

$stmt = $conexion->query(
    "SELECT d.id_docente, d.id_usuario, u.nombre, u.apellido
     FROM docente d
     LEFT JOIN usuarios u ON u.id_usuario = d.id_usuario
     ORDER BY d.id_docente"
);
$docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($docentes)) {
    $advertencias[] = "⚠️ No hay docentes. Crea usuarios con rol 'docente'.";
} else {
    $docentesSinUsuario = array_filter($docentes, fn($d) => $d["id_usuario"] === null);
    if (!empty($docentesSinUsuario)) {
        $ids = array_column($docentesSinUsuario, "id_docente");
        $errores[] = "❌ Docentes sin usuario vinculado: IDs " . implode(", ", $ids);
    } else {
        $info[] = "✅ Total de docentes: " . count($docentes) . " (todos con usuario vinculado)";
    }
}

$info[] = "<strong>🎓 ESTUDIANTES</strong>";

$stmt = $conexion->query(
    "SELECT e.id_estudiante, e.id_usuario, u.nombre, u.apellido, e.rude
     FROM estudiante e
     LEFT JOIN usuarios u ON u.id_usuario = e.id_usuario
     ORDER BY e.id_estudiante"
);
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($estudiantes)) {
    $advertencias[] = "⚠️ No hay estudiantes. Crea usuarios con rol 'estudiante' en el módulo de Usuarios.";
} else {
    $estudiantesSinUsuario = array_filter($estudiantes, fn($e) => $e["id_usuario"] === null);
    if (!empty($estudiantesSinUsuario)) {
        $ids = array_column($estudiantesSinUsuario, "id_estudiante");
        $errores[] = "❌ Estudiantes sin usuario vinculado: IDs " . implode(", ", $ids);
    }

    $estudiantessinRUDE = array_filter($estudiantes, fn($e) => empty($e["rude"]));
    if (!empty($estudiantessinRUDE)) {
        $advertencias[] = "⚠️ " . count($estudiantessinRUDE) . " estudiantes sin RUDE. Completa en el módulo de Estudiantes.";
    }

    $info[] = "✅ Total de estudiantes: " . count($estudiantes);
}

// ============================================================
// 5. CURSOS
// ============================================================
$info[] = "<strong>📚 CURSOS</strong>";

$stmt = $conexion->query(
    "SELECT id_curso, nombre_curso, paralelo, cupo_maximo, estado FROM curso ORDER BY nombre_curso, paralelo"
);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cursos)) {
    $errores[] = "❌ No hay cursos. Importa datos o crea algunos.";
} else {
    $info[] = "✅ Total de cursos: " . count($cursos);

    foreach ($cursos as $c) {
        if ($c["estado"] != 1) {
            $advertencias[] = "⚠️ Curso {$c["nombre_curso"]} {$c["paralelo"]}: inactivo";
        }
    }
}

// ============================================================
// 6. MATERIAS
// ============================================================
$info[] = "<strong>📖 MATERIAS</strong>";

$stmt = $conexion->query("SELECT id_materia, nombre_materia, estado FROM materia ORDER BY nombre_materia");
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($materias)) {
    $errores[] = "❌ No hay materias. Importa datos o crea algunas.";
} else {
    $info[] = "✅ Total de materias: " . count($materias);
}

// ============================================================
// 7. GESTIONES
// ============================================================
$info[] = "<strong>📅 GESTIONES</strong>";

$stmt = $conexion->query("SELECT id_gestion, `año`, fecha_inicio, fecha_fin, estado FROM gestion ORDER BY `año` DESC");
$gestiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($gestiones)) {
    $errores[] = "❌ No hay gestiones. Crea al menos una.";
} else {
    $info[] = "✅ Total de gestiones: " . count($gestiones);

    $activasCount = count(array_filter($gestiones, fn($g) => $g["estado"] === "activa"));
    if ($activasCount === 0) {
        $errores[] = "❌ No hay gestiones activas. Cambia el estado de al menos una a 'activa'.";
    } elseif ($activasCount > 1) {
        $advertencias[] = "⚠️ Hay " . $activasCount . " gestiones activas. Idealmente solo debe haber una.";
    } else {
        $gestionActiva = array_values(array_filter($gestiones, fn($g) => $g["estado"] === "activa"))[0];
        $info[] = "✅ Una gestión activa: " . $gestionActiva["año"];
    }
}

// ============================================================
// 8. ASIGNACIONES DOCENTE-MATERIA-CURSO
// ============================================================
$info[] = "<strong>👨‍🏫📚 ASIGNACIONES (DOCENTE-CURSO-MATERIA)</strong>";

$stmt = $conexion->query(
    "SELECT COUNT(*) FROM curso_docente WHERE estado = 1"
);
$asignacionesCount = (int) $stmt->fetchColumn();

if ($asignacionesCount === 0) {
    $advertencias[] = "⚠️ No hay asignaciones de docentes a cursos/materias. Crea algunas para que los docentes puedan calificar.";
} else {
    $info[] = "✅ Total de asignaciones activas: " . $asignacionesCount;
}

// ============================================================
// 9. INSCRIPCIONES
// ============================================================
$info[] = "<strong>✍️  INSCRIPCIONES</strong>";

$stmt = $conexion->query(
    "SELECT COUNT(*) FROM inscripcion"
);
$inscripcionesCount = (int) $stmt->fetchColumn();

if ($inscripcionesCount === 0) {
    $info[] = "✅ No hay inscripciones aún. Registra estudiantes cuando estés listo.";
} else {
    $info[] = "✅ Total de inscripciones: " . $inscripcionesCount;

    // Verificar inconsistencias
    $stmt = $conexion->query(
        "SELECT i.id_inscripcion, i.id_estudiante, i.id_curso, i.id_gestion
         FROM inscripcion i
         LEFT JOIN estudiante e ON e.id_estudiante = i.id_estudiante
         LEFT JOIN curso c ON c.id_curso = i.id_curso
         LEFT JOIN gestion g ON g.id_gestion = i.id_gestion
         WHERE e.id_estudiante IS NULL OR c.id_curso IS NULL OR g.id_gestion IS NULL"
    );
    $inconsistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($inconsistentes)) {
        $ids = array_column($inconsistentes, "id_inscripcion");
        $errores[] = "❌ Inscripciones con referencias rotas: IDs " . implode(", ", $ids);
    }
}

// ============================================================
// 10. CALIFICACIONES
// ============================================================
$info[] = "<strong>📊 CALIFICACIONES (NOTAS)</strong>";

$stmt = $conexion->query("SELECT COUNT(*) FROM nota");
$notasCount = (int) $stmt->fetchColumn();

if ($notasCount === 0) {
    $info[] = "✅ No hay calificaciones aún. Se registrarán cuando los docentes carguen notas.";
} else {
    $info[] = "✅ Total de notas: " . $notasCount;
}

// ============================================================
// 11. RESUMEN Y RECOMENDACIONES
// ============================================================
$info[] = "<strong>📋 RESUMEN</strong>";

if (empty($errores)) {
    if (empty($advertencias)) {
        $info[] = "✅ <strong>Todo está bien configurado. Puedes empezar a usar el sistema.</strong>";
    } else {
        $info[] = "⚠️ <strong>El sistema funciona, pero revisa las advertencias de arriba.</strong>";
    }
} else {
    $info[] = "❌ <strong>Hay errores que impiden usar el sistema. Corrígelos primero.</strong>";
}

// ============================================================
// 12. DATOS DE INICIO RÁPIDO
// ============================================================
$info[] = "<strong>🚀 DATOS PARA PROBAR</strong>";

$adminUser = $conexion->query(
    "SELECT username FROM usuarios WHERE rol = 'admin' AND estado = 'activo' LIMIT 1"
)->fetchColumn();

if ($adminUser) {
    $info[] = "✅ Usuario admin: <code>$adminUser</code>";
} else {
    $advertencias[] = "⚠️ No hay usuario admin activo. Crea uno para acceder a la administración.";
}

$docenteUser = $conexion->query(
    "SELECT u.username FROM usuarios u WHERE u.rol = 'docente' AND u.estado = 'activo' LIMIT 1"
)->fetchColumn();

if ($docenteUser) {
    $info[] = "✅ Usuario docente: <code>$docenteUser</code>";
} else {
    $advertencias[] = "⚠️ No hay docentes activos. Crea algunos.";
}

$estudianteCount = $conexion->query("SELECT COUNT(*) FROM estudiante")->fetchColumn();
$info[] = "✅ Estudiantes: $estudianteCount";

$cursoCount = $conexion->query("SELECT COUNT(*) FROM curso WHERE estado = 1")->fetchColumn();
$info[] = "✅ Cursos activos: $cursoCount";

$gestActiva = $conexion->query(
    "SELECT `año` FROM gestion WHERE estado = 'activa' LIMIT 1"
)->fetchColumn();
if ($gestActiva) {
    $info[] = "✅ Gestión activa: $gestActiva";
}

// ============================================================
// RENDERIZAR HTML
// ============================================================
echo renderHTML($errores, $advertencias, $info);

function renderHTML($errores, $advertencias, $info) {
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas - Sistema Académico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; }
        .card { margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-header { background: #007bff; color: white; font-weight: bold; }
        .list-group-item { border-left: 4px solid transparent; }
        .error { border-left-color: #dc3545; background: #fff5f5; }
        .warning { border-left-color: #ffc107; background: #fffbf0; }
        .info { border-left-color: #28a745; background: #f0fff4; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .back-link { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                🧪 Pruebas de Sistema - Base de Datos
            </div>
            <div class="card-body">
                <p>Este script verifica que la BD está correctamente configurada y lista para usar.</p>
                <p class="text-muted">Última actualización: <?php echo date("Y-m-d H:i:s"); ?></p>
            </div>
        </div>

        <?php if (!empty($errores)): ?>
        <div class="card">
            <div class="card-header bg-danger">
                ❌ Errores (<?php echo count($errores); ?>)
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($errores as $err): ?>
                    <div class="list-group-item error">
                        <?php echo htmlspecialchars($err); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($advertencias)): ?>
        <div class="card">
            <div class="card-header bg-warning text-dark">
                ⚠️ Advertencias (<?php echo count($advertencias); ?>)
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($advertencias as $adv): ?>
                    <div class="list-group-item warning">
                        <?php echo htmlspecialchars($adv); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($info)): ?>
        <div class="card">
            <div class="card-header bg-success">
                ✅ Información (<?php echo count($info); ?>)
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($info as $i): ?>
                    <div class="list-group-item info">
                        <?php echo $i; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="index.php" class="btn btn-primary">Volver al sistema</a>
            <button onclick="location.reload()" class="btn btn-secondary">Recargar pruebas</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
}
?>