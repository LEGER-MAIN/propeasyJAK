<?php
/**
 * Modelo ReporteIrregularidad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja las operaciones relacionadas con los reportes de irregularidades
 */

require_once APP_PATH . '/core/Database.php';

class ReporteIrregularidad {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Crear un nuevo reporte de irregularidad
     * 
     * @param array $data Datos del reporte
     * @return int|false ID del reporte creado o false si falla
     */
    public function crear($data) {
        $sql = "INSERT INTO reportes_irregularidades (
            usuario_id, tipo_reporte, titulo, descripcion, archivo_adjunto, estado
        ) VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $data['usuario_id'],
            $data['tipo_reporte'],
            $data['titulo'],
            $data['descripcion'],
            $data['archivo_adjunto'] ?? null,
            REPORT_STATUS_PENDING
        ]);
    }
    
    /**
     * Obtener un reporte por ID
     * 
     * @param int $id ID del reporte
     * @return array|false Datos del reporte o false si no existe
     */
    public function obtenerPorId($id) {
        $sql = "SELECT r.*, u.nombre, u.apellido, u.email, u.telefono,
                       a.nombre as admin_nombre, a.apellido as admin_apellido
                FROM reportes_irregularidades r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                LEFT JOIN usuarios a ON r.admin_responsable_id = a.id
                WHERE r.id = ?";
        
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Obtener todos los reportes con filtros opcionales
     * 
     * @param array $filtros Filtros opcionales
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de reportes
     */
    public function obtenerTodos($filtros = [], $limit = 50, $offset = 0) {
        $sql = "SELECT r.*, u.nombre, u.apellido, u.email, u.telefono,
                       a.nombre as admin_nombre, a.apellido as admin_apellido
                FROM reportes_irregularidades r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                LEFT JOIN usuarios a ON r.admin_responsable_id = a.id";
        
        $whereConditions = [];
        $params = [];
        
        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $whereConditions[] = "r.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        if (!empty($filtros['tipo_reporte'])) {
            $whereConditions[] = "r.tipo_reporte = ?";
            $params[] = $filtros['tipo_reporte'];
        }
        
        if (!empty($filtros['usuario_id'])) {
            $whereConditions[] = "r.usuario_id = ?";
            $params[] = $filtros['usuario_id'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $whereConditions[] = "r.fecha_reporte >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $whereConditions[] = "r.fecha_reporte <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        $sql .= " ORDER BY r.fecha_reporte DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Obtener reportes de un usuario específico
     * 
     * @param int $usuarioId ID del usuario
     * @return array Lista de reportes del usuario
     */
    public function obtenerPorUsuario($usuarioId) {
        $sql = "SELECT * FROM reportes_irregularidades 
                WHERE usuario_id = ? 
                ORDER BY fecha_reporte DESC";
        
        return $this->db->select($sql, [$usuarioId]);
    }
    
    /**
     * Actualizar el estado de un reporte
     * 
     * @param int $id ID del reporte
     * @param string $estado Nuevo estado
     * @param string $respuesta Respuesta del administrador
     * @param int $adminId ID del administrador responsable
     * @return bool True si se actualizó correctamente
     */
    public function actualizarEstado($id, $estado, $respuesta = null, $adminId = null) {
        $sql = "UPDATE reportes_irregularidades 
                SET estado = ?, respuesta_admin = ?, admin_responsable_id = ?, 
                    fecha_respuesta = CURRENT_TIMESTAMP
                WHERE id = ?";
        
        return $this->db->update($sql, [$estado, $respuesta, $adminId, $id]) !== false;
    }
    
    /**
     * Eliminar un reporte
     * 
     * @param int $id ID del reporte
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        // Primero obtener el reporte para eliminar el archivo adjunto si existe
        $reporte = $this->obtenerPorId($id);
        if ($reporte && !empty($reporte['archivo_adjunto'])) {
            $rutaArchivo = PUBLIC_PATH . '/uploads/reportes/' . $reporte['archivo_adjunto'];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
        }
        
        $sql = "DELETE FROM reportes_irregularidades WHERE id = ?";
        return $this->db->delete($sql, [$id]) !== false;
    }
    
    /**
     * Obtener estadísticas de reportes
     * 
     * @return array Estadísticas de reportes
     */
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                    COUNT(*) as total_reportes,
                    COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes,
                    COUNT(CASE WHEN estado = 'atendido' THEN 1 END) as atendidos,
                    COUNT(CASE WHEN estado = 'descartado' THEN 1 END) as descartados,
                    COUNT(CASE WHEN tipo_reporte = 'queja_agente' THEN 1 END) as quejas_agente,
                    COUNT(CASE WHEN tipo_reporte = 'problema_plataforma' THEN 1 END) as problemas_plataforma,
                    COUNT(CASE WHEN tipo_reporte = 'informacion_falsa' THEN 1 END) as informacion_falsa,
                    COUNT(CASE WHEN tipo_reporte = 'otro' THEN 1 END) as otros
                FROM reportes_irregularidades";
        
        return $this->db->selectOne($sql) ?: [];
    }
    
    /**
     * Obtener reportes pendientes
     * 
     * @return array Lista de reportes pendientes
     */
    public function obtenerPendientes() {
        $sql = "SELECT r.*, u.nombre, u.apellido, u.email, u.telefono
                FROM reportes_irregularidades r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.estado = 'pendiente'
                ORDER BY r.fecha_reporte ASC";
        
        return $this->db->select($sql);
    }
    
    /**
     * Contar reportes por estado
     * 
     * @return array Conteo de reportes por estado
     */
    public function contarPorEstado() {
        $sql = "SELECT estado, COUNT(*) as cantidad
                FROM reportes_irregularidades
                GROUP BY estado";
        
        $resultados = $this->db->select($sql);
        $conteo = [];
        
        foreach ($resultados as $row) {
            $conteo[$row['estado']] = $row['cantidad'];
        }
        
        return $conteo;
    }
    
    /**
     * Obtener tipos de reporte disponibles
     * 
     * @return array Lista de tipos de reporte
     */
    public function obtenerTiposReporte() {
        return [
            'queja_agente' => 'Queja contra Agente',
            'problema_plataforma' => 'Problema con la Plataforma',
            'informacion_falsa' => 'Información Falsa',
            'otro' => 'Otro'
        ];
    }
    
    /**
     * Obtener estados disponibles
     * 
     * @return array Lista de estados
     */
    public function obtenerEstados() {
        return [
            'pendiente' => 'Pendiente',
            'atendido' => 'Atendido',
            'descartado' => 'Descartado'
        ];
    }
    
    /**
     * Validar datos del reporte
     * 
     * @param array $data Datos a validar
     * @return array Array con errores (vacío si no hay errores)
     */
    public function validar($data) {
        $errores = [];
        
        if (empty($data['titulo'])) {
            $errores[] = 'El título es obligatorio';
        } elseif (strlen($data['titulo']) > 255) {
            $errores[] = 'El título no puede exceder 255 caracteres';
        }
        
        if (empty($data['descripcion'])) {
            $errores[] = 'La descripción es obligatoria';
        } elseif (strlen($data['descripcion']) < 10) {
            $errores[] = 'La descripción debe tener al menos 10 caracteres';
        }
        
        if (empty($data['tipo_reporte'])) {
            $errores[] = 'El tipo de reporte es obligatorio';
        } elseif (!in_array($data['tipo_reporte'], array_keys($this->obtenerTiposReporte()))) {
            $errores[] = 'El tipo de reporte no es válido';
        }
        
        if (empty($data['usuario_id'])) {
            $errores[] = 'El usuario es obligatorio';
        }
        
        return $errores;
    }
    
    /**
     * Obtener total de reportes
     * 
     * @return int Total de reportes
     */
    public function getTotalReportes() {
        $query = "SELECT COUNT(*) as total FROM reportes_irregularidades";
        $resultado = $this->db->selectOne($query);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener reportes por estado
     * 
     * @param string $status Estado de los reportes
     * @return int Total de reportes con ese estado
     */
    public function getReportesByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM reportes_irregularidades WHERE estado = ?";
        $resultado = $this->db->selectOne($query, [$status]);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener reportes recientes
     * 
     * @param int $limit Límite de reportes
     * @return array Lista de reportes recientes
     */
    public function getRecentReportes($limit = 10) {
        $query = "SELECT r.*, u.nombre, u.apellido, u.email 
                  FROM reportes_irregularidades r
                  LEFT JOIN usuarios u ON r.usuario_id = u.id
                  ORDER BY r.fecha_reporte DESC 
                  LIMIT ?";
        return $this->db->select($query, [$limit]);
    }
} 