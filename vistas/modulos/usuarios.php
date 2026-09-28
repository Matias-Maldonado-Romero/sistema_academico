<?php
require_once "controladores/usuarios.controlador.php";
require_once "modelos/usuarios.modelo.php";

// 1. Acciones
$controlador = new ControladorUsuarios();

$controlador->ctrCrearUsuario();
$controlador->ctrRegistroUsuario();
$controlador->ctrEditarUsuario();
$controlador->ctrCambiarEstadoUsuario();

$usuarios = ModeloUsuarios::mdlMostrarUsuarios();
$puedeAdministrar = isset($_SESSION["iniciarSesion"], $_SESSION["rol"])
    && $_SESSION["iniciarSesion"] === "ok"
    && in_array(strtolower(trim((string) $_SESSION["rol"])), ["admin", "administrador"], true);
?>

<div class="container-fluid">

    <!-- 2. Lista de usuarios -->
    <div class="card shadow-sm mb-4">

        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">Usuarios</h3>
                <p class="text-muted mb-0">
                    Administración de usuarios del sistema
                </p>
            </div>
            <?php if ($puedeAdministrar): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                    Nuevo usuario
                </button>
            <?php endif; ?>
            <input
                type="text"
                id="buscarUsuario"
                class="form-control"
                style="width: 250px;"
                placeholder="Buscar usuario..."
            >
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Lista de usuarios</strong>
            <span class="float-end">
                Total:
                <strong>
                    <?php echo count($usuarios); ?>
                </strong>
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table
                    class="table table-hover"
                    id="tablaUsuarios"
                >
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $usuario["nombre"] . " " . $usuario["apellido"]
                                );
                                ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($usuario["correo"]); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($usuario["rol"]); ?>
                            </td>
                            <td class="text-center">
                                <?php if ($usuario["estado"] == "activo"): ?>
                                    <span class="badge bg-success">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm btnEditarUsuario"
                                    data-id="<?php echo $usuario["id_usuario"]; ?>"
                                    data-nombre="<?php echo htmlspecialchars($usuario["nombre"], ENT_QUOTES, "UTF-8"); ?>"
                                    data-apellido="<?php echo htmlspecialchars($usuario["apellido"], ENT_QUOTES, "UTF-8"); ?>"
                                    data-correo="<?php echo htmlspecialchars($usuario["correo"], ENT_QUOTES, "UTF-8"); ?>"
                                    data-rol="<?php echo htmlspecialchars($usuario["rol"], ENT_QUOTES, "UTF-8"); ?>"
                                >
                                    Editar
                                </button>
                                <form
                                    method="POST"
                                    style="display: inline;"
                                    class="formEstadoUsuario"
                                >
                                    <input
                                        type="hidden"
                                        name="cambiarEstadoUsuario"
                                        value="1"
                                    >
                                    <input
                                        type="hidden"
                                        name="id_usuario"
                                        value="<?php echo $usuario["id_usuario"]; ?>"
                                    >
                                    <?php if ($usuario["estado"] == "activo"): ?>
                                        <input
                                            type="hidden"
                                            name="estado"
                                            value="inactivo"
                                        >
                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                        >
                                            Deshabilitar
                                        </button>
                                    <?php else: ?>
                                        <input
                                            type="hidden"
                                            name="estado"
                                            value="activo"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm"
                                        >
                                            Habilitar
                                        </button>

                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (isset($GLOBALS["usuarioMensaje"])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($GLOBALS["usuarioMensajeTipo"] ?? "info", ENT_QUOTES, "UTF-8"); ?>" role="status">
        <?php echo htmlspecialchars($GLOBALS["usuarioMensaje"], ENT_QUOTES, "UTF-8"); ?>
    </div>
<?php endif; ?>

<?php if ($puedeAdministrar): ?>
<div class="modal fade" id="modalCrearUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="crearUsuario" value="1">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nuevoNombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="nuevoApellido" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CI</label>
                        <input type="text" class="form-control" name="nuevoCi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre de usuario</label>
                        <input type="text" class="form-control" name="nuevoUsername" autocomplete="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="nuevoPassword" autocomplete="new-password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="nuevoRol" required>
                            <option value="Admin">Administrador</option>
                            <option value="Docente">Docente</option>
                            <option value="Usuario">Usuario</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 3. Editar usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Editar usuario</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="editarUsuario" value="1">
                    <input type="hidden" name="id_usuario" id="editar_id_usuario">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                                class="form-control"
                                name="nombre"
                                id="editar_nombre"
                                required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text"
                                class="form-control"
                                name="apellido"
                                id="editar_apellido"
                                required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email"
                                class="form-control"
                                name="correo"
                                id="editar_correo"
                                required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select"
                                name="rol"
                                id="editar_rol">
                            <option value="Admin">Administrador</option>
                            <option value="Docente">Docente</option>
                            <option value="Secretaria">Secretaria</option>
                            <option value="Usuario">Usuario</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>

                        <input type="password"
                                class="form-control"
                                name="password"
                                placeholder="Dejar vacío para no cambiar">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>

// 4. Buscar y editar
document
    .getElementById("buscarUsuario")
    .addEventListener("keyup", function () {
        let texto = this.value.toLowerCase();
        let filas = document.querySelectorAll(
            "#tablaUsuarios tbody tr"
        );
        filas.forEach(function (fila) {
            if (fila.textContent.toLowerCase().includes(texto)) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    });

document
    .querySelectorAll(".btnEditarUsuario")
    .forEach(function (boton) {
        boton.addEventListener("click", function () {
            document.getElementById("editar_id_usuario").value =
                this.dataset.id;
            document.getElementById("editar_nombre").value =
                this.dataset.nombre;
            document.getElementById("editar_apellido").value =
                this.dataset.apellido;
            document.getElementById("editar_correo").value =
                this.dataset.correo;
            document.getElementById("editar_rol").value =
                this.dataset.rol;
            let modal = new bootstrap.Modal(
                document.getElementById("modalEditarUsuario")
            );
            modal.show();
        });
    });
</script>
