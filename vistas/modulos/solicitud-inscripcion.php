<?php
$datosSolicitud = (new ControladorInscripciones())->ctrGestionarSolicitudOnline();
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/pages/auth.css">

<div id="auth">
	<div class="row h-100">
		<div class="col-lg-7 col-12">
			<div id="auth-left">
				<div class="auth-logo">
					<a href="index.php"><img src="<?php echo BASE_URL; ?>assets/images/logo/logo-institucion.svg" alt="Logo de la Unidad Educativa"></a>
				</div>
				<h1 class="auth-title">Inscripción en línea</h1>
				<p class="auth-subtitle mb-4">Completa tus datos. La solicitud quedará pendiente hasta que secretaría la revise.</p>

				<?php if ($datosSolicitud["mensaje"] !== ""): ?>
					<div class="alert alert-<?php echo htmlspecialchars($datosSolicitud["tipo"], ENT_QUOTES, "UTF-8"); ?>" role="alert">
						<?php echo htmlspecialchars($datosSolicitud["mensaje"], ENT_QUOTES, "UTF-8"); ?>
					</div>
				<?php endif; ?>

				<form method="POST" action="index.php?ruta=solicitud-inscripcion">
					<?php include "vistas/modulos/formulario-solicitud-inscripcion.php"; ?>

					<button type="submit" class="btn btn-primary btn-lg w-100 mt-4" name="solicitudOnline" value="1">
						Enviar solicitud
					</button>
				</form>

				<div class="text-center mt-4">
					<a class="font-bold" href="index.php">Volver al inicio de sesión</a>
				</div>
			</div>
		</div>
		<div class="col-lg-5 d-none d-lg-block">
			<div id="auth-right"></div>
		</div>
	</div>
</div>
