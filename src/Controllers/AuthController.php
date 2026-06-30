<?php
/**
 * SigaTI - Controlador: AuthController (src/Controllers/AuthController.php)
 */
if (count(get_included_files()) === 1) exit("Acceso denegado.");

class AuthController {
    private $usuarioModel;

    public function __construct($usuarioModelo) {
        $this->usuarioModel = $usuarioModelo;
    }

    public function mostrarLogin($errorLogin = "") {
        $pageTitle = "Iniciar Sesión";
        include __DIR__ . '/../Views/auth/login.php';
    }

    public function procesarLogin($datosPost) {
        $username = trim($datosPost['usuario'] ?? '');
        $password = trim($datosPost['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->mostrarLogin("Por favor, llena todos los campos.");
            return;
        }

        $usuario = $this->usuarioModel->buscarPorUsuario($username);

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_name'] = $usuario['nombre'];
            header("Location: index.php?action=dashboard");
            exit();
        } else {
            $this->mostrarLogin("Credenciales incorrectas. Inténtalo de nuevo.");
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}