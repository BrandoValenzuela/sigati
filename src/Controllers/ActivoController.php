<?php
/**
 * SigaTI - Controlador: ActivoController (src/Controllers/ActivoController.php)
 */
if (count(get_included_files()) === 1) exit("Acceso denegado.");

class ActivoController {
    private $modelo;

    public function __construct($activoModelo) {
        $this->modelo = $activoModelo;
    }

    /**
     * Renderiza el Panel de Control Unificado (CU-02)
     */
    public function mostrarDashboard() {
        $kpis = $this->modelo->obtenerMetricasDashboard();
        $pageTitle = "Panel de Control";
        
        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/dashboard/index.php'; // Vista exclusiva del Dashboard
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Renderiza el Formulario de Alta (CU-03)
     */
    public function mostrarFormularioCrear($mensajeError = "") {
        $pageTitle = "Registrar Nuevo Activo";

        // 1. Cargamos las ubicaciones detalladas desde el modelo
        $ubicaciones = $this->modelo->obtenerUbicacionesCompletas();
        
        // 2. Cargamos los sistemas operativos (puedes mantenerlo como input o query simple)
        $sistemasOperativos = $this->modelo->obtenerSistemasOperativos();
        
        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/activos/crear.php'; // Vista exclusiva del Formulario
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Procesa la persistencia del equipo (CU-03)
     */
    public function procesarAlta($datosPost) {
        if (empty($datosPost['id_equipo']) || empty($datosPost['tipo_equipo']) || empty($datosPost['id_espacio']) || empty($datosPost['id_so']) || empty($datosPost['fecha_ultimo_preventivo'])) {
            $this->mostrarFormularioCrear("Todos los campos con asterisco (*) son obligatorios.");
            return;
        }

        try {
            if ($this->modelo->existeIdEquipo($datosPost['id_equipo'])) {
                $this->mostrarFormularioCrear("El código de equipo '" . htmlspecialchars($datosPost['id_equipo']) . "' ya se encuentra registrado.");
                return;
            }

            $this->modelo->guardar($datosPost);
            header("Location: index.php?action=dashboard&status=success");
            exit();
        } catch (Exception $e) {
            $this->mostrarFormularioCrear($e->getMessage());
        }
    }
}