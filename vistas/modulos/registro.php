<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/pages/auth.css">

<div id="auth">
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="index.php"><img src="<?php echo BASE_URL; ?>assets/images/logo/logo.png" alt="Logo"></a>
                </div>
                <h1 class="auth-title">Crear cuenta</h1>
                <p class="auth-subtitle mb-4">Registra un nuevo usuario.</p>

                <?php
                $registro = new ControladorUsuarios();
                $registro->ctrRegistroUsuario();

                if (!empty($GLOBALS["registroExitoso"])) {
                    echo '<div class="alert alert-success text-center">Cuenta creada. Ya puedes iniciar sesión.</div>';
                } elseif (!empty($GLOBALS["registroError"])) {
                    echo '<div class="alert alert-danger text-center">' . htmlspecialchars($GLOBALS["registroError"], ENT_QUOTES, "UTF-8") . '</div>';
                }
                ?>

                <form method="POST" action="index.php?ruta=registro">
                    <div class="form-group mb-3">
                        <input type="text" class="form-control form-control-xl" name="regNombre" placeholder="Nombre" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="text" class="form-control form-control-xl" name="regApellido" placeholder="Apellido" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="text" class="form-control form-control-xl" name="regCi" placeholder="CI" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="email" class="form-control form-control-xl" name="regCorreo" placeholder="Correo electrónico" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="text" class="form-control form-control-xl" name="regUsername" placeholder="Nombre de usuario" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control form-control-xl" name="regPassword" placeholder="Contraseña" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control form-control-xl" name="regConfirmarPassword" placeholder="Confirmar contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-3">Crear cuenta</button>
                </form>

                <div class="text-center mt-4">
                    <a class="font-bold" href="index.php">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
    </div>
</div>