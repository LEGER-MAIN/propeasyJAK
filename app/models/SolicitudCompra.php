<?php
/**
 * Modelo SolicitudCompra
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja todas las operaciones relacionadas con las solicitudes de compra
 * de propiedades por parte de los clientes.
 */

class SolicitudCompra {
    private $db;
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Crear una nueva solicitud de compra
     * 
     * @param array $data Datos de la solicitud
     * @return int|false ID de la solicitud creada o false si falla
     */
    public function crear($data) {
        $sql = "INSERT INTO solicitudes_compra (
            propiedad_id, cliente_id, agente_id, nombre_cliente, 
            email_cliente, telefono_cliente, mensaje, presupuesto_min, 
            presupuesto_max, estado, fecha_solicitud
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['propiedad_id'],
            $data['cliente_id'],
            $data['agente_id'],
            $data['nombre_cliente'],
            $data['email_cliente'],
            $data['telefono_cliente'],
            $data['mensaje'] ?? null,
            $data['presupuesto_min'] ?? null,
            $data['presupuesto_max'] ?? null,
            REQUEST_STATUS_NEW
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Obtener una solicitud por ID
     * 
     * @param int $id ID de la solicitud
     * @return array|false Datos de la solicitud o false si no existe
     */
    public function obtenerPorId($id) {
        $sql = "SELECT sc.*, 
                       p.titulo as titulo_propiedad,
                       p.precio as precio_propiedad,
                       p.moneda as moneda_propiedad,
                       p.ciudad as ciudad_propiedad,
                       p.sector as sector_propiedad,
                       p.direccion as direccion_propiedad,
                       ua.nombre as nombre_agente,
                       ua.apellido as apellido_agente,
                       ua.email as email_agente,
                       ua.telefono as telefono_agente,
                       uc.nombre as nombre_cliente,
                       uc.apellido as apellido_cliente,
                       uc.email as email_cliente,
                       uc.telefono as telefono_cliente
                FROM solicitudes_compra sc
                INNER JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios ua ON sc.agente_id = ua.id
                INNER JOIN usuarios uc ON sc.cliente_id = uc.id
                WHERE sc.id = ?";
        
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Alias para obtenerPorId - mantener compatibilidad
     * 
     * @param int $id ID de la solicitud
     * @return array|false Datos de la solicitud o false si no existe
     */
    public function getById($id) {
        return $this->obtenerPorId($id);
    }
    
    /**
     * Obtener todas las solicitudes de un cliente
     * 
     * @param int $clienteId ID del cliente
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de solicitudes
     */
    public function obtenerPorCliente($clienteId, $limit = 10, $offset = 0) {
        $sql = "SELECT sc.*, 
                       p.titulo as titulo_propiedad,
                       p.precio as precio_propiedad,
                       p.moneda as moneda_propiedad,
                       p.ciudad as ciudad_propiedad,
                       p.sector as sector_propiedad,
                       ua.nombre as nombre_agente,
                       ua.apellido as apellido_agente,
                       ua.email as email_agente
                FROM solicitudes_compra sc
                INNER JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios ua ON sc.agente_id = ua.id
                WHERE sc.cliente_id = ?
                ORDER BY sc.fecha_solicitud DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$clienteId, $limit, $offset]);
    }
    
    /**
     * Obtener todas las solicitudes de un agente
     * 
     * @param int $agenteId ID del agente
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de solicitudes
     */
    public function obtenerPorAgente($agenteId, $limit = 10, $offset = 0) {
        $sql = "SELECT sc.*, 
                       p.titulo as titulo_propiedad,
                       p.precio as precio_propiedad,
                       p.moneda as moneda_propiedad,
                       p.ciudad as ciudad_propiedad,
                       p.sector as sector_propiedad,
                       uc.nombre as nombre_cliente,
                       uc.apellido as apellido_cliente,
                       uc.email as email_cliente,
                       uc.telefono as telefono_cliente
                FROM solicitudes_compra sc
                INNER JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios uc ON sc.cliente_id = uc.id
                WHERE sc.agente_id = ?
                ORDER BY sc.fecha_solicitud DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$agenteId, $limit, $offset]);
    }
    
    /**
     * Obtener solicitudes por estado
     * 
     * @param string $estado Estado de las solicitudes
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de solicitudes
     */
    public function obtenerPorEstado($estado, $limit = 10, $offset = 0) {
        $sql = "SELECT sc.*, 
                       p.titulo as titulo_propiedad,
                       p.precio as precio_propiedad,
                       p.moneda as moneda_propiedad,
                       p.ciudad as ciudad_propiedad,
                       p.sector as sector_propiedad,
                       ua.nombre as nombre_agente,
                       ua.apellido as apellido_agente,
                       uc.nombre as nombre_cliente,
                       uc.apellido as apellido_cliente
                FROM solicitudes_compra sc
                INNER JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios ua ON sc.agente_id = ua.id
                INNER JOIN usuarios uc ON sc.cliente_id = uc.id
                WHERE sc.estado = ?
                ORDER BY sc.fecha_solicitud DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$estado, $limit, $offset]);
    }
    
    /**
     * Actualizar el estado de una solicitud
     * 
     * @param int $id ID de la solicitud
     * @param string $estado Nuevo estado
     * @param string $respuesta Respuesta del agente (opcional)
     * @return bool True si se actualizó correctamente
     */
    public function actualizarEstado($id, $estado, $respuesta = null) {
        $sql = "UPDATE solicitudes_compra 
                SET estado = ?, 
                    respuesta_agente = ?, 
                    fecha_respuesta = NOW() 
                WHERE id = ?";
        
        return $this->db->update($sql, [$estado, $respuesta, $id]) !== false;
    }
    
    /**
     * Alias para actualizarEstado - mantener compatibilidad
     * 
     * @param int $id ID de la solicitud
     * @param string $estado Nuevo estado
     * @return bool True si se actualizó correctamente
     */
    public function updateStatus($id, $estado) {
        return $this->actualizarEstado($id, $estado);
    }
    
    /**
     * Verificar si ya existe una solicitud del cliente para la propiedad
     * 
     * @param int $clienteId ID del cliente
     * @param int $propiedadId ID de la propiedad
     * @return bool True si ya existe una solicitud
     */
    public function existeSolicitud($clienteId, $propiedadId) {
        $sql = "SELECT COUNT(*) as total 
                FROM solicitudes_compra 
                WHERE cliente_id = ? AND propiedad_id = ?";
        
        $result = $this->db->selectOne($sql, [$clienteId, $propiedadId]);
        return $result && $result['total'] > 0;
    }
    
    /**
     * Obtener estadísticas de solicitudes para un agente
     * 
     * @param int $agenteId ID del agente
     * @return array Estadísticas
     */
    public function obtenerEstadisticasAgente($agenteId) {
        $sql = "SELECT 
                    COUNT(*) as total_solicitudes,
                    COUNT(CASE WHEN estado = 'nuevo' THEN 1 END) as solicitudes_nuevas,
                    COUNT(CASE WHEN estado = 'en_revision' THEN 1 END) as solicitudes_revision,
                    COUNT(CASE WHEN estado = 'reunion_agendada' THEN 1 END) as solicitudes_reunion,
                    COUNT(CASE WHEN estado = 'cerrado' THEN 1 END) as solicitudes_cerradas
                FROM solicitudes_compra 
                WHERE agente_id = ?";
        
        return $this->db->selectOne($sql, [$agenteId]);
    }
    
    /**
     * Obtener estadísticas de solicitudes para un cliente
     * 
     * @param int $clienteId ID del cliente
     * @return array Estadísticas
     */
    public function obtenerEstadisticasCliente($clienteId) {
        $sql = "SELECT 
                    COUNT(*) as total_solicitudes,
                    COUNT(CASE WHEN estado = 'nuevo' THEN 1 END) as solicitudes_nuevas,
                    COUNT(CASE WHEN estado = 'en_revision' THEN 1 END) as solicitudes_revision,
                    COUNT(CASE WHEN estado = 'reunion_agendada' THEN 1 END) as solicitudes_reunion,
                    COUNT(CASE WHEN estado = 'cerrado' THEN 1 END) as solicitudes_cerradas
                FROM solicitudes_compra 
                WHERE cliente_id = ?";
        
        return $this->db->selectOne($sql, [$clienteId]);
    }
    
    /**
     * Obtener estadísticas generales de solicitudes
     * 
     * @return array Estadísticas generales
     */
    public function obtenerEstadisticasGenerales() {
        $sql = "SELECT 
                    COUNT(*) as total_solicitudes,
                    COUNT(CASE WHEN estado = 'nuevo' THEN 1 END) as solicitudes_nuevas,
                    COUNT(CASE WHEN estado = 'en_revision' THEN 1 END) as solicitudes_revision,
                    COUNT(CASE WHEN estado = 'reunion_agendada' THEN 1 END) as solicitudes_reunion,
                    COUNT(CASE WHEN estado = 'cerrado' THEN 1 END) as solicitudes_cerradas,
                    COUNT(DISTINCT cliente_id) as total_clientes,
                    COUNT(DISTINCT agente_id) as total_agentes
                FROM solicitudes_compra";
        
        return $this->db->selectOne($sql);
    }
    
    /**
     * Eliminar una solicitud
     * 
     * @param int $id ID de la solicitud
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        $sql = "DELETE FROM solicitudes_compra WHERE id = ?";
        return $this->db->delete($sql, [$id]) !== false;
    }
    
    /**
     * Buscar solicitudes con filtros
     * 
     * @param array $filtros Filtros de búsqueda
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de solicitudes filtradas
     */
    public function buscar($filtros = [], $limit = 10, $offset = 0) {
        $sql = "SELECT sc.*, 
                       p.titulo as titulo_propiedad,
                       p.precio as precio_propiedad,
                       p.moneda as moneda_propiedad,
                       p.ciudad as ciudad_propiedad,
                       p.sector as sector_propiedad,
                       ua.nombre as nombre_agente,
                       ua.apellido as apellido_agente,
                       uc.nombre as nombre_cliente,
                       uc.apellido as apellido_cliente
                FROM solicitudes_compra sc
                INNER JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios ua ON sc.agente_id = ua.id
                INNER JOIN usuarios uc ON sc.cliente_id = uc.id";
        
        $where = [];
        $params = [];
        
        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $where[] = "sc.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        if (!empty($filtros['agente_id'])) {
            $where[] = "sc.agente_id = ?";
            $params[] = $filtros['agente_id'];
        }
        
        if (!empty($filtros['cliente_id'])) {
            $where[] = "sc.cliente_id = ?";
            $params[] = $filtros['cliente_id'];
        }
        
        if (!empty($filtros['propiedad_id'])) {
            $where[] = "sc.propiedad_id = ?";
            $params[] = $filtros['propiedad_id'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $where[] = "sc.fecha_solicitud >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "sc.fecha_solicitud <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        // Construir WHERE
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY sc.fecha_solicitud DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Obtener solicitudes por agente y estado
     * 
     * @param int $agenteId ID del agente
     * @param string $estado Estado de las solicitudes
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de solicitudes
     */
    public function getSolicitudesAgente($agenteId, $estado = null, $limit = 10, $offset = 0) {
        $sql = "SELECT sc.*, 
                       p.titulo as titulo_propiedad,
                       p.precio as precio_propiedad,
                       p.moneda as moneda_propiedad,
                       p.ciudad as ciudad_propiedad,
                       p.sector as sector_propiedad,
                       uc.nombre as nombre_cliente,
                       uc.apellido as apellido_cliente,
                       uc.email as email_cliente,
                       uc.telefono as telefono_cliente
                FROM solicitudes_compra sc
                INNER JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios uc ON sc.cliente_id = uc.id
                WHERE sc.agente_id = ?";
        
        $params = [$agenteId];
        
        if ($estado !== null) {
            $sql .= " AND sc.estado = ?";
            $params[] = $estado;
        }
        
        $sql .= " ORDER BY sc.fecha_solicitud DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Obtener solicitudes recientes de un agente para el dashboard
     * 
     * @param int $agenteId ID del agente
     * @param int $limit Límite de resultados
     * @return array Lista de solicitudes recientes
     */
    public function getSolicitudesRecientesPorAgente($agenteId, $limit = 5) {
        $sql = "SELECT 
                    sc.id,
                    sc.estado,
                    sc.fecha_solicitud,
                    sc.nombre_cliente,
                    sc.email_cliente,
                    p.titulo as titulo_propiedad,
                    p.precio
                  FROM solicitudes_compra sc
                  LEFT JOIN propiedades p ON sc.propiedad_id = p.id
                  WHERE sc.agente_id = ?
                  ORDER BY sc.fecha_solicitud DESC
                  LIMIT ?";
        
        return $this->db->select($sql, [$agenteId, $limit]);
    }
} 