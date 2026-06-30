<?php
/**
 * SigaTI - Modelo: Activo (src/Models/Activo.php)
 */
if (count(get_included_files()) === 1) exit("Acceso denegado.");

class Activo {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Verifica si un ID de equipo ya existe en la base de datos (Paso 6)
     */
    public function existeIdEquipo($id_equipo) {
        $sql = "SELECT COUNT(*) FROM activos WHERE id_equipo = :id_equipo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_equipo' => $id_equipo]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Inserta el registro en la tabla activos (Paso 7)
     */
    public function guardar($datos) {
        try {
            $sql = "INSERT INTO activos (
                        id_equipo, tipo_equipo, id_espacio, id_so, procesador, 
                        memoria_ram, almacenamiento, monitor, datos_conexion, 
                        tipo_conexion, fecha_ultimo_preventivo
                    ) VALUES (
                        :id_equipo, :tipo_equipo, :id_espacio, :id_so, :procesador, 
                        :memoria_ram, :almacenamiento, :monitor, :datos_conexion, 
                        :tipo_conexion, :fecha_ultimo_preventivo
                    )";

            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':id_equipo'               => $datos['id_equipo'],
                ':tipo_equipo'             => $datos['tipo_equipo'],
                ':id_espacio'              => (int)$datos['id_espacio'],
                ':id_so'                   => (int)$datos['id_so'],
                ':procesador'              => $datos['procesador'] ?: null,
                ':memoria_ram'             => $datos['memoria_ram'] ?: null,
                ':almacenamiento'          => $datos['almacenamiento'] ?: null,
                ':monitor'                 => $datos['monitor'] ?: null,
                ':datos_conexion'          => $datos['datos_conexion'] ?: null,
                ':tipo_conexion'           => $datos['tipo_conexion'] ?: null,
                ':fecha_ultimo_preventivo' => $datos['fecha_ultimo_preventivo']
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error interno en el servidor: " . $e->getMessage());
        }
    }

/**
     * Mantiene las KPIs del Dashboard consultando la tabla 'activos'
     */
    public function obtenerMetricasDashboard() {
        $metricas = ['total' => 0, 'operativos' => 0, 'mantenimiento' => 0, 'vencidos' => 0];
        try {
            // 1. Contar el total real en la tabla 'activos'
            $metricas['total'] = (int)$this->db->query("SELECT COUNT(*) FROM activos")->fetchColumn();
            
            // 2. Contar los preventivos vencidos (más de 180 días) usando tu columna exacta
            $sqlVencidos = "SELECT COUNT(*) FROM activos WHERE fecha_ultimo_preventivo < DATE_SUB(NOW(), INTERVAL 180 DAY)";
            $metricas['vencidos'] = (int)$this->db->query($sqlVencidos)->fetchColumn();

            // 3. Cálculo del semáforo operativo
            $metricas['operativos'] = $metricas['total'] - $metricas['vencidos'];
            
            return $metricas;
        } catch (PDOException $e) {
            // En caso de error, devolvemos el array en ceros para no romper la vista
            return $metricas;
        }
    }
}