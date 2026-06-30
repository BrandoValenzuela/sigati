<?php if (count(get_included_files()) === 1) exit("Acceso denegado."); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold text-dark m-0">Panel de Control</h1>
            <p class="text-muted m-0">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario_name']); ?></strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?action=nuevo_activo" class="btn btn-primary d-flex align-items-center gap-2 px-3">
                <i class="bi bi-plus-circle"></i> Nuevo Activo
            </a>
            <a href="index.php?action=logout" class="btn btn-outline-danger d-flex align-items-center gap-2 px-3">
                <i class="bi bi-box-arrow-right"></i> Salir del Sistema
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi kpi-total p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="kpi-title">Total Activos</span>
                    <i class="bi bi-pc-display kpi-icon text-primary"></i>
                </div>
                <span class="kpi-value"><?php echo $kpis['total']; ?></span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi kpi-operativos p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="kpi-title">En Operación</span>
                    <i class="bi bi-check-circle-fill kpi-icon text-success"></i>
                </div>
                <span class="kpi-value"><?php echo $kpis['operativos']; ?></span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi kpi-mantenimiento p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="kpi-title">Mantenimiento</span>
                    <i class="bi bi-tools kpi-icon text-warning"></i>
                </div>
                <span class="kpi-value"><?php echo $kpis['mantenimiento']; ?></span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi kpi-vencidos p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="kpi-title">Preventivos Vencidos</span>
                    <i class="bi bi-exclamation-octagon-fill kpi-icon text-danger"></i>
                </div>
                <span class="kpi-value"><?php echo $kpis['vencidos']; ?></span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="p-4 bg-white rounded shadow-sm" style="min-height: 250px;">
                <h5 class="fw-bold mb-3"><i class="bi bi-mailbox me-2"></i> Buzón de Incidencias Activas</h5>
                <p class="text-muted small">Módulo estructurado en espera...</p>
            </div>
        </div>
        <div class="col-md-5">
            <div class="p-4 bg-white rounded shadow-sm" style="min-height: 250px;">
                <h5 class="fw-bold mb-3"><i class="bi bi-cart3 me-2"></i> Carrito Técnico</h5>
                <p class="text-muted small">Módulo estructurado en espera...</p>
            </div>
        </div>
    </div>
</div>