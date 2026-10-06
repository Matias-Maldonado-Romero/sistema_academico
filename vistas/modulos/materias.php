<?php
$datosMaterias = (new ControladorMaterias())->ctrGestionarMaterias();
$materiaEditar = $datosMaterias["materiaEditar"];
?>
<div class="page-heading">
	<div class="page-title">
		<h3>Materias</h3>
		<p class="text-subtitle text-muted">Administra las asignaturas y su campo de formación.</p>
	</div>
</div>

<div class="page-content">
	<section class="section">
		<?php if ($datosMaterias["mensaje"] !== ""): ?>
			<div class="d-none" data-swal-feedback data-swal-feedback-type="<?php echo htmlspecialchars($datosMaterias["tipo"], ENT_QUOTES, "UTF-8"); ?>">
				<?php echo htmlspecialchars($datosMaterias["mensaje"], ENT_QUOTES, "UTF-8"); ?>
			</div>
		<?php endif; ?>

		<div class="card">
			<div class="card-header">
				<h4 class="card-title mb-0"><?php echo $materiaEditar ? "Editar materia" : "Registrar materia"; ?></h4>
			</div>
			<div class="card-body">
				<form method="POST" action="index.php?ruta=materias" data-swal-confirm data-swal-confirm-title="<?php echo $materiaEditar ? "¿Guardar los cambios?" : "¿Registrar la materia?"; ?>" data-swal-confirm-text="Se guardará la información de esta materia.">
					<input type="hidden" name="id_materia" value="<?php echo (int) ($materiaEditar["id_materia"] ?? 0); ?>">
					<div class="row g-3">
						<div class="col-12 col-md-4">
							<label class="form-label" for="nombre-materia">Nombre de la materia</label>
							<input class="form-control" id="nombre-materia" name="nombre_materia" type="text" maxlength="100" required value="<?php echo htmlspecialchars($materiaEditar["nombre_materia"] ?? "", ENT_QUOTES, "UTF-8"); ?>">
						</div>
						<div class="col-12 col-md-4">
							<label class="form-label" for="campo-materia">Campo de formación</label>
							<input class="form-control" id="campo-materia" name="campo" type="text" maxlength="50" value="<?php echo htmlspecialchars($materiaEditar["campo"] ?? "", ENT_QUOTES, "UTF-8"); ?>" placeholder="Ej. Ciencias Naturales">
						</div>
						<div class="col-12 col-md-4">
							<label class="form-label" for="descripcion-materia">Descripción</label>
							<input class="form-control" id="descripcion-materia" name="descripcion" type="text" maxlength="150" value="<?php echo htmlspecialchars($materiaEditar["descripcion"] ?? "", ENT_QUOTES, "UTF-8"); ?>" placeholder="Descripción breve">
						</div>
						<div class="col-12 d-flex justify-content-end gap-2">
							<?php if ($materiaEditar): ?>
								<a class="btn btn-light-secondary" href="index.php?ruta=materias">Cancelar</a>
							<?php endif; ?>
							<button class="btn btn-primary" type="submit" name="guardarMateria" value="1">
								<i class="bi bi-save me-1" aria-hidden="true"></i><?php echo $materiaEditar ? "Guardar cambios" : "Registrar materia"; ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="card mt-4">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h4 class="card-title mb-0">Listado de materias</h4>
				<span class="badge bg-light-secondary text-secondary"><?php echo count($datosMaterias["materias"]); ?> registros</span>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover align-middle mb-0">
						<thead>
							<tr>
								<th scope="col">Materia</th>
								<th scope="col">Campo</th>
								<th scope="col">Descripción</th>
								<th scope="col">Estado</th>
								<th scope="col" class="text-end">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php if (empty($datosMaterias["materias"])): ?>
								<tr><td colspan="5" class="text-center text-muted py-4">No hay materias registradas todavía.</td></tr>
							<?php else: ?>
								<?php foreach ($datosMaterias["materias"] as $materia): ?>
									<tr>
										<td class="fw-bold"><?php echo htmlspecialchars($materia["nombre_materia"], ENT_QUOTES, "UTF-8"); ?></td>
										<td><?php echo htmlspecialchars($materia["campo"] ?: "—", ENT_QUOTES, "UTF-8"); ?></td>
										<td><?php echo htmlspecialchars($materia["descripcion"] ?: "—", ENT_QUOTES, "UTF-8"); ?></td>
										<td>
											<span class="badge <?php echo (int) $materia["estado"] === 1 ? "bg-light-success text-success" : "bg-light-secondary text-secondary"; ?>">
												<?php echo (int) $materia["estado"] === 1 ? "Activa" : "Inactiva"; ?>
											</span>
										</td>
										<td class="text-end">
											<div class="d-inline-flex gap-1">
												<a class="btn btn-sm btn-light-primary" href="index.php?ruta=materias&amp;editar=<?php echo (int) $materia["id_materia"]; ?>" aria-label="Editar <?php echo htmlspecialchars($materia["nombre_materia"], ENT_QUOTES, "UTF-8"); ?>" title="Editar">
													<i class="bi bi-pencil-square" aria-hidden="true"></i>
												</a>
												<form method="POST" action="index.php?ruta=materias" class="d-inline" data-swal-confirm data-swal-confirm-title="<?php echo (int) $materia["estado"] === 1 ? "¿Desactivar esta materia?" : "¿Activar esta materia?"; ?>" data-swal-confirm-text="<?php echo htmlspecialchars($materia["nombre_materia"], ENT_QUOTES, "UTF-8"); ?>">
													<input type="hidden" name="id_materia" value="<?php echo (int) $materia["id_materia"]; ?>">
													<input type="hidden" name="nuevo_estado" value="<?php echo (int) $materia["estado"] === 1 ? 0 : 1; ?>">
													<button class="btn btn-sm <?php echo (int) $materia["estado"] === 1 ? "btn-light-danger" : "btn-light-success"; ?>" type="submit" name="cambiarEstadoMateria" value="1" aria-label="<?php echo (int) $materia["estado"] === 1 ? "Desactivar" : "Activar"; ?> <?php echo htmlspecialchars($materia["nombre_materia"], ENT_QUOTES, "UTF-8"); ?>" title="<?php echo (int) $materia["estado"] === 1 ? "Desactivar" : "Activar"; ?>">
														<i class="bi <?php echo (int) $materia["estado"] === 1 ? "bi-toggle-on" : "bi-toggle-off"; ?>" aria-hidden="true"></i>
													</button>
												</form>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>