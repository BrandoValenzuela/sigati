<?php if (count(get_included_files()) === 1) exit("Acceso denegado."); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold text-dark m-0">
                <i class="bi bi-pc-display-horizontal text-primary me-2"></i>Equipos Registrados
            </h1>
            <p class="text-muted m-0">Asegurando consistencia en el inventario de hardware institucional.</p>
        </div>
        <div>
            <a href="index.php?action=ver_activos" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="tablaBuscador" class="form-control border-start-0" 
                       placeholder="Filtrar por identificador, procesador, espacio físico...">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaActivos">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Identificador</th>
                        <th>Tipo</th>
                        <th>Espacio Físico</th>
                        <th>Procesador</th>
                        <th>Sistema Operativo</th>
                        <th>Obsolescencia</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activos)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-laptop shadow-sm display-6 d-block mb-3 text-secondary"></i>
                                No se encontraron activos informáticos registrados en esta sección.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activos as $act): ?>
                            <tr>
                                <td class="fw-bold text-dark ps-4"><?php echo htmlspecialchars($act['id_equipo']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo htmlspecialchars($act['tipo_equipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($act['nombre_espacio']); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars($act['procesador']); ?></td>
                                <td><?php echo htmlspecialchars($act['nombre_so'] ?? 'No especificado'); ?></td>
                                <td>
                                    <?php 
                                    $estatus = $act['estatus_ciclo_vida'] ?? 'FUNCIONAL CON RESERVAS';
                                    if ($estatus === 'ÓPTIMO') {
                                        echo '<span class="badge bg-success-subtle text-success fw-bold px-2 py-1"><i class="bi bi-shield-check me-1"></i>Óptimo</span>';
                                    } elseif ($estatus === 'OBSOLETO') {
                                        echo '<span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1"><i class="bi bi-x-octagon me-1"></i>Obsoleto</span>';
                                    } else {
                                        echo '<span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1"><i class="bi bi-exclamation-circle me-1"></i>Con Reservas</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="index.php?action=editar_activo&id=<?php echo urlencode($act['id_equipo']); ?>" 
                                           class="btn btn-outline-primary" title="Editar Activo">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="index.php?action=historial_mantenimiento&id=<?php echo urlencode($act['id_equipo']); ?>" 
                                           class="btn btn-outline-secondary" title="Historial Clínico">
                                            <i class="bi bi-wrench"></i>
                                        </a>
                                        <a href="index.php?action=crear_reporte&id_equipo=<?php echo urlencode($act['id_equipo']); ?>" 
                                            class="btn btn-outline-danger fw-semibold" title="Levantar Reporte / Falla">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Reportar
                                        </a>
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

<script>
document.getElementById('tablaBuscador').addEventListener('keyup', function() {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tablaActivos tbody tr');
    
    rows.forEach(row => {
        // Ignorar la fila de "No se encontraron activos" si existe
        if(row.cells.length > 1) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        }
    });
});
</script>