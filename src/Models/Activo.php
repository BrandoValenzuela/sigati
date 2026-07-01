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

            // 3. KPI DINÁMICA: Equipos actualmente en mantenimiento / taller
            // Cuenta los códigos únicos de equipo en la bitácora que no tienen aún una acción técnica registrada
            $sqlMantenimiento = "SELECT COUNT(DISTINCT id_equipo) 
                                 FROM bitacora_mantenimiento 
                                 WHERE accion_tecnica IS NULL OR TRIM(accion_tecnica) = ''";
            $metricas['mantenimiento'] = (int)$this->db->query($sqlMantenimiento)->fetchColumn();

            // 4. Cálculo del semáforo operativo
            $metricas['operativos'] = $metricas['total'] - $metricas['vencidos'];

            
            
            return $metricas;
        } catch (PDOException $e) {
            // En caso de error, devolvemos el array en ceros para no romper la vista
            return $metricas;
        }
    }

    /**
     * Obtiene el listado de espacios físicos cruzados con su sección institucional
     */
    public function obtenerUbicacionesCompletas() {
        try {
            // Consulta exacta uniendo tus dos tablas mediante id_seccion
            $sql = "SELECT e.id_espacio, e.nombre_espacio, s.nombre_seccion 
                    FROM espacios_fisicos e
                    INNER JOIN secciones s ON e.id_seccion = s.id_seccion
                    ORDER BY s.nombre_seccion ASC, e.nombre_espacio ASC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    /**
     * Obtiene el listado completo de sistemas operativos
     */
    public function obtenerSistemasOperativos() {
        try {
            $sql = "SELECT id_so, nombre_so FROM sistemas_operativos ORDER BY nombre_so ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
/**
     * Obtiene el resumen gerencial por secciones mapeando obsolescencia sin duplicados.
     */
    public function obtenerResumenObsolescenciaPorSeccion() {
        try {
            $sql = "SELECT 
                        s.id_seccion,
                        s.nombre_seccion,
                        COUNT(DISTINCT a.id_equipo) AS total_equipos,
                        
                        -- Contamos de forma única asegurando que el ID no se repita en la agregación
                        COUNT(DISTINCT CASE WHEN p.estatus_ciclo_vida = 'ÓPTIMO' THEN a.id_equipo END) AS optimos,
                        
                        -- Si el equipo no tiene match o es funcional con reservas, cae aquí de forma única
                        COUNT(DISTINCT CASE WHEN a.id_equipo IS NOT NULL AND (p.estatus_ciclo_vida IS NULL OR p.estatus_ciclo_vida = 'FUNCIONAL CON RESERVAS') THEN a.id_equipo END) AS reservas,
                        
                        -- Contamos obsoletos de forma única
                        COUNT(DISTINCT CASE WHEN p.estatus_ciclo_vida = 'OBSOLETO' THEN a.id_equipo END) AS obsoletos
                    FROM secciones s
                    LEFT JOIN espacios_fisicos e ON s.id_seccion = e.id_seccion
                    LEFT JOIN activos a ON e.id_espacio = a.id_espacio
                    -- Agregamos una subconsulta o priorizamos el primer match para evitar la multiplicación de filas
                    LEFT JOIN parametros_obsolescencia p ON p.id_parametro = (
                        SELECT p2.id_parametro 
                        FROM parametros_obsolescencia p2 
                        WHERE a.procesador LIKE p2.patron_procesador 
                        LIMIT 1
                    )
                    GROUP BY s.id_seccion, s.nombre_seccion
                    ORDER BY s.nombre_seccion ASC";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    /**
     * Obtiene todos los activos pertenecientes a una sección específica,
     * calculando su obsolescencia de manera única.
     */
    public function obtenerActivosPorSeccion($id_seccion) {
        try {
            $sql = "SELECT 
                        a.id_equipo,
                        a.tipo_equipo,
                        a.procesador,
                        e.nombre_espacio,
                        so.nombre_so,
                        p.estatus_ciclo_vida,
                        p.accion_sugerida
                    FROM activos a
                    INNER JOIN espacios_fisicos e ON a.id_espacio = e.id_espacio
                    LEFT JOIN sistemas_operativos so ON a.id_so = so.id_so
                    LEFT JOIN parametros_obsolescencia p ON p.id_parametro = (
                        SELECT p2.id_parametro 
                        FROM parametros_obsolescencia p2 
                        WHERE a.procesador LIKE p2.patron_procesador 
                        LIMIT 1
                    )
                    WHERE e.id_seccion = :id_seccion
                    ORDER BY a.id_equipo ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_seccion' => $id_seccion]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    /**
     * Obtiene el nombre de una sección directamente para los títulos de la interfaz.
     */
    public function obtenerNombreSeccion($id_seccion) {
        try {
            $sql = "SELECT nombre_seccion FROM secciones WHERE id_seccion = :id_seccion LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_seccion' => $id_seccion]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado ? $resultado['nombre_seccion'] : 'Desconocida';
        } catch (PDOException $e) {
            return 'Detalle';
        }
    }
}