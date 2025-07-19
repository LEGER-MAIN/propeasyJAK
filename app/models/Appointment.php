<?php
/**
 * Modelo Appointment - Gestión de Citas
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja todas las operaciones relacionadas con las citas
 * entre agentes y clientes para visitas de propiedades.
 */

require_once APP_PATH . '/core/Database.php';

class Appointment {
    private $db;
    private $table = 'citas';
    
    // Estados de citas
    const STATUS_PROPOSED = 'propuesta';
    const STATUS_ACCEPTED = 'aceptada';
    const STATUS_REJECTED = 'rechazada';
    const STATUS_COMPLETED = 'completada';
    const STATUS_CANCELLED = 'cancelada';
    const STATUS_CHANGE_REQUESTED = 'cambio_solicitado';
    
    // Tipos de citas
    const TYPE_PROPERTY_VISIT = 'visita_propiedad';
    const TYPE_OFFICE_MEETING = 'reunion_oficina';
    const TYPE_VIDEO_CALL = 'video_llamada';
    
    /**
     * Constructor del modelo Appointment
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Crear una nueva cita
     * 
     * @param array $data Datos de la cita
     * @return int|false ID de la cita creada o false si falla
     */
    public function create($data) {
        // Validar datos de entrada
        $validation = $this->validateAppointmentData($data);
        
        if (!$validation['success']) {
            return false;
        }
        
        // Verificar disponibilidad de horario
        $isAvailable = $this->isTimeSlotAvailable($data['agente_id'], $data['fecha_cita']);
        
        if (!$isAvailable) {
            return false;
        }
        
        // Preparar datos para inserción
        $appointmentData = [
            'solicitud_id' => (int)$data['solicitud_id'],
            'agente_id' => (int)$data['agente_id'],
            'cliente_id' => (int)$data['cliente_id'],
            'propiedad_id' => (int)$data['propiedad_id'],
            'fecha_cita' => $data['fecha_cita'],
            'lugar' => sanitizeInput($data['lugar']),
            'tipo_cita' => $data['tipo_cita'],
            'estado' => self::STATUS_PROPOSED,
            'observaciones' => sanitizeInput($data['observaciones'] ?? ''),
            'fecha_creacion' => date('Y-m-d H:i:s')
        ];
        
        // Insertar cita en la base de datos
        $query = "INSERT INTO {$this->table} 
                  (solicitud_id, agente_id, cliente_id, propiedad_id, fecha_cita, 
                   lugar, tipo_cita, estado, observaciones, fecha_creacion) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($query, array_values($appointmentData));
    }
    
    /**
     * Obtener una cita por ID
     * 
     * @param int $id ID de la cita
     * @return array|false Datos de la cita o false si no existe
     */
    public function getById($id) {
        $query = "SELECT c.*, 
                         s.mensaje as solicitud_mensaje,
                         p.titulo as propiedad_titulo,
                         p.direccion as propiedad_direccion,
                         p.precio_venta as propiedad_precio,
                         ag.nombre as agente_nombre,
                         ag.apellido as agente_apellido,
                         ag.email as agente_email,
                         ag.telefono as agente_telefono,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido,
                         cl.email as cliente_email,
                         cl.telefono as cliente_telefono
                  FROM {$this->table} c
                  LEFT JOIN solicitudes_compra s ON c.solicitud_id = s.id
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios ag ON c.agente_id = ag.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  WHERE c.id = ?";
        
        return $this->db->selectOne($query, [(int)$id]);
    }
    
    /**
     * Obtener citas por agente
     * 
     * @param int $agenteId ID del agente
     * @param string $estado Estado de las citas (opcional)
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de citas
     */
    public function getByAgent($agenteId, $estado = null, $limit = 20, $offset = 0) {
        $params = [(int)$agenteId];
        $estadoFilter = '';
        
        if ($estado) {
            $estadoFilter = ' AND c.estado = ?';
            $params[] = $estado;
        }
        
        $query = "SELECT c.*, 
                         p.titulo as propiedad_titulo,
                         p.direccion as propiedad_direccion,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido,
                         cl.email as cliente_email
                  FROM {$this->table} c
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  WHERE c.agente_id = ?{$estadoFilter}
                  ORDER BY c.fecha_cita DESC
                  LIMIT ? OFFSET ?";
        
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener citas por cliente
     * 
     * @param int $clienteId ID del cliente
     * @param string $estado Estado de las citas (opcional)
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de citas
     */
    public function getByClient($clienteId, $estado = null, $limit = 20, $offset = 0) {
        $params = [(int)$clienteId];
        $estadoFilter = '';
        
        if ($estado) {
            $estadoFilter = ' AND c.estado = ?';
            $params[] = $estado;
        }
        
        $query = "SELECT c.*, 
                         p.titulo as propiedad_titulo,
                         p.direccion as propiedad_direccion,
                         ag.nombre as agente_nombre,
                         ag.apellido as agente_apellido,
                         ag.email as agente_email,
                         ag.telefono as agente_telefono
                  FROM {$this->table} c
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios ag ON c.agente_id = ag.id
                  WHERE c.cliente_id = ?{$estadoFilter}
                  ORDER BY c.fecha_cita DESC
                  LIMIT ? OFFSET ?";
        
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener citas por propiedad
     * 
     * @param int $propiedadId ID de la propiedad
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de citas
     */
    public function getByProperty($propiedadId, $limit = 20, $offset = 0) {
        $query = "SELECT c.*, 
                         ag.nombre as agente_nombre,
                         ag.apellido as agente_apellido,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido
                  FROM {$this->table} c
                  LEFT JOIN usuarios ag ON c.agente_id = ag.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  WHERE c.propiedad_id = ?
                  ORDER BY c.fecha_cita DESC
                  LIMIT ? OFFSET ?";
        
        return $this->db->select($query, [(int)$propiedadId, (int)$limit, (int)$offset]);
    }
    
    /**
     * Obtener citas por solicitud
     * 
     * @param int $solicitudId ID de la solicitud
     * @return array Lista de citas
     */
    public function getBySolicitud($solicitudId) {
        $query = "SELECT c.*, 
                         p.titulo as propiedad_titulo,
                         ag.nombre as agente_nombre,
                         ag.apellido as agente_apellido,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido
                  FROM {$this->table} c
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios ag ON c.agente_id = ag.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  WHERE c.solicitud_id = ?
                  ORDER BY c.fecha_cita DESC";
        
        return $this->db->select($query, [(int)$solicitudId]);
    }
    
    /**
     * Obtener citas para un rango de fechas
     * 
     * @param int $agenteId ID del agente
     * @param string $fechaInicio Fecha de inicio (Y-m-d)
     * @param string $fechaFin Fecha de fin (Y-m-d)
     * @return array Lista de citas
     */
    public function getByDateRange($agenteId, $fechaInicio, $fechaFin) {
        $query = "SELECT c.*, 
                         p.titulo as propiedad_titulo,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido
                  FROM {$this->table} c
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  WHERE c.agente_id = ? 
                  AND DATE(c.fecha_cita) BETWEEN ? AND ?
                  ORDER BY c.fecha_cita ASC";
        
        return $this->db->select($query, [(int)$agenteId, $fechaInicio, $fechaFin]);
    }
    
    /**
     * Obtener citas pendientes (propuestas y aceptadas)
     * 
     * @param int $agenteId ID del agente
     * @return array Lista de citas pendientes
     */
    public function getPendingAppointments($agenteId) {
        $query = "SELECT c.*, 
                         p.titulo as propiedad_titulo,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido
                  FROM {$this->table} c
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  WHERE c.agente_id = ? 
                  AND c.estado IN (?, ?)
                  AND c.fecha_cita >= NOW()
                  ORDER BY c.fecha_cita ASC";
        
        return $this->db->select($query, [(int)$agenteId, self::STATUS_PROPOSED, self::STATUS_ACCEPTED]);
    }
    
    /**
     * Actualizar estado de una cita
     * 
     * @param int $id ID de la cita
     * @param string $estado Nuevo estado
     * @return bool True si se actualizó correctamente
     */
    public function updateStatus($id, $estado) {
        error_log("updateStatus llamado con id: {$id}, estado: {$estado}");
        
        $estadosValidos = [
            self::STATUS_PROPOSED,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_CHANGE_REQUESTED
        ];
        
        error_log("Estados válidos: " . implode(', ', $estadosValidos));
        
        if (!in_array($estado, $estadosValidos)) {
            error_log("Estado '{$estado}' no es válido");
            return false;
        }
        
        $query = "UPDATE {$this->table} 
                  SET estado = ?, fecha_actualizacion = NOW() 
                  WHERE id = ?";
        
        error_log("Query: {$query}");
        error_log("Parámetros: " . implode(', ', [$estado, (int)$id]));
        
        $result = $this->db->update($query, [$estado, (int)$id]);
        error_log("Resultado de db->update: " . ($result ? 'true' : 'false'));
        
        return $result;
    }
    
    /**
     * Actualizar una cita
     * 
     * @param int $id ID de la cita
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function update($id, $data) {
        // Validar datos de entrada
        $validation = $this->validateUpdateData($data);
        if (!$validation['success']) {
            return false;
        }
        
        // Verificar disponibilidad de horario si se cambia la fecha
        if (isset($data['fecha_cita'])) {
            $cita = $this->getById($id);
            if ($cita && $cita['fecha_cita'] !== $data['fecha_cita']) {
                if (!$this->isTimeSlotAvailable($cita['agente_id'], $data['fecha_cita'], $id)) {
                    return false;
                }
            }
        }
        
        // Preparar datos para actualización
        $updateData = [];
        $params = [];
        
        if (isset($data['fecha_cita'])) {
            $updateData[] = 'fecha_cita = ?';
            $params[] = $data['fecha_cita'];
        }
        
        if (isset($data['lugar'])) {
            $updateData[] = 'lugar = ?';
            $params[] = sanitizeInput($data['lugar']);
        }
        
        if (isset($data['tipo_cita'])) {
            $updateData[] = 'tipo_cita = ?';
            $params[] = $data['tipo_cita'];
        }
        
        if (isset($data['observaciones'])) {
            $updateData[] = 'observaciones = ?';
            $params[] = sanitizeInput($data['observaciones']);
        }
        
        if (empty($updateData)) {
            return false;
        }
        
        $updateData[] = 'fecha_actualizacion = NOW()';
        $params[] = (int)$id;
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $updateData) . " WHERE id = ?";
        
        return $this->db->update($query, $params);
    }
    
    /**
     * Eliminar una cita
     * 
     * @param int $id ID de la cita
     * @return bool True si se eliminó correctamente
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->delete($query, [(int)$id]);
    }
    
    /**
     * Contar citas por agente y estado
     * 
     * @param int $agenteId ID del agente
     * @param string $estado Estado de las citas (opcional)
     * @return int Número de citas
     */
    public function countByAgent($agenteId, $estado = null) {
        $params = [(int)$agenteId];
        $estadoFilter = '';
        
        if ($estado) {
            $estadoFilter = ' AND estado = ?';
            $params[] = $estado;
        }
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE agente_id = ?{$estadoFilter}";
        $result = $this->db->selectOne($query, $params);
        
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Contar citas por cliente y estado
     * 
     * @param int $clienteId ID del cliente
     * @param string $estado Estado de las citas (opcional)
     * @return int Número de citas
     */
    public function countByClient($clienteId, $estado = null) {
        $params = [(int)$clienteId];
        $estadoFilter = '';
        
        if ($estado) {
            $estadoFilter = ' AND estado = ?';
            $params[] = $estado;
        }
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE cliente_id = ?{$estadoFilter}";
        $result = $this->db->selectOne($query, $params);
        
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Verificar si un horario está disponible
     * 
     * @param int $agenteId ID del agente
     * @param string $fechaCita Fecha y hora de la cita
     * @param int $excludeId ID de cita a excluir (para actualizaciones)
     * @return bool True si el horario está disponible
     */
    public function isTimeSlotAvailable($agenteId, $fechaCita, $excludeId = null) {
        // Convertir la fecha de la cita a timestamp
        $fechaCitaTimestamp = strtotime($fechaCita);
        if (!$fechaCitaTimestamp) {
            return false;
        }
        
        // Verificar solo citas en la misma fecha y hora exacta
        $fechaCitaDate = date('Y-m-d H:i:00', $fechaCitaTimestamp);
        
        $params = [(int)$agenteId, $fechaCitaDate];
        $excludeFilter = '';
        
        if ($excludeId) {
            $excludeFilter = ' AND id != ?';
            $params[] = (int)$excludeId;
        }
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE agente_id = ? 
                  AND fecha_cita = ?
                  AND estado IN (?, ?)
                  {$excludeFilter}";
        
        $params[] = self::STATUS_PROPOSED;
        $params[] = self::STATUS_ACCEPTED;
        
        $result = $this->db->selectOne($query, $params);
        $total = $result ? (int)$result['total'] : 0;
        
        return $total === 0;
    }
    
    /**
     * Obtener estadísticas de citas para un agente
     * 
     * @param int $agenteId ID del agente
     * @return array Estadísticas
     */
    public function getAgentStats($agenteId) {
        $query = "SELECT 
                    COUNT(*) as total_citas,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as propuestas,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as aceptadas,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as rechazadas,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as completadas,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as canceladas,
                    COUNT(CASE WHEN fecha_cita >= NOW() AND estado IN (?, ?) THEN 1 END) as proximas
                  FROM {$this->table} 
                  WHERE agente_id = ?";
        
        $params = [
            self::STATUS_PROPOSED,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_PROPOSED,
            self::STATUS_ACCEPTED,
            (int)$agenteId
        ];
        
        return $this->db->selectOne($query, $params);
    }
    
    /**
     * Validar datos de entrada para crear cita
     * 
     * @param array $data Datos a validar
     * @return array Resultado de la validación
     */
    private function validateAppointmentData($data) {
        $errors = [];
        
        // Validar campos requeridos
        if (empty($data['solicitud_id'])) {
            $errors[] = 'ID de solicitud es requerido';
        }
        
        if (empty($data['agente_id'])) {
            $errors[] = 'ID de agente es requerido';
        }
        
        if (empty($data['cliente_id'])) {
            $errors[] = 'ID de cliente es requerido';
        }
        
        if (empty($data['propiedad_id'])) {
            $errors[] = 'ID de propiedad es requerido';
        }
        
        if (empty($data['fecha_cita'])) {
            $errors[] = 'Fecha de cita es requerida';
        } else {
            // Validar formato de fecha
            $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_cita']);
            if (!$fecha || $fecha->format('Y-m-d H:i:s') !== $data['fecha_cita']) {
                $errors[] = 'Formato de fecha inválido';
            } else {
                // Validar que la fecha no sea en el pasado
                if ($fecha < new DateTime()) {
                    $errors[] = 'La fecha de cita no puede ser en el pasado';
                }
            }
        }
        
        if (empty($data['lugar'])) {
            $errors[] = 'Lugar es requerido';
        }
        
        if (empty($data['tipo_cita'])) {
            $errors[] = 'Tipo de cita es requerido';
        } else {
            $tiposValidos = [
                self::TYPE_PROPERTY_VISIT, 
                self::TYPE_OFFICE_MEETING, 
                self::TYPE_VIDEO_CALL,
                'llamada_telefonica',
                'firma_documentos',
                'otro'
            ];
            if (!in_array($data['tipo_cita'], $tiposValidos)) {
                $errors[] = 'Tipo de cita inválido';
            }
        }
        
        return [
            'success' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validar datos de entrada para actualizar cita
     * 
     * @param array $data Datos a validar
     * @return array Resultado de la validación
     */
    private function validateUpdateData($data) {
        $errors = [];
        
        if (isset($data['fecha_cita'])) {
            $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_cita']);
            if (!$fecha || $fecha->format('Y-m-d H:i:s') !== $data['fecha_cita']) {
                $errors[] = 'Formato de fecha inválido';
            } else {
                if ($fecha < new DateTime()) {
                    $errors[] = 'La fecha de cita no puede ser en el pasado';
                }
            }
        }
        
        if (isset($data['tipo_cita'])) {
            $tiposValidos = [
                self::TYPE_PROPERTY_VISIT, 
                self::TYPE_OFFICE_MEETING, 
                self::TYPE_VIDEO_CALL,
                'llamada_telefonica',
                'firma_documentos',
                'otro'
            ];
            if (!in_array($data['tipo_cita'], $tiposValidos)) {
                $errors[] = 'Tipo de cita inválido';
            }
        }
        
        return [
            'success' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Obtener tipos de citas disponibles
     * 
     * @return array Tipos de citas
     */
    public static function getAppointmentTypes() {
        return [
            self::TYPE_PROPERTY_VISIT => 'Visita a la propiedad',
            self::TYPE_OFFICE_MEETING => 'Reunión en oficina',
            self::TYPE_VIDEO_CALL => 'Videollamada',
            'llamada_telefonica' => 'Llamada telefónica',
            'firma_documentos' => 'Firma de documentos',
            'otro' => 'Otro'
        ];
    }
    
    /**
     * Obtener estados de citas disponibles
     * 
     * @return array Estados de citas
     */
    public static function getAppointmentStatuses() {
        return [
            self::STATUS_PROPOSED => 'Propuesta',
            self::STATUS_ACCEPTED => 'Aceptada',
            self::STATUS_REJECTED => 'Rechazada',
            self::STATUS_COMPLETED => 'Completada',
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_CHANGE_REQUESTED => 'Cambio Solicitado'
        ];
    }
    
    /**
     * Obtener citas filtradas para reportes
     * 
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin
     * @param int $agenteId ID del agente (opcional)
     * @param string $estado Estado de la cita (opcional)
     * @param string $tipo Tipo de cita (opcional)
     * @return array Lista de citas filtradas
     */
    public function getFilteredForReport($fechaInicio, $fechaFin, $agenteId = null, $estado = null, $tipo = null) {
        $whereConditions = [];
        $params = [];
        
        // Filtro de fechas
        $whereConditions[] = "DATE(c.fecha_cita) BETWEEN ? AND ?";
        $params[] = $fechaInicio;
        $params[] = $fechaFin;
        
        // Filtro de agente
        if ($agenteId) {
            $whereConditions[] = "c.agente_id = ?";
            $params[] = (int)$agenteId;
        }
        
        // Filtro de estado
        if ($estado) {
            $whereConditions[] = "c.estado = ?";
            $params[] = $estado;
        }
        
        // Filtro de tipo
        if ($tipo) {
            $whereConditions[] = "c.tipo_cita = ?";
            $params[] = $tipo;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $query = "SELECT c.*, 
                         p.titulo as propiedad_titulo,
                         ag.nombre as agente_nombre,
                         ag.apellido as agente_apellido,
                         ag.email as agente_email,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido,
                         cl.email as cliente_email
                  FROM {$this->table} c
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios ag ON c.agente_id = ag.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  WHERE {$whereClause}
                  ORDER BY c.fecha_cita DESC";
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener estadísticas para reportes
     * 
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin
     * @param int $agenteId ID del agente (opcional)
     * @return array Estadísticas
     */
    public function getReportStats($fechaInicio, $fechaFin, $agenteId = null) {
        $whereConditions = [];
        $params = [];
        
        // Filtro de fechas
        $whereConditions[] = "DATE(fecha_cita) BETWEEN ? AND ?";
        $params[] = $fechaInicio;
        $params[] = $fechaFin;
        
        // Filtro de agente
        if ($agenteId) {
            $whereConditions[] = "agente_id = ?";
            $params[] = (int)$agenteId;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'propuesta' THEN 1 ELSE 0 END) as propuestas,
                    SUM(CASE WHEN estado = 'aceptada' THEN 1 ELSE 0 END) as aceptadas,
                    SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'cambio_solicitado' THEN 1 ELSE 0 END) as cambio_solicitado
                  FROM {$this->table}
                  WHERE {$whereClause}";
        
        $result = $this->db->selectOne($query, $params);
        
        return $result ?: [
            'total' => 0,
            'propuestas' => 0,
            'aceptadas' => 0,
            'rechazadas' => 0,
            'canceladas' => 0,
            'completadas' => 0,
            'cambio_solicitado' => 0
        ];
    }
    
    /**
     * Obtener citas recientes para el dashboard
     * 
     * @param int $limit Número de citas a obtener
     * @return array Lista de citas recientes
     */
    public function getRecent($limit = 10) {
        $query = "SELECT c.*, 
                         p.titulo as propiedad_titulo,
                         ag.nombre as agente_nombre,
                         ag.apellido as agente_apellido,
                         ag.email as agente_email,
                         cl.nombre as cliente_nombre,
                         cl.apellido as cliente_apellido,
                         cl.email as cliente_email
                  FROM {$this->table} c
                  LEFT JOIN propiedades p ON c.propiedad_id = p.id
                  LEFT JOIN usuarios ag ON c.agente_id = ag.id
                  LEFT JOIN usuarios cl ON c.cliente_id = cl.id
                  ORDER BY c.fecha_cita DESC
                  LIMIT ?";
        
        return $this->db->select($query, [(int)$limit]);
    }
    
    /**
     * Obtener total de citas en el sistema
     * 
     * @return int Total de citas
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->selectOne($query);
        
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener total de citas (alias para compatibilidad)
     * 
     * @return int Total de citas
     */
    public function getTotalAppointments() {
        return $this->getTotalCount();
    }
    
    /**
     * Obtener citas por estado
     * 
     * @param string $status Estado de las citas
     * @return int Total de citas con ese estado
     */
    public function getAppointmentsByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado = ?";
        $resultado = $this->db->selectOne($query, [$status]);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener citas recientes
     * 
     * @param int $limit Límite de citas
     * @return array Lista de citas recientes
     */
    public function getRecentAppointments($limit = 10) {
        return $this->getRecent($limit);
    }
    
    /**
     * Obtener citas por mes
     * 
     * @param int $months Número de meses
     * @return array Datos de citas por mes
     */
    public function getAppointmentsByMonth($months = 12) {
        $query = "SELECT DATE_FORMAT(fecha_cita, '%Y-%m') as mes, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE fecha_cita >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                  GROUP BY DATE_FORMAT(fecha_cita, '%Y-%m') 
                  ORDER BY mes";
        return $this->db->select($query, [$months]);
    }
    
    /**
     * Obtener citas por agente
     * 
     * @return array Datos de citas por agente
     */
    public function getAppointmentsByAgent() {
        $query = "SELECT u.nombre as agente_nombre, u.apellido as agente_apellido, 
                         COUNT(*) as total_citas,
                         COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) as citas_completadas
                  FROM {$this->table} c
                  LEFT JOIN usuarios u ON c.agente_id = u.id
                  GROUP BY c.agente_id, u.nombre, u.apellido
                  ORDER BY total_citas DESC";
        return $this->db->select($query);
    }
    
    /**
     * Obtener citas por tipo
     * 
     * @return array Datos de citas por tipo
     */
    public function getAppointmentsByType() {
        $query = "SELECT tipo_cita, COUNT(*) as total FROM {$this->table} GROUP BY tipo_cita ORDER BY total DESC";
        return $this->db->select($query);
    }
} 