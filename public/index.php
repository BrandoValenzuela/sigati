<?php
/**
 * SigaTI - Sistema de Gestión de Activos de TI
 * Front Controller Centralizado (public/index.php)
 */

// 1. Inicializar el manejo de sesiones de PHP de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusión de configuraciones e infraestructura central
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// 3. Inclusión de Controladores y Modelos necesarios para este bloque
require_once __DIR__ . '/../src/Models/Usuario.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Models/Activo.php';

// 4. Inicializar la conexión única a la Base de Datos y Controladores
$db = getDatabaseConnection();
$usuarioModel = new Usuario($db);
$authController = new AuthController($usuarioModel);

// 5. CAPTURA DE ACCIONES DE AUTENTICACIÓN
$errorLogin = "";

// Si el usuario presiona el botón de acceder en la vista que acabamos de diseñar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_login'])) {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // El controlador procesa la seguridad. Si falla, regresa el string con el error.
    $errorLogin = $authController->login($usuario, $password);
}

// Si se solicita explícitamente el cierre de sesión por URL (?action=logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $authController->logout();
}

// 6. CONTROLADOR PERIMETRAL DE RUTA (¿Está logueado?)
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    // Si NO está logueado, detenemos todo y pintamos de inmediato el Login con la nueva estética
    include __DIR__ . '/../src/Views/auth/login.php';
    exit();
}

/**
 * =========================================================================
 * ÁREA PROTEGIDA: DASHBOARD DE SIGATI (CU-02)
 * Si el flujo llega aquí, significa que la sesión es 100% válida.
 * =========================================================================
 */
$activoModel = new Activo($db);
$inventario = $activoModel->obtenerTodos(); // Esto alimentará tus KPIs más adelante

// Por el momento, renderizamos una estructura temporal para confirmar que entraste
$pageTitle = "Panel de Control";
include __DIR__ . '/../src/Views/layouts/header.php';
?>

<div class="container my-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold text-dark m-0">Panel de Control</h1>
            <p class="text-muted m-0">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario_name']); ?></strong> <span class="badge bg-secondary">admin</span></p>
        </div>
        <div>
            <a href="?action=logout" class="btn btn-danger d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </div>
    </div>

    <div class="alert alert-success shadow-sm p-4" role="alert">
        <h4 class="alert-heading fw-bold">¡Autenticación Exitosa! 🎉</h4>
        <p class="m-0">El sistema de sesiones seguras está funcionando de manera impecable bajo el patrón MVC de SigaTI. Has superado el <strong>CU-01 (Autenticación)</strong> de la etapa de desarrollo.</p>
        <hr>
        <p class="mb-0 small text-muted">Próximo paso operativo: Transformar este espacio en el Dashboard definitivo con las métricas, buzón de incidencias y el carrito técnico de refacciones.</p>
    </div>
</div>

<?php 
include __DIR__ . '/../src/Views/layouts/footer.php'; 
?>