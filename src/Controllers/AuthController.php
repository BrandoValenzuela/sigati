<?php
/**
 * SigaTI - Controlador: AuthController (src/Controllers/AuthController.php)
 */
if (count(get_included_files()) === 1) exit("Acceso denegado.");

class AuthController {
    private $usuarioModel;

    // Ahora inyectamos el Modelo de Usuario en lugar de la conexión directa
    public function __construct($usuarioModel) {
        $this->usuarioModel = $usuarioModel;
    }

    public function login($usuario, $password) {
        $usuario = trim(filter_var($usuario, FILTER_SANITIZE_SPECIAL_CHARS));

        if (empty($usuario) || empty($password)) {
            return "Por favor, completa todos los campos.";
        }

        // LE PEDIMOS AL MODELO QUE BUSQUE EN LA BD
        $admin = $this->usuarioModel->buscarPorUsuario($usuario);

        // EL CONTROLADOR VALIDA LA LÓGICA
        if ($admin && password_verify($password, $admin['password_hash'])) {
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['usuario_id']   = $admin['id_usuario'];
            $_SESSION['usuario_name'] = $admin['nombre_completo'];
            $_SESSION['logueado']     = true;

            // LE PEDIMOS AL MODELO QUE ACTUALICE EL ACCESO
            $this->usuarioModel->registrarAcceso($admin['id_usuario']);

            header("Location: " . BASE_URL . "public/index.php");
            exit();
        } else {
            return "Usuario o contraseña incorrectos.";
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        session_destroy();
        header("Location: " . BASE_URL . "public/index.php");
        exit();
    }
}