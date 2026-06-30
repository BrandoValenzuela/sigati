<?php
/**
 * SigaTI - Front Controller Oficial (public/index.php)
 */
define('BASE_URL', 'http://localhost/sigati/');
define('APP_NAME', 'SigaTI');
session_start();

// 1. Carga estricta de dependencias estructurales
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Usuario.php';
require_once __DIR__ . '/../src/Models/Activo.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/ActivoController.php';

// 2. Inicialización de componentes de infraestructura
$db = Connection::getInstance()->getConnection();
$authController = new AuthController(new Usuario($db));
$activoController = new ActivoController(new Activo($db));

// 3. Captura de la acción solicitada por el usuario
$action = $_GET['action'] ?? 'dashboard';

// 4. Búnker de Seguridad Perimetral: Si no hay sesión, se fuerza el flujo de Auth
if (!isset($_SESSION['usuario_id']) && !in_array($action, ['login', 'procesar_login'])) {
    header("Location: index.php?action=login");
    exit();
}

// 5. Enrutador Limpio y Despachador General (Sin código de lógica mezclado)
switch ($action) {
    // --- RUTAS DE AUTENTICACIÓN (CU-01) ---
    case 'login':
        $authController->mostrarLogin();
        break;
        
    case 'procesar_login':
        $authController->procesarLogin($_POST);
        break;
        
    case 'logout':
        $authController->logout();
        break;

    // --- RUTAS DEL PANEL DE CONTROL (CU-02) ---
    case 'dashboard':
        $activoController->mostrarDashboard();
        break;

    // --- RUTAS DE GESTIÓN DE EQUIPOS (CU-03) ---
    case 'nuevo_activo':
        $activoController->mostrarFormularioCrear();
        break;

    case 'procesar_alta':
        $activoController->procesarAlta($_POST);
        break;

    default:
        header("Location: index.php?action=dashboard");
        exit();
}