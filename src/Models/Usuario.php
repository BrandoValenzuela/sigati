<?php
/**
 * SigaTI - Modelo: Usuario (src/Models/Usuario.php)
 */
if (count(get_included_files()) === 1) exit("Acceso denegado.");

class Usuario {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Busca un administrador por su nombre de usuario
     */
    public function buscarPorUsuario($usuario) {
        $sql = "SELECT id_usuario, usuario, password_hash, nombre_completo 
                FROM usuarios_admin 
                WHERE usuario = :usuario LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario' => $usuario]);
        return $stmt->fetch();
    }

    /**
     * Registra la estampa de tiempo del último acceso
     */
    public function registrarAcceso($id_usuario) {
        $sql = "UPDATE usuarios_admin SET ultimo_acceso = NOW() WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
    }
}