<?php
$datosInscripcion = (new ControladorInscripciones())->ctrGestionarInscripcion();
$datosSolicitud = (new ControladorInscripciones())->ctrGestionarSolicitudOnline();
?>
<div class="page-content">
	<section class="section">
		<?php if ($datosInscripcion["mensaje"] !== ""): ?>
			<div class="d-none" data-swal-feedback data-swal-feedback-type="<?php echo htmlspecialchars($datosInscripcion["tipo"], ENT_QUOTES, "UTF-8"); ?>">
				<?php echo htmlspecialchars($datosInscripcion["mensaje"], ENT_QUOTES, "UTF-8"); ?>
			</div>
		<?php endif; ?>
		<?php if ($datosSolicitud["mensaje"] !== ""): ?>
			<div class="d-none" data-swal-feedback data-swal-feedback-type="<?php echo htmlspecialchars($datosSolicitud["tipo"], ENT_QUOTES, "UTF-8"); ?>">
				<?php echo htmlspecialchars($datosSolicitud["mensaje"], ENT_QUOTES, "UTF-8"); ?>
			</div>
		<?php endif; ?>

		<div class="d-flex justify-content-end mb-4">
			<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalNuevaInscripcion">
				<i class="bi bi-person-plus-fill me-2" aria-hidden="true"></i>Registrar inscripción
			</button>
		</div>

		<div class="d-flex justify-content-between align-items-center mb-3">
			<h2 class="mb-0 text-primary fw-bold">Solicitudes de inscripción</h2>
			<span class="badge bg-light-secondary text-secondary fs-6"><?php echo count($datosInscripcion["inscripciones"]); ?> solicitudes</span>
		</div>

		<div class="table-responsive">
			<table class="table align-middle mb-0">
				<thead>
					<tr>
						<th scope="col">Estudiante</th>
						<th scope="col">RUDE</th>
						<th scope="col">Curso</th>
						<th scope="col">Gestión</th>
						<th scope="col">Estado</th>
						<th scope="col">Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($datosInscripcion["inscripciones"])): ?>
						<tr><td colspan="6" class="text-center text-muted py-4">No hay solicitudes de inscripción.</td></tr>
					<?php else: ?>
						<?php foreach ($datosInscripcion["inscripciones"] as $inscripcion): ?>
							<?php
							$estadoActual = strtolower((string) ($inscripcion["estado"] ?? "pendiente"));
							$estadosBadge = [
								"pendiente" => ["warning", "Pendiente"],
								"activo"    => ["success", "Aprobada"],
								"rechazado" => ["danger", "Rechazada"],
								"aprobado"  => ["info", "Aprobado"],
								"reprobado" => ["dark", "Reprobado"],
								"retirado"  => ["secondary", "Retirado"],
							];
							[$claseBadge, $textoBadge] = $estadosBadge[$estadoActual] ?? ["secondary", $estadoActual];
						?>
							<tr>
								<td><?php echo htmlspecialchars($inscripcion["estudiante"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars($inscripcion["rude"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars($inscripcion["curso"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars((string) $inscripcion["anio"], ENT_QUOTES, "UTF-8"); ?></td>
								<td>
									<span class="badge bg-<?php echo htmlspecialchars($claseBadge, ENT_QUOTES, "UTF-8"); ?>">
										<?php echo htmlspecialchars($textoBadge, ENT_QUOTES, "UTF-8"); ?>
									</span>
								</td>
								<td>
									<?php if ($estadoActual === "pendiente"): ?>
									<div class="d-flex gap-2">
										<form method="POST" action="index.php?ruta=inscripciones" class="d-inline">
											<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($datosInscripcion["csrf"], ENT_QUOTES, "UTF-8"); ?>">
											<input type="hidden" name="id_inscripcion" value="<?php echo (int) $inscripcion["id_inscripcion"]; ?>">
											<input type="hidden" name="estado" value="aprobada">
											<button type="submit" name="actualizarEstadoInscripcion" value="1" class="btn btn-success btn-sm">Aprobar</button>
										</form>
										<form method="POST" action="index.php?ruta=inscripciones" class="d-inline">
											<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($datosInscripcion["csrf"], ENT_QUOTES, "UTF-8"); ?>">
											<input type="hidden" name="id_inscripcion" value="<?php echo (int) $inscripcion["id_inscripcion"]; ?>">
											<input type="hidden" name="estado" value="rechazada">
											<button type="submit" name="actualizarEstadoInscripcion" value="1" class="btn btn-danger btn-sm">Rechazar</button>
										</form>
									</div>
									<?php else: ?>
										<span class="text-muted">Resuelta</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
</div>

<div class="modal fade" id="modalNuevaInscripcion" tabindex="-1" aria-labelledby="tituloModalNuevaInscripcion" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<form class="modal-content" method="POST" action="index.php?ruta=inscripciones">
				<div class="modal-header">
					<div>
						<h5 class="modal-title" id="tituloModalNuevaInscripcion">Registrar solicitud de inscripción</h5>
						<p class="text-muted small mb-0">Completa los datos del estudiante. La solicitud quedará pendiente hasta su aprobación.</p>
					</div>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>
				<div class="modal-body">
					<?php include "vistas/modulos/formulario-solicitud-inscripcion.php"; ?>

					<?php if (empty($datosSolicitud["cursos"]) || empty($datosSolicitud["gestiones"])): ?>
						<div class="alert alert-warning mb-0" role="alert">
							Para registrar una inscripción, primero debe haber cursos y una gestión activa.
						</div>
					<?php endif; ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary" name="solicitudOnline" value="1" <?php echo empty($datosSolicitud["cursos"]) || empty($datosSolicitud["gestiones"]) ? "disabled" : ""; ?>>
						<i class="bi bi-check-circle me-1" aria-hidden="true"></i>Registrar solicitud
					</button>
				</div>
			</form>
		</div>
	</div>
</div>