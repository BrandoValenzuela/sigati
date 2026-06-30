<?php 
/**
 * SigaTI - Vista: Crear Activo (src/Views/activos/crear.php)
 */
if (count(get_included_files()) === 1) exit("Acceso denegado."); 
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold text-dark m-0">
                <i class="bi bi-pc-display-horizontal text-primary me-2"></i>Registrar Equipo
            </h1>
            <p class="text-muted m-0">Asegurando consistencia en el inventario de hardware institucional.</p>
        </div>
        <div>
            <a href="index.php?action=dashboard" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </div>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $mensajeError; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
        <form action="index.php?action=procesar_alta" method="POST">
            <div class="row g-4">
                
                <div class="col-12 col-md-6">
                    <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                        <i class="bi bi-diagram-3 me-2"></i>Ubicación e Identificación
                    </h5>
                    
                    <div class="mb-3">
                        <label for="id_equipo" class="form-label fw-semibold">ID Equipo (Código de Inventario) *</label>
                        <input type="text" class="form-control" id="id_equipo" name="id_equipo" required 
                               placeholder="Ej: CBT-LAP-024"
                               value="<?php echo isset($_POST['id_equipo']) ? htmlspecialchars($_POST['id_equipo']) : ''; ?>">
                        <div class="form-text">Debe ser un identificador único para el sistema.</div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_equipo" class="form-label fw-semibold">Tipo de Equipo *</label>
                        <select class="form-select" id="tipo_equipo" name="tipo_equipo" required>
                            <?php $t = $_POST['tipo_equipo'] ?? ''; ?>
                            <option value="" disabled <?php echo $t === '' ? 'selected' : ''; ?>>Selecciona tipo...</option>
                            <option value="Escritorio" <?php echo $t === 'Escritorio' ? 'selected' : ''; ?>>Escritorio</option>
                            <option value="Laptop" <?php echo $t === 'Laptop' ? 'selected' : ''; ?>>Laptop</option>
                            <option value="All-in-One" <?php echo $t === 'All-in-One' ? 'selected' : ''; ?>>All-in-One</option>
                            <option value="Servidor" <?php echo $t === 'Servidor' ? 'selected' : ''; ?>>Servidor</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="mb-3">
                        <label for="id_espacio" class="form-label fw-semibold">Ubicación Exacta (Sección / Espacio) *</label>
                        <select class="form-select" id="id_espacio" name="id_espacio" required>
                            <option value="" disabled selected>Selecciona el espacio de destino...</option>
                            
                            <?php 
                            $seccionActual = '';
                            foreach ($ubicaciones as $ubi): 
                                // Genera un encabezado visual cada vez que cambia la sección
                                if ($seccionActual !== $ubi['nombre_seccion']): 
                                    if ($seccionActual !== '') echo '</optgroup>';
                                    $seccionActual = $ubi['nombre_seccion'];
                                    echo '<optgroup label="' . htmlspecialchars($seccionActual) . '">';
                                endif;
                            ?>
                                <option value="<?php echo $ubi['id_espacio']; ?>" 
                                    <?php echo (isset($_POST['id_espacio']) && $_POST['id_espacio'] == $ubi['id_espacio']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ubi['nombre_espacio']); ?>
                                </option>
                            <?php endforeach; ?>
                            
                            <?php if ($seccionActual !== '') echo '</optgroup>'; ?>
                        </select>
                        <div class="form-text">Organizado automáticamente por nivel institucional.</div>
                    </div>

                    <div class="mb-3">
                        <label for="id_so" class="form-label fw-semibold">Sistema Operativo *</label>
                        <select class="form-select" id="id_so" name="id_so" required>
                            <option value="" disabled selected>Selecciona el sistema operativo...</option>
                            <?php foreach ($sistemasOperativos as $so): ?>
                                <option value="<?php echo $so['id_so']; ?>"
                                    <?php echo (isset($_POST['id_so']) && $_POST['id_so'] == $so['id_so']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($so['nombre_so']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Asigna la plataforma base del equipo.</div>
                    </div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_conexion" class="form-label fw-semibold">Tipo de Conexión</label>
                        <select class="form-select" id="tipo_conexion" name="tipo_conexion">
                            <?php $tc = $_POST['tipo_conexion'] ?? ''; ?>
                            <option value="Ethernet" <?php echo $tc === 'Ethernet' ? 'selected' : ''; ?>>Ethernet (Cable)</option>
                            <option value="Wi-Fi" <?php echo $tc === 'Wi-Fi' ? 'selected' : ''; ?>>Wi-Fi (Inalámbrico)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="datos_conexion" class="form-label fw-semibold">Dirección IP o MAC</label>
                        <input type="text" class="form-control" id="datos_conexion" name="datos_conexion" 
                               placeholder="Ej: 192.168.1.50 u 00:1a:2b:3c:4d:5e"
                               value="<?php echo isset($_POST['datos_conexion']) ? htmlspecialchars($_POST['datos_conexion']) : ''; ?>">
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <h5 class="fw-bold text-success mb-3 pb-2 border-bottom">
                        <i class="bi bi-cpu me-2"></i>Especificaciones del Hardware
                    </h5>

                    <div class="mb-3">
                        <label for="procesador" class="form-label fw-semibold">Procesador *</label>
                        <input type="text" class="form-control" id="procesador" name="procesador" required 
                               list="procesadores_sugeridos" 
                               placeholder="Ej: Intel Core i5 11a Gen"
                               value="<?php echo isset($_POST['procesador']) ? htmlspecialchars($_POST['procesador']) : ''; ?>">
                        
                        <datalist id="procesadores_sugeridos">
                            <option value="Intel Core i3 7a Gen">
                            <option value="Intel Core i5 7a Gen">
                            <option value="Intel Core i7 7a Gen">
                            <option value="Intel Core i3 8a Gen">
                            <option value="Intel Core i5 8a Gen">
                            <option value="Intel Core i3 10a Gen">
                            <option value="Intel Core i5 10a Gen">
                            <option value="Intel Core i3 11a Gen">
                            <option value="Intel Core i5 11a Gen">
                            <option value="Intel Core i7 11a Gen">
                            <option value="Intel Core i5 12a Gen">
                            
                            <option value="Intel Celeron N4000 Series">
                            <option value="Intel Celeron N5000 Series">
                            <option value="Intel Celeron G Series (Desktop)">
                            <option value="Intel Celeron J Series">
                            
                            <option value="AMD Ryzen 3 Serie 3000">
                            <option value="AMD Ryzen 5 Serie 3000">
                            <option value="AMD Ryzen 3 Serie 4000">
                            <option value="AMD Ryzen 5 Serie 4000">
                            <option value="AMD Ryzen 5 Serie 5000">
                            <option value="AMD Ryzen 7 Serie 5000">
                            <option value="AMD Ryzen 5 Serie 7000">
                        </datalist>
                        <div class="form-text">Usa las sugerencias para asegurar que el semáforo de obsolescencia calcule el estado correcto en los reportes.</div>
                    </div>

                    <div class="mb-3">
                        <label for="memoria_ram" class="form-label fw-semibold">Memoria RAM</label>
                        <input type="text" class="form-control" id="memoria_ram" name="memoria_ram" 
                               placeholder="Ej: 16 GB DDR4"
                               value="<?php echo isset($_POST['memoria_ram']) ? htmlspecialchars($_POST['memoria_ram']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="almacenamiento" class="form-label fw-semibold">Almacenamiento (Disco)</label>
                        <input type="text" class="form-control" id="almacenamiento" name="almacenamiento" 
                               placeholder="Ej: 512 GB NVMe SSD"
                               value="<?php echo isset($_POST['almacenamiento']) ? htmlspecialchars($_POST['almacenamiento']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="monitor" class="form-label fw-semibold">Monitor / Pantalla</label>
                        <input type="text" class="form-control" id="monitor" name="monitor" 
                               placeholder="Ej: HP 24'' FHD / Integrada 15.6''"
                               value="<?php echo isset($_POST['monitor']) ? htmlspecialchars($_POST['monitor']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_ultimo_preventivo" class="form-label fw-semibold">Fecha Último Preventivo *</label>
                        <input type="date" class="form-control" id="fecha_ultimo_preventivo" name="fecha_ultimo_preventivo" required 
                               value="<?php echo isset($_POST['fecha_ultimo_preventivo']) ? htmlspecialchars($_POST['fecha_ultimo_preventivo']) : date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5 fw-bold">
                    <i class="bi bi-hdd-network me-2"></i>Guardar Activo en Inventario
                </button>
            </div>
        </form>
    </div>
</div>