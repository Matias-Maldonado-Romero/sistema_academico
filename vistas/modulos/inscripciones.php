<?php
$datosInscripcion = (new ControladorInscripciones())->ctrGestionarInscripcion();
?>
<div class="page-content">
	<section class="section">
		<?php if ($datosInscripcion["mensaje"] !== ""): ?>
			<div class="d-none" data-swal-feedback data-swal-feedback-type="<?php echo htmlspecialchars($datosInscripcion["tipo"], ENT_QUOTES, "UTF-8"); ?>">
				<?php echo htmlspecialchars($datosInscripcion["mensaje"], ENT_QUOTES, "UTF-8"); ?>
			</div>
		<?php endif; ?>

		<div class="d-flex justify-content-end mb-4">
			<button type="button" class="btn btn-primary btn-lg">
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
								$estadoColor = $estadoActual === "aprobada" ? "success" : ($estadoActual === "rechazada" ? "danger" : "warning");
							?>
							<tr>
								<td><?php echo htmlspecialchars($inscripcion["estudiante"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars($inscripcion["rude"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars($inscripcion["curso"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars((string) $inscripcion["anio"], ENT_QUOTES, "UTF-8"); ?></td>
								<td>
									<span class="badge bg-<?php echo $estadoColor; ?> text-white">
										<?php echo htmlspecialchars(ucfirst((string) ($inscripcion["estado"] ?? "pendiente")), ENT_QUOTES, "UTF-8"); ?>
									</span>
								</td>
								<td>
									<div class="d-flex gap-2">
										<form method="POST" action="index.php?ruta=inscripciones" class="d-inline">
											<input type="hidden" name="id_inscripcion" value="<?php echo (int) $inscripcion["id_inscripcion"]; ?>">
											<input type="hidden" name="estado" value="aprobada">
											<button type="submit" name="actualizarEstadoInscripcion" value="1" class="btn btn-success btn-sm">Aprobar</button>
										</form>
										<form method="POST" action="index.php?ruta=inscripciones" class="d-inline">
											<input type="hidden" name="id_inscripcion" value="<?php echo (int) $inscripcion["id_inscripcion"]; ?>">
											<input type="hidden" name="estado" value="rechazada">
											<button type="submit" name="actualizarEstadoInscripcion" value="1" class="btn btn-danger btn-sm">Rechazar</button>
										</form>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
</div>