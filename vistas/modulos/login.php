<!-- Cargamos el CSS exclusivo de esta vista de Mazer -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/pages/auth.css">

<div id="auth">
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <!-- Ruta del logo corregida con BASE_URL -->
                    <a href="#"><img src="<?php echo BASE_URL; ?>assets/images/logo/logo.png" alt="Logo"></a>
                </div>
                <h1 class="auth-title">Log in.</h1>
                <p class="auth-subtitle mb-5">Log in with your data that you entered during registration.</p>

                <!-- Formulario adaptado a POST -->
                <form method="POST">
                    <div class="form-group position-relative has-icon-left mb-4">
                        <!-- name="ingEmail" añadido -->
                        <input type="email" class="form-control form-control-xl" name="ingEmail" placeholder="Username" required>
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <!-- name="ingPassword" añadido -->
                        <input type="password" class="form-control form-control-xl" name="ingPassword" placeholder="Password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <div class="form-check form-check-lg d-flex align-items-end">
                        <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label text-gray-600" for="flexCheckDefault">
                            Keep me logged in
                        </label>
                    </div>
                    
                    <!-- type="submit" añadido -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>

                    <?php
                    // Ejecución del controlador backend
                    $login = new ControladorUsuarios();
                    $login->ctrIngresoUsuario();
                    ?>
                </form>
                
                <div class="text-center mt-5 text-lg fs-4">
                    <p class="text-gray-600">Don't have an account? <a href="#" class="font-bold">Sign up</a>.</p>
                    <p><a class="font-bold" href="#">Forgot password?</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">
                <!-- Mazer inserta la imagen de fondo aquí mediante CSS (auth.css) -->
            </div>
        </div>
    </div>
</div>