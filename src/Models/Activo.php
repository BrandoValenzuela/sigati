<?php
/**
 * SigaTI - Sistema de Gestión de Activos de TI
 * Modelo: Activo (src/Models/Activo.php)
 */

if (count(get_included_files()) === 1) exit("Acceso denegado.");

class Activo {
    private $db;

    // Inyectamos la conexión PDO en el constructor
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Obtiene la lista completa de activos con los nombres de su espacio físico y sistema operativo.
     * Calcula dinámicamente los semáforos de cada equipo al vuelo.
     */
    public function obtenerTodos() {
        $sql = "SELECT a.*, e.nombre_space as espacio, s.nombre_so as sistema_operativo 
                FROM activos a
                INNER JOIN espacios_fisicos e ON a.id_espacio = e.id_espacio
                INNER JOIN sistemas_operativos s ON a.id_so = s.id_so
                ORDER BY a.id_equipo ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $equipos = $stmt->fetchAll();

        // Enriquecer cada equipo con los semáforos calculados automáticamente
        foreach ($equipos as &$equipo) {
            $equipo['obsolescencia'] = $this->calcularCicloDeVida($equipo['procesador']);
            $equipo['semaforo_preventivo'] = $this->obtenerSemaforoPreventivo($equipo['fecha_ultimo_preventivo']);
        }

        return $equipos;
    }

    /**
     * Busca un activo específico por su ID alfanumérico (Ideal para el futuro código QR).
     */
    public function obtenerPorId($id_equipo) {
        $sql = "SELECT a.*, e.nombre_space as espacio, s.nombre_so as sistema_operativo 
                FROM activos a
                INNER JOIN espacios_fisicos e ON a.id_espacio = e.id_espacio
                INNER JOIN sistemas_operativos s ON a.id_so = s.id_so
                WHERE a.id_equipo = :id_equipo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_equipo' => $id_equipo]);
        $equipo = $stmt->fetch();

        if ($equipo) {
            $equipo['obsolescencia'] = $this->calcularCicloDeVida($equipo['procesador']);
            $equipo['semaforo_preventivo'] = $this->obtenerSemaforoPreventivo($equipo['fecha_ultimo_preventivo']);
        }

        return $equipo;
    }

    /**
     * Inserta una nueva ficha técnica en la base de datos.
     */
    public function registrar($datos) {
        $sql = "INSERT INTO activos (id_equipo, tipo_equipo, id_espacio, id_so, procesador, memoria_ram, almacenamiento, monitor, datos_conexion, tipo_conexion, fecha_ultimo_preventivo) 
                VALUES (:id_equipo, :tipo_equipo, :id_espacio, :id_so, :procesador, :memoria_ram, :almacenamiento, :monitor, :datos_conexion, :tipo_conexion, :fecha_ultimo_preventivo)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_equipo'               => $datos['id_equipo'],
            ':tipo_equipo'             => $datos['tipo_equipo'],
            ':id_espacio'              => $datos['id_espacio'],
            ':id_so'                   => $datos['id_so'],
            ':procesador'              => $datos['procesador'],
            ':memoria_ram'             => $datos['memoria_ram'],
            ':almacenamiento'          => $datos['almacenamiento'],
            ':monitor'                 => $datos['monitor'] ?? 'Ninguno',
            ':datos_conexion'          => $datos['datos_conexion'] ?? null,
            ':tipo_conexion'           => $datos['tipo_conexion'],
            ':fecha_ultimo_preventivo' => $datos['fecha_ultimo_preventivo']
        ]);
    }

    /**
     * ALGORITMO 1: Procesa el texto del procesador contra la matriz de parámetros
     */
    private function calcularCicloDeVida($procesadorTexto) {
        $sql = "SELECT patron_procesador, estatus_ciclo_vida, accion_sugerida FROM parametros_obsolescencia";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $parametros = $stmt->fetchAll();

        $procesadorLimpio = strtolower($procesadorTexto);

        foreach ($parametros as $param) {
            // Limpiamos los comodines SQL '%' para la evaluación en PHP
            $patronLimpio = strtolower(str_replace('%', '', $param['patron_procesador']));

            if (str_contains($procesadorLimpio, $patronLimpio)) {
                return [
                    'estatus' => $param['estatus_ciclo_vida'],
                    'accion'  => $param['accion_sugerida']
                ];
            }
        }

        // Caso por defecto (Estrategia de resguardo del MVP)
        return [
            'estatus' => 'FUNCIONAL CON RESERVAS',
            'accion'  => 'Procesador no identificado en la matriz maestra. Requiere evaluación manual.'
        ];
    }

    /**
     * ALGORITMO 2: Evalúa los meses transcurridos desde el último preventivo
     */
    private function obtenerSemaforoPreventivo($fechaUltimoPreventivo) {
        $fechaPasada = new DateTime($fechaUltimoPreventivo);
        $fechaActual = new DateTime();
        
        $diferencia = $fechaPasada->diff($fechaActual);
        $mesesTranscurridos = ($diferencia->y * 12) + $diferencia->m;

        if ($mesesTranscurridos < 6) {
            return [
                'estatus'   => 'Al Corriente',
                'clase_css' => 'badge-success', // Verde
                'alerta'    => false
            ];
        } elseif ($mesesTranscurridos >= 6 && $mesesTranscurridos <= 9) {
            return [
                'estatus'   => 'Próximo',
                'clase_css' => 'badge-warning', // Amarillo
                'alerta'    => false
            ];
        } else {
            return [
                'estatus'   => 'Vencido / Alerta',
                'clase_css' => 'badge-danger',  // Rojo
                'alerta'    => true
            ];
        }
    }
}