<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($datosSolicitud["csrf"], ENT_QUOTES, "UTF-8"); ?>">
<div class="row g-3">
	<div class="col-md-6">
		<label class="form-label" for="solicitud-nombre">Nombre</label>
		<input class="form-control" id="solicitud-nombre" name="nombre" maxlength="100" required>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-apellido">Apellido</label>
		<input class="form-control" id="solicitud-apellido" name="apellido" maxlength="100" required>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-ci">CI</label>
		<input class="form-control" id="solicitud-ci" name="ci" minlength="4" maxlength="20" required>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-rude">RUDE (opcional)</label>
		<input class="form-control" id="solicitud-rude" name="rude" maxlength="30">
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-correo">Correo electrónico</label>
		<input class="form-control" id="solicitud-correo" name="correo" type="email" maxlength="100" required>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-telefono">Teléfono (opcional)</label>
		<input class="form-control" id="solicitud-telefono" name="telefono" maxlength="20">
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-usuario">Nombre de usuario</label>
		<input class="form-control" id="solicitud-usuario" name="username" minlength="4" maxlength="40" autocomplete="username" required>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-fecha">Fecha de nacimiento (opcional)</label>
		<input class="form-control" id="solicitud-fecha" name="fecha_nacimiento" type="date">
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-password">Contraseña</label>
		<input class="form-control" id="solicitud-password" name="password" type="password" minlength="8" autocomplete="new-password" required>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-confirmar">Confirmar contraseña</label>
		<input class="form-control" id="solicitud-confirmar" name="confirmar_password" type="password" minlength="8" autocomplete="new-password" required>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-genero">Género (opcional)</label>
		<select class="form-select" id="solicitud-genero" name="genero">
			<option value="">Seleccionar</option>
			<option value="Masculino">Masculino</option>
			<option value="Femenino">Femenino</option>
		</select>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-curso">Curso</label>
		<select class="form-select" id="solicitud-curso" name="id_curso" required <?php echo empty($datosSolicitud["cursos"]) ? "disabled" : ""; ?>>
			<option value="">Seleccionar curso</option>
			<?php foreach ($datosSolicitud["cursos"] as $curso): ?>
				<option value="<?php echo (int) $curso["id_curso"]; ?>">
					<?php echo htmlspecialchars($curso["nombre_curso"] . " — Paralelo " . $curso["paralelo"], ENT_QUOTES, "UTF-8"); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (empty($datosSolicitud["cursos"])): ?>
			<div class="form-text text-danger">No se pudieron cargar los cursos. Contacta a secretaría para revisar la configuración.</div>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<label class="form-label" for="solicitud-gestion">Gestión activa</label>
		<select class="form-select" id="solicitud-gestion" name="id_gestion" required>
			<option value="">Seleccionar gestión</option>
			<?php foreach ($datosSolicitud["gestiones"] as $gestion): ?>
				<option value="<?php echo (int) $gestion["id_gestion"]; ?>"><?php echo htmlspecialchars((string) $gestion["anio"], ENT_QUOTES, "UTF-8"); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-12">
		<label class="form-label" for="solicitud-direccion">Dirección (opcional)</label>
		<textarea class="form-control" id="solicitud-direccion" name="direccion" maxlength="500" rows="2"></textarea>
	</div>
</div>
