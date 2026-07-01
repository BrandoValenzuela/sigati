<?php if (count(get_included_files()) === 1) exit("Acceso denegado."); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold text-dark m-0">
                <i class="bi bi-pie-chart-fill text-primary me-2"></i>Estado del Parque Tecnológico
            </h1>
            <p class="text-muted">Distribución y obsolescencia del hardware segmentado por secciones institucionales.</p>
        </div>
        <div>
            <a href="index.php?action=dashboard" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($resumenSecciones as $sec): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-white pivot-card">
                    <div class="card-body d-flex flex-column p-4">
                        <!-- Título de Sección -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($sec['nombre_seccion']); ?></h4>
                            <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold">
                                <?php echo $sec['total_equipos']; ?> Equipos
                            </span>
                        </div>
                        
                        <hr class="text-muted my-2">
                        
                        <!-- Distribución / Semáforo de Obsolescencia -->
                        <div class="my-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-medium"><i class="bi bi-shield-check text-success me-1"></i> Óptimos:</span>
                                <span class="badge bg-success-subtle text-success fw-bold"><?php echo (int)$sec['optimos']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-medium"><i class="bi bi-exclamation-circle text-warning me-1"></i> Con Reservas:</span>
                                <span class="badge bg-warning-subtle text-warning fw-bold"><?php echo (int)$sec['reservas']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-medium"><i class="bi bi-x-octagon text-danger me-1"></i> Obsoletos:</span>
                                <span class="badge bg-danger-subtle text-danger fw-bold"><?php echo (int)$sec['obsoletos']; ?></span>
                            </div>
                        </div>

                        <!-- Acción para bajar al detalle -->
                        <div class="mt-3">
                            <a href="index.php?action=listar_activos&id_seccion=<?php echo $sec['id_seccion']; ?>" 
                               class="btn btn-outline-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2">
                                Ver Equipos <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>