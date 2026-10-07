<?php
/**
 * PRUEBAS INTERACTIVAS - Sistema Académico
 * 
 * Cópialo a la raíz del proyecto
 * Accede a: http://localhost/tu-proyecto/pruebas_interactivas.php
 * 
 * Te permite:
 * - Probar el login sin interfaz web
 * - Crear usuarios directamente
 * - Crear inscripciones de prueba
 * - Simular acciones del controlador
 */

require_once "config/database.php";

session_start();

$resultado = null;
$paso = $_GET["paso"] ?? "inicio";

// ============================================================
// PASO 0: Menú principal
// ============================================================
if ($paso === "inicio") {
    $resultado = [
        "titulo" => "🧪 Pruebas Interactivas",
        "mensaje" => "Elige una prueba para ejecutar:",
        "opciones" => [
            ["titulo" => "1️⃣ Auditoría completa", "url" => "pruebas.php", "target" => "_blank"],
            ["titulo" => "2️⃣ Probar login", "url" => "?paso=login"],
            ["titulo" => "3️⃣ Crear usuario admin", "url" => "?paso=crear_admin"],
            ["titulo" => "4️⃣ Crear docente", "url" => "?paso=crear_docente"],
            ["titulo" => "5️⃣ Crear estudiante", "url" => "?paso=crear_estudiante"],
            ["titulo" => "6️⃣ Ver todos los usuarios", "url" => "?paso=listar_usuarios"],
            ["titulo" => "7️⃣ Verificar docentes y estudiantes", "url" => "?paso=verificar_perfiles"],
            ["titulo" => "8️⃣ Ver BD", "url" => "?paso=ver_bd"],
            ["titulo" => "Volver al sistema", "url" => "index.php"],
        ]
    ];
}

// ============================================================
// PASO 1: Login
// ============================================================
elseif ($paso === "login") {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $usuario = trim($_POST["usuario"] ?? "");
        $pass = $_POST["password"] ?? "";

        if (empty($usuario) || empty($pass)) {
            $resultado = ["error" => "Usuario y contraseña son requeridos"];
        } else {
            $stmt = Database::getConnection()->prepare(
                "SELECT * FROM usuarios WHERE username = :u OR correo = :u LIMIT 1"
            );
            $stmt->execute([":u" => $usuario]);
            $usuarioData = $stmt->fetch();

            if ($usuarioData && password_verify($pass, $usuarioData["password"])) {
                if ($usuarioData["estado"] === "inactivo") {
                    $resultado = ["error" => "Esta cuenta está deshabilitada"];
                } else {
                    $_SESSION["iniciarSesion"] = "ok";
                    $_SESSION["id"] = $usuarioData["id_usuario"];
                    $_SESSION["nombre"] = $usuarioData["nombre"];
                    $_SESSION["apellido"] = $usuarioData["apellido"];
                    $_SESSION["username"] = $usuarioData["username"];
                    $_SESSION["rol"] = strtolower($usuarioData["rol"]);

                    $resultado = [
                        "exito" => "Login exitoso como " . $usuarioData["nombre"] . " (" . $usuarioData["rol"] . ")",
                        "redirigir" => "index.php"
                    ];
                }
            } else {
                $resultado = ["error" => "Usuario o contraseña incorrectos"];
            }
        }
    } else {
        $resultado = [
            "titulo" => "Probar login",
            "formulario" => "login",
            "campos" => [
                ["nombre" => "usuario", "label" => "Usuario o correo", "type" => "text", "placeholder" => "admin"],
                ["nombre" => "password", "label" => "Contraseña", "type" => "password", "placeholder" => "Test123456"]
            ]
        ];
    }
}

// ============================================================
// PASO 2: Crear admin
// ============================================================
elseif ($paso === "crear_admin") {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = trim($_POST["nombre"] ?? "");
        $apellido = trim($_POST["apellido"] ?? "");
        $ci = trim($_POST["ci"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        if (empty($nombre) || empty($apellido) || empty($ci) || empty($correo) || empty($username) || empty($password)) {
            $resultado = ["error" => "Todos los campos son obligatorios"];
        } else {
            try {
                $db = Database::getConnection();
                $stmt = $db->prepare(
                    "INSERT INTO usuarios (nombre, apellido, ci, telefono, correo, username, password, rol, estado)
                     VALUES (:nombre, :apellido, :ci, :telefono, :correo, :username, :password, 'admin', 'activo')"
                );
                $resultado_exec = $stmt->execute([
                    ":nombre" => $nombre,
                    ":apellido" => $apellido,
                    ":ci" => $ci,
                    ":telefono" => !empty($telefono) ? $telefono : null,
                    ":correo" => $correo,
                    ":username" => $username,
                    ":password" => password_hash($password, PASSWORD_DEFAULT)
                ]);

                if ($resultado_exec) {
                    $resultado = ["exito" => "Admin creado correctamente. Usuario: " . $username];
                } else {
                    $resultado = ["error" => "No se pudo crear el admin. Verifica que el usuario/email no exista"];
                }
            } catch (Exception $e) {
                $resultado = ["error" => "Error: " . $e->getMessage()];
            }
        }
    } else {
        $resultado = [
            "titulo" => "Crear usuario Administrador",
            "formulario" => "crear_usuario",
            "campos" => [
                ["nombre" => "nombre", "label" => "Nombre", "type" => "text"],
                ["nombre" => "apellido", "label" => "Apellido", "type" => "text"],
                ["nombre" => "ci", "label" => "CI", "type" => "text"],
                ["nombre" => "telefono", "label" => "Teléfono (opcional)", "type" => "text"],
                ["nombre" => "correo", "label" => "Correo", "type" => "email"],
                ["nombre" => "username", "label" => "Nombre de usuario", "type" => "text"],
                ["nombre" => "password", "label" => "Contraseña (mín. 8 caracteres)", "type" => "password"],
            ]
        ];
    }
}

// ============================================================
// PASO 3: Crear docente
// ============================================================
elseif ($paso === "crear_docente") {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = trim($_POST["nombre"] ?? "");
        $apellido = trim($_POST["apellido"] ?? "");
        $ci = trim($_POST["ci"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $especialidad = trim($_POST["especialidad"] ?? "Sin especificar");

        if (empty($nombre) || empty($apellido) || empty($ci) || empty($correo) || empty($username) || empty($password)) {
            $resultado = ["error" => "Todos los campos básicos son obligatorios"];
        } else {
            try {
                $db = Database::getConnection();
                $db->beginTransaction();

                $stmt = $db->prepare(
                    "INSERT INTO usuarios (nombre, apellido, ci, telefono, correo, username, password, rol, estado)
                     VALUES (:nombre, :apellido, :ci, :telefono, :correo, :username, :password, 'docente', 'activo')"
                );
                $stmt->execute([
                    ":nombre" => $nombre,
                    ":apellido" => $apellido,
                    ":ci" => $ci,
                    ":telefono" => !empty($telefono) ? $telefono : null,
                    ":correo" => $correo,
                    ":username" => $username,
                    ":password" => password_hash($password, PASSWORD_DEFAULT)
                ]);

                $idUsuario = (int) $db->lastInsertId();

                $stmt = $db->prepare("INSERT INTO docente (id_usuario, especialidad) VALUES (:id, :esp)");
                $stmt->execute([
                    ":id" => $idUsuario,
                    ":esp" => $especialidad
                ]);

                $db->commit();
                $resultado = ["exito" => "Docente creado correctamente. Usuario: " . $username];
            } catch (Exception $e) {
                $db->rollBack();
                $resultado = ["error" => "Error: " . $e->getMessage()];
            }
        }
    } else {
        $resultado = [
            "titulo" => "Crear usuario Docente",
            "formulario" => "crear_usuario",
            "campos" => [
                ["nombre" => "nombre", "label" => "Nombre", "type" => "text"],
                ["nombre" => "apellido", "label" => "Apellido", "type" => "text"],
                ["nombre" => "ci", "label" => "CI", "type" => "text"],
                ["nombre" => "telefono", "label" => "Teléfono (opcional)", "type" => "text"],
                ["nombre" => "correo", "label" => "Correo", "type" => "email"],
                ["nombre" => "username", "label" => "Nombre de usuario", "type" => "text"],
                ["nombre" => "password", "label" => "Contraseña (mín. 8 caracteres)", "type" => "password"],
                ["nombre" => "especialidad", "label" => "Especialidad", "type" => "text", "placeholder" => "Ej: Matemáticas"],
            ]
        ];
    }
}

// ============================================================
// PASO 4: Crear estudiante
// ============================================================
elseif ($paso === "crear_estudiante") {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = trim($_POST["nombre"] ?? "");
        $apellido = trim($_POST["apellido"] ?? "");
        $ci = trim($_POST["ci"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        if (empty($nombre) || empty($apellido) || empty($ci) || empty($correo) || empty($username) || empty($password)) {
            $resultado = ["error" => "Todos los campos son obligatorios"];
        } else {
            try {
                $db = Database::getConnection();
                $db->beginTransaction();

                $stmt = $db->prepare(
                    "INSERT INTO usuarios (nombre, apellido, ci, telefono, correo, username, password, rol, estado)
                     VALUES (:nombre, :apellido, :ci, :telefono, :correo, :username, :password, 'estudiante', 'activo')"
                );
                $stmt->execute([
                    ":nombre" => $nombre,
                    ":apellido" => $apellido,
                    ":ci" => $ci,
                    ":telefono" => !empty($telefono) ? $telefono : null,
                    ":correo" => $correo,
                    ":username" => $username,
                    ":password" => password_hash($password, PASSWORD_DEFAULT)
                ]);

                $idUsuario = (int) $db->lastInsertId();

                $stmt = $db->prepare("INSERT INTO estudiante (id_usuario) VALUES (:id)");
                $stmt->execute([":id" => $idUsuario]);

                $db->commit();
                $resultado = ["exito" => "Estudiante creado correctamente. Usuario: " . $username];
            } catch (Exception $e) {
                $db->rollBack();
                $resultado = ["error" => "Error: " . $e->getMessage()];
            }
        }
    } else {
        $resultado = [
            "titulo" => "Crear usuario Estudiante",
            "formulario" => "crear_usuario",
            "campos" => [
                ["nombre" => "nombre", "label" => "Nombre", "type" => "text"],
                ["nombre" => "apellido", "label" => "Apellido", "type" => "text"],
                ["nombre" => "ci", "label" => "CI", "type" => "text"],
                ["nombre" => "telefono", "label" => "Teléfono (opcional)", "type" => "text"],
                ["nombre" => "correo", "label" => "Correo", "type" => "email"],
                ["nombre" => "username", "label" => "Nombre de usuario", "type" => "text"],
                ["nombre" => "password", "label" => "Contraseña (mín. 8 caracteres)", "type" => "password"],
            ]
        ];
    }
}

// ============================================================
// PASO 5: Listar usuarios
// ============================================================
elseif ($paso === "listar_usuarios") {
    try {
        $stmt = Database::getConnection()->query(
            "SELECT id_usuario, nombre, apellido, ci, username, rol, estado FROM usuarios ORDER BY id_usuario"
        );
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = [
            "titulo" => "Lista de usuarios",
            "tabla" => ["id_usuario", "nombre", "apellido", "ci", "username", "rol", "estado"],
            "datos" => $usuarios
        ];
    } catch (Exception $e) {
        $resultado = ["error" => $e->getMessage()];
    }
}

// ============================================================
// PASO 6: Verificar perfiles
// ============================================================
elseif ($paso === "verificar_perfiles") {
    try {
        $db = Database::getConnection();
        $stmt = $db->query(
            "SELECT d.id_docente, u.nombre, u.apellido, u.username, d.especialidad
             FROM docente d
             INNER JOIN usuarios u ON u.id_usuario = d.id_usuario
             ORDER BY d.id_docente"
        );
        $docentes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $stmt = $db->query(
            "SELECT e.id_estudiante, u.nombre, u.apellido, u.username, e.rude
             FROM estudiante e
             INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
             ORDER BY e.id_estudiante"
        );
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $resultado = [
            "titulo" => "Verificar perfiles vinculados",
            "seccion1" => [
                "titulo" => "Docentes",
                "tabla" => ["id_docente", "nombre", "apellido", "username", "especialidad"],
                "datos" => !empty($docentes) ? $docentes : ["error" => "No hay docentes"]
            ],
            "seccion2" => [
                "titulo" => "Estudiantes",
                "tabla" => ["id_estudiante", "nombre", "apellido", "username", "rude"],
                "datos" => !empty($estudiantes) ? $estudiantes : ["error" => "No hay estudiantes"]
            ]
        ];
    } catch (Exception $e) {
        $resultado = ["error" => $e->getMessage()];
    }
}

// ============================================================
// PASO 7: Ver BD directa
// ============================================================
elseif ($paso === "ver_bd") {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $tabla = $_POST["tabla"] ?? "";
        $tablasValidas = ["usuarios", "docente", "estudiante", "curso", "materia", "gestion", "inscripcion", "nota"];

        if (!in_array($tabla, $tablasValidas, true)) {
            $resultado = ["error" => "Tabla no válida"];
        } else {
            try {
                $stmt = Database::getConnection()->query("SELECT * FROM " . $tabla . " LIMIT 100");
                $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($datos)) {
                    $resultado = ["info" => "No hay registros en " . $tabla];
                } else {
                    $resultado = [
                        "titulo" => "Contenido de tabla: " . $tabla,
                        "tabla" => array_keys($datos[0]),
                        "datos" => $datos
                    ];
                }
            } catch (Exception $e) {
                $resultado = ["error" => $e->getMessage()];
            }
        }
    } else {
        $resultado = [
            "titulo" => "Ver contenido de tabla",
            "formulario" => "tabla",
            "tablas" => ["usuarios", "docente", "estudiante", "curso", "materia", "gestion", "inscripcion", "nota"]
        ];
    }
}

// ============================================================
// RENDERIZAR HTML
// ============================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas Interactivas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; }
        .card { margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { font-size: 0.9rem; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($resultado === null) { echo "Error: paso no reconocido"; } ?>

        <?php if (isset($resultado["exito"])): ?>
        <div class="alert alert-success"><?php echo $resultado["exito"]; ?></div>
        <?php if (isset($resultado["redirigir"])): ?>
            <meta http-equiv="refresh" content="2; url=<?php echo $resultado["redirigir"]; ?>">
        <?php else: ?>
            <a href="?paso=inicio" class="btn btn-primary">Volver al menú</a>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($resultado["error"])): ?>
        <div class="alert alert-danger"><?php echo $resultado["error"]; ?></div>
        <a href="?paso=inicio" class="btn btn-primary">Volver al menú</a>
        <?php endif; ?>

        <?php if (isset($resultado["info"])): ?>
        <div class="alert alert-info"><?php echo $resultado["info"]; ?></div>
        <a href="?paso=inicio" class="btn btn-primary">Volver al menú</a>
        <?php endif; ?>

        <?php if (isset($resultado["titulo"]) && !isset($resultado["formulario"]) && !isset($resultado["tabla"]) && !isset($resultado["opciones"])): ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <?php echo $resultado["titulo"]; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($resultado["opciones"])): ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <?php echo $resultado["titulo"]; ?>
            </div>
            <div class="card-body">
                <p><?php echo $resultado["mensaje"]; ?></p>
                <div class="list-group">
                    <?php foreach ($resultado["opciones"] as $op): ?>
                    <a href="<?php echo $op["url"]; ?>" target="<?php echo $op["target"] ?? "_self"; ?>" class="list-group-item list-group-item-action">
                        <?php echo $op["titulo"]; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (($resultado["formulario"] ?? "") === "login"): ?>
        <div class="card">
            <div class="card-header bg-primary text-white"><?php echo $resultado["titulo"]; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php foreach ($resultado["campos"] as $campo): ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $campo["label"]; ?></label>
                        <input type="<?php echo $campo["type"]; ?>" class="form-control" name="<?php echo $campo["nombre"]; ?>" placeholder="<?php echo $campo["placeholder"] ?? ""; ?>" required>
                    </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary">Probar</button>
                    <a href="?paso=inicio" class="btn btn-secondary">Volver</a>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (($resultado["formulario"] ?? "") === "crear_usuario"): ?>
        <div class="card">
            <div class="card-header bg-primary text-white"><?php echo $resultado["titulo"]; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php foreach ($resultado["campos"] as $campo): ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $campo["label"]; ?></label>
                        <input type="<?php echo $campo["type"]; ?>" class="form-control" name="<?php echo $campo["nombre"]; ?>" placeholder="<?php echo $campo["placeholder"] ?? ""; ?>" <?php echo strpos($campo["label"], "opcional") === false ? "required" : ""; ?>>
                    </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary">Crear</button>
                    <a href="?paso=inicio" class="btn btn-secondary">Volver</a>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (($resultado["formulario"] ?? "") === "tabla"): ?>
        <div class="card">
            <div class="card-header bg-primary text-white"><?php echo $resultado["titulo"]; ?></div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Selecciona una tabla</label>
                        <select class="form-select" name="tabla" required>
                            <option value="">-- Elige una tabla --</option>
                            <?php foreach ($resultado["tablas"] as $t): ?>
                            <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Ver</button>
                    <a href="?paso=inicio" class="btn btn-secondary">Volver</a>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($resultado["tabla"]) && is_array($resultado["tabla"])): ?>
        <div class="card">
            <div class="card-header bg-primary text-white"><?php echo $resultado["titulo"] ?? ""; ?></div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <?php foreach ($resultado["tabla"] as $col): ?>
                            <th><?php echo $col; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultado["datos"] as $row): ?>
                        <tr>
                            <?php foreach ($resultado["tabla"] as $col): ?>
                            <td><?php echo htmlspecialchars((string) ($row[$col] ?? ""), ENT_QUOTES, "UTF-8"); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="?paso=inicio" class="btn btn-primary">Volver al menú</a>
        <?php endif; ?>

        <?php if (isset($resultado["seccion1"])): ?>
        <div class="card">
            <div class="card-header bg-primary text-white"><?php echo $resultado["titulo"]; ?></div>
        </div>

        <div class="card">
            <div class="card-header"><?php echo $resultado["seccion1"]["titulo"]; ?></div>
            <div class="card-body table-responsive">
                <?php if (isset($resultado["seccion1"]["error"])): ?>
                <p class="text-muted"><?php echo $resultado["seccion1"]["error"]; ?></p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr><?php foreach ($resultado["seccion1"]["tabla"] as $col): ?><th><?php echo $col; ?></th><?php endforeach; ?></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultado["seccion1"]["datos"] as $row): ?>
                        <tr><?php foreach ($resultado["seccion1"]["tabla"] as $col): ?><td><?php echo htmlspecialchars((string) ($row[$col] ?? ""), ENT_QUOTES, "UTF-8"); ?></td><?php endforeach; ?></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><?php echo $resultado["seccion2"]["titulo"]; ?></div>
            <div class="card-body table-responsive">
                <?php if (isset($resultado["seccion2"]["error"])): ?>
                <p class="text-muted"><?php echo $resultado["seccion2"]["error"]; ?></p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr><?php foreach ($resultado["seccion2"]["tabla"] as $col): ?><th><?php echo $col; ?></th><?php endforeach; ?></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultado["seccion2"]["datos"] as $row): ?>
                        <tr><?php foreach ($resultado["seccion2"]["tabla"] as $col): ?><td><?php echo htmlspecialchars((string) ($row[$col] ?? ""), ENT_QUOTES, "UTF-8"); ?></td><?php endforeach; ?></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        <a href="?paso=inicio" class="btn btn-primary mt-3">Volver al menú</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>