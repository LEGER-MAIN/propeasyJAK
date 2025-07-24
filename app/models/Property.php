<?php
/**
 * Modelo Property - Gestión de Propiedades Inmobiliarias
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja todas las operaciones relacionadas con propiedades:
 * creación, edición, búsqueda, validación, etc.
 */

require_once APP_PATH . '/core/Database.php';

class Property {
    private $db;
    private $table = 'propiedades';
    private $tableImages = 'imagenes_propiedades';
    
    // Propiedades de la clase
    public $id;
    public $titulo;
    public $descripcion;
    public $tipo;
    public $precio;
    public $moneda;
    public $ciudad;
    public $sector;
    public $direccion;
    public $metros_cuadrados;
    public $habitaciones;
    public $banos;
    public $estacionamientos;
    public $estado_propiedad;
    public $estado_publicacion;
    public $agente_id;
    public $cliente_vendedor_id;
    public $token_validacion;
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $fecha_venta;
    public $precio_venta;
    
    /**
     * Constructor del modelo Property
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Crear una nueva propiedad
     * 
     * @param array $data Datos de la propiedad
     * @return array Resultado de la operación
     */
    public function create($data) {
        // Validar datos de entrada
        $validation = $this->validatePropertyData($data);
        if (!$validation['success']) {
            return $validation;
        }
        
        // Generar token de validación si es cliente
        $tokenValidacion = null;
        $agenteAsignado = null;
        if (isset($data['cliente_vendedor_id']) && !empty($data['cliente_vendedor_id'])) {
            $tokenValidacion = $this->generateValidationToken();
            
            // Si se proporcionó un agente específico, usarlo; si no, asignar automáticamente
            if (isset($data['agente_id']) && !empty($data['agente_id'])) {
                // Verificar que el agente existe y está activo
                $agenteAsignado = $this->getAgenteById($data['agente_id']);
                if (!$agenteAsignado || $agenteAsignado['rol'] !== 'agente' || $agenteAsignado['estado'] !== 'activo') {
                    return [
                        'success' => false,
                        'message' => 'El agente seleccionado no está disponible.'
                    ];
                }
            } else {
            // Asignar automáticamente el agente con menos propiedades
            $agenteAsignado = $this->getAgenteConMenosPropiedades();
            $data['agente_id'] = $agenteAsignado ? $agenteAsignado['id'] : null;
            }
        }
        
        // Preparar datos para inserción
        $propertyData = [
            'titulo' => sanitizeInput($data['titulo']),
            'descripcion' => sanitizeInput($data['descripcion']),
            'tipo' => $data['tipo'],
            'precio' => floatval($data['precio']),
            'moneda' => $data['moneda'] ?? 'USD',
            'ciudad' => sanitizeInput($data['ciudad']),
            'sector' => sanitizeInput($data['sector']),
            'direccion' => sanitizeInput($data['direccion']),
            'metros_cuadrados' => floatval($data['metros_cuadrados']),
            'habitaciones' => intval($data['habitaciones']),
            'banos' => intval($data['banos']),
            'estacionamientos' => intval($data['estacionamientos'] ?? 0),
            'estado_propiedad' => $data['estado_propiedad'] ?? 'bueno',
            'estado_publicacion' => isset($data['cliente_vendedor_id']) ? 'en_revision' : 'activa',
            'agente_id' => $data['agente_id'] ?? null,
            'cliente_vendedor_id' => $data['cliente_vendedor_id'] ?? null,
            'token_validacion' => $tokenValidacion,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];
        
        // Insertar propiedad en la base de datos
        $query = "INSERT INTO {$this->table} 
                  (titulo, descripcion, tipo, precio, moneda, ciudad, sector, direccion, 
                   metros_cuadrados, habitaciones, banos, estacionamientos, estado_propiedad, 
                   estado_publicacion, agente_id, cliente_vendedor_id, token_validacion, 
                   fecha_creacion, fecha_actualizacion) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $propertyId = $this->db->insert($query, array_values($propertyData));
        
        if ($propertyId) {
            // Registrar actividad
            require_once APP_PATH . '/models/ActivityLog.php';
            $userId = $data['agente_id'] ?? $data['cliente_vendedor_id'] ?? null;
            if ($userId) {
                ActivityLog::log($userId, 'create', $this->table, $propertyId, [
                    'titulo' => $propertyData['titulo'],
                    'precio' => $propertyData['precio'],
                    'tipo' => $propertyData['tipo']
                ]);
            }
            
            // Procesar imágenes si se proporcionaron
            if (isset($data['imagenes']) && is_array($data['imagenes'])) {
                $this->processImages($propertyId, $data['imagenes']);
            }
            
            return [
                'success' => true,
                'message' => 'Propiedad creada exitosamente.',
                'property_id' => $propertyId,
                'token_validacion' => $tokenValidacion,
                'agente_asignado' => $agenteAsignado
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al crear la propiedad. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Obtener todas las propiedades activas (para API)
     * 
     * @return array Lista de propiedades activas
     */
    public function getAllActive() {
        $query = "SELECT p.*, 
                         u.nombre as agente_nombre, 
                         u.apellido as agente_apellido,
                         (SELECT ruta FROM {$this->tableImages} 
                          WHERE propiedad_id = p.id AND es_principal = 1 
                          LIMIT 1) as imagen_principal
                  FROM {$this->table} p
                  LEFT JOIN usuarios u ON p.agente_id = u.id
                  WHERE p.estado_publicacion = 'activa'
                  ORDER BY p.fecha_creacion DESC";
        
        return $this->db->select($query);
    }
    
    /**
     * Obtener todas las propiedades con filtros
     * 
     * @param array $filters Filtros de búsqueda
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de propiedades
     */
    public function getAll($filters = [], $limit = 12, $offset = 0) {
        $whereConditions = [];
        $params = [];
        
        // Aplicar filtros
        if (!empty($filters['tipo'])) {
            $whereConditions[] = "p.tipo = ?";
            $params[] = $filters['tipo'];
        }
        
        if (!empty($filters['ciudad'])) {
            $whereConditions[] = "p.ciudad LIKE ?";
            $params[] = '%' . $filters['ciudad'] . '%';
        }
        
        if (!empty($filters['sector'])) {
            $whereConditions[] = "p.sector LIKE ?";
            $params[] = '%' . $filters['sector'] . '%';
        }
        
        if (!empty($filters['precio_min'])) {
            $whereConditions[] = "p.precio >= ?";
            $params[] = floatval($filters['precio_min']);
        }
        
        if (!empty($filters['precio_max'])) {
            $whereConditions[] = "p.precio <= ?";
            $params[] = floatval($filters['precio_max']);
        }
        
        if (!empty($filters['habitaciones'])) {
            $whereConditions[] = "p.habitaciones >= ?";
            $params[] = intval($filters['habitaciones']);
        }
        
        if (!empty($filters['banos'])) {
            $whereConditions[] = "p.banos >= ?";
            $params[] = intval($filters['banos']);
        }
        
        // Filtro de búsqueda
        if (!empty($filters['search'])) {
            $whereConditions[] = "(p.titulo LIKE ? OR p.direccion LIKE ? OR p.descripcion LIKE ? OR p.tipo LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Solo mostrar propiedades activas para el público (excepto para admin)
        if (!isset($filters['estado_publicacion'])) {
            $whereConditions[] = "p.estado_publicacion = 'activa'";
        } else {
            $whereConditions[] = "p.estado_publicacion = ?";
            $params[] = $filters['estado_publicacion'];
        }
        
        // Construir consulta
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT p.*, 
                         u.nombre as agente_nombre, 
                         u.apellido as agente_apellido,
                         (SELECT ruta FROM {$this->tableImages} 
                          WHERE propiedad_id = p.id AND es_principal = 1 
                          LIMIT 1) as imagen_principal
                  FROM {$this->table} p
                  LEFT JOIN usuarios u ON p.agente_id = u.id
                  {$whereClause}
                  ORDER BY p.fecha_creacion DESC
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $result = $this->db->select($query, $params);
        
        // Asegurar que siempre retorne un array
        return $result !== false ? $result : [];
    }
    
    /**
     * Obtener una propiedad por ID
     * 
     * @param int $id ID de la propiedad
     * @return array|null Datos de la propiedad
     */
    public function getById($id) {
        $query = "SELECT p.*, 
                         u.nombre as agente_nombre, 
                         u.apellido as agente_apellido,
                         u.telefono as agente_telefono,
                         u.email as agente_email,
                         (SELECT ruta FROM {$this->tableImages} WHERE propiedad_id = p.id AND es_principal = 1 LIMIT 1) as imagen_principal
                  FROM {$this->table} p
                  LEFT JOIN usuarios u ON p.agente_id = u.id
                  WHERE p.id = ?";
        
        $property = $this->db->selectOne($query, [$id]);
        
        if ($property) {
            // Obtener imágenes de la propiedad
            $property['imagenes'] = $this->getImages($id);
        }
        
        return $property;
    }
    
    /**
     * Obtener propiedades por agente
     * 
     * @param int $agenteId ID del agente
     * @param string $estado Estado de la propiedad (opcional)
     * @return array Lista de propiedades
     */
    public function getByAgent($agenteId, $estado = null) {
        $whereConditions = ["p.agente_id = ?"];
        $params = [$agenteId];
        
        if ($estado) {
            $whereConditions[] = "p.estado_publicacion = ?";
            $params[] = $estado;
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $query = "SELECT p.*, 
                         u.nombre as cliente_nombre, 
                         u.apellido as cliente_apellido,
                         u.email as cliente_email,
                         u.telefono as cliente_telefono,
                         (SELECT ruta FROM {$this->tableImages} 
                          WHERE propiedad_id = p.id AND es_principal = 1 
                          LIMIT 1) as imagen_principal
                  FROM {$this->table} p
                  LEFT JOIN usuarios u ON p.cliente_vendedor_id = u.id
                  {$whereClause}
                  ORDER BY p.fecha_creacion DESC";
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener propiedades por agente (alias para compatibilidad)
     * 
     * @param int $agenteId ID del agente
     * @param int $limit Límite de resultados
     * @return array Lista de propiedades
     */
    public function getPropiedadesPorAgente($agenteId, $limit = 5) {
        $query = "SELECT p.*, 
                         (SELECT ruta FROM {$this->tableImages} 
                          WHERE propiedad_id = p.id AND es_principal = 1 
                          LIMIT 1) as imagen_principal
                  FROM {$this->table} p
                  WHERE p.agente_id = ?
                  ORDER BY p.fecha_creacion DESC
                  LIMIT ?";
        
        return $this->db->select($query, [$agenteId, $limit]);
    }
    
    /**
     * Obtener propiedades pendientes de validación
     * 
     * @return array Lista de propiedades en revisión
     */
    public function getPendingValidation() {
        $query = "SELECT p.*, 
                         u.nombre as cliente_nombre, 
                         u.apellido as cliente_apellido,
                         u.telefono as cliente_telefono,
                         u.email as cliente_email,
                         (SELECT ruta FROM {$this->tableImages} 
                          WHERE propiedad_id = p.id AND es_principal = 1 
                          LIMIT 1) as imagen_principal
                  FROM {$this->table} p
                  LEFT JOIN usuarios u ON p.cliente_vendedor_id = u.id
                  WHERE p.estado_publicacion = 'en_revision'
                  ORDER BY p.fecha_creacion ASC";
        
        return $this->db->select($query);
    }
    
    /**
     * Validar propiedad con token
     * 
     * @param int $propertyId ID de la propiedad
     * @param string $token Token de validación
     * @param int $agenteId ID del agente que valida
     * @return array Resultado de la validación
     */
    public function validateProperty($propertyId, $token, $agenteId) {
        // Verificar que la propiedad existe y tiene el token correcto
        $query = "SELECT * FROM {$this->table} 
                  WHERE id = ? AND token_validacion = ? AND estado_publicacion = 'en_revision'";
        
        $property = $this->db->selectOne($query, [$propertyId, $token]);
        
        if (!$property) {
            return [
                'success' => false,
                'message' => 'Propiedad no encontrada o token inválido.'
            ];
        }
        
        // Actualizar propiedad como validada
        $updateQuery = "UPDATE {$this->table} 
                       SET estado_publicacion = 'activa', 
                           agente_id = ?, 
                           token_validacion = NULL,
                           fecha_actualizacion = ?
                       WHERE id = ?";
        
        if ($this->db->update($updateQuery, [$agenteId, date('Y-m-d H:i:s'), $propertyId])) {
            return [
                'success' => true,
                'message' => 'Propiedad validada exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al validar la propiedad.'
            ];
        }
    }
    
    /**
     * Actualizar propiedad
     * 
     * @param int $id ID de la propiedad
     * @param array $data Datos a actualizar
     * @return array Resultado de la actualización
     */
    public function update($id, $data) {
        // Verificar que la propiedad existe
        $existingProperty = $this->getById($id);
        if (!$existingProperty) {
            return [
                'success' => false,
                'message' => 'Propiedad no encontrada.'
            ];
        }
        
        // Validar datos de entrada
        $validation = $this->validatePropertyData($data, true);
        if (!$validation['success']) {
            return $validation;
        }
        
        // Preparar datos para actualización
        $updateData = [
            'titulo' => sanitizeInput($data['titulo']),
            'descripcion' => sanitizeInput($data['descripcion']),
            'tipo' => $data['tipo'],
            'precio' => floatval($data['precio']),
            'moneda' => $data['moneda'] ?? 'USD',
            'ciudad' => sanitizeInput($data['ciudad']),
            'sector' => sanitizeInput($data['sector']),
            'direccion' => sanitizeInput($data['direccion']),
            'metros_cuadrados' => floatval($data['metros_cuadrados']),
            'habitaciones' => intval($data['habitaciones']),
            'banos' => intval($data['banos']),
            'estacionamientos' => intval($data['estacionamientos'] ?? 0),
            'estado_propiedad' => $data['estado_propiedad'] ?? 'bueno',
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];
        
        // Construir consulta de actualización
        $setClause = [];
        $params = [];
        
        foreach ($updateData as $field => $value) {
            $setClause[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE id = ?";
        
        if ($this->db->update($query, $params)) {
            // Procesar nuevas imágenes si se proporcionaron
            if (isset($data['imagenes']) && is_array($data['imagenes'])) {
                // Eliminar imágenes anteriores solo si se suben nuevas
                $this->deleteImages($id);
                $this->processImages($id, $data['imagenes']);
            }
            
            return [
                'success' => true,
                'message' => 'Propiedad actualizada exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar la propiedad.'
            ];
        }
    }
    
    /**
     * Eliminar propiedad
     * 
     * @param int $id ID de la propiedad
     * @return array Resultado de la eliminación
     */
    public function delete($id) {
        // Verificar que la propiedad existe
        $existingProperty = $this->getById($id);
        if (!$existingProperty) {
            return [
                'success' => false,
                'message' => 'Propiedad no encontrada.'
            ];
        }
        
        // Eliminar imágenes asociadas
        $this->deleteImages($id);
        
        // Eliminar propiedad
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        
        if ($this->db->delete($query, [$id])) {
            return [
                'success' => true,
                'message' => 'Propiedad eliminada exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar la propiedad.'
            ];
        }
    }
    
    /**
     * Obtener imágenes de una propiedad
     * 
     * @param int $propertyId ID de la propiedad
     * @return array Lista de imágenes
     */
    public function getImages($propertyId) {
        $query = "SELECT * FROM {$this->tableImages} 
                  WHERE propiedad_id = ? 
                  ORDER BY es_principal DESC, orden ASC";
        
        return $this->db->select($query, [$propertyId]);
    }
    
    /**
     * Procesar y guardar imágenes de una propiedad
     * 
     * @param int $propertyId ID de la propiedad
     * @param array $images Array de imágenes
     * @return bool Resultado de la operación
     */
    private function processImages($propertyId, $images) {
        // Las imágenes ya fueron procesadas por el controlador
        // Aquí solo las guardamos en la base de datos
        
        foreach ($images as $index => $image) {
            $isPrincipal = $index === 0; // Solo la primera es principal
            
            $imageData = [
                'propiedad_id' => $propertyId,
                'nombre_archivo' => $image['name'],
                'ruta' => $image['path'],
                'es_principal' => $isPrincipal ? 1 : 0,
                'orden' => $index + 1,
                'fecha_subida' => date('Y-m-d H:i:s')
            ];
            
            $query = "INSERT INTO {$this->tableImages} 
                      (propiedad_id, nombre_archivo, ruta, es_principal, orden, fecha_subida) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            
            $this->db->insert($query, array_values($imageData));
        }
        
        return true;
    }
    
    /**
     * Eliminar imágenes de una propiedad
     * 
     * @param int $propertyId ID de la propiedad
     * @return bool Resultado de la operación
     */
    private function deleteImages($propertyId) {
        $query = "DELETE FROM {$this->tableImages} WHERE propiedad_id = ?";
        return $this->db->delete($query, [$propertyId]);
    }
    
    /**
     * Generar token de validación
     * 
     * @return string Token único
     */
    private function generateValidationToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Validar datos de la propiedad
     * 
     * @param array $data Datos a validar
     * @param bool $isUpdate Si es una actualización
     * @return array Resultado de la validación
     */
    private function validatePropertyData($data, $isUpdate = false) {
        $errors = [];
        
        // Validar campos obligatorios
        if (empty($data['titulo'])) {
            $errors[] = 'El título es obligatorio.';
        }
        
        if (empty($data['descripcion'])) {
            $errors[] = 'La descripción es obligatoria.';
        }
        
        if (empty($data['tipo'])) {
            $errors[] = 'El tipo de propiedad es obligatorio.';
        } elseif (!in_array($data['tipo'], ['casa', 'apartamento', 'terreno', 'local_comercial', 'oficina'])) {
            $errors[] = 'Tipo de propiedad inválido.';
        }
        
        if (empty($data['precio']) || !is_numeric($data['precio']) || $data['precio'] <= 0) {
            $errors[] = 'El precio es obligatorio y debe ser mayor a 0.';
        }
        
        if (empty($data['ciudad'])) {
            $errors[] = 'La ciudad es obligatoria.';
        }
        
        if (empty($data['sector'])) {
            $errors[] = 'El sector es obligatorio.';
        }
        
        if (empty($data['direccion'])) {
            $errors[] = 'La dirección es obligatoria.';
        }
        
        if (empty($data['metros_cuadrados']) || !is_numeric($data['metros_cuadrados']) || $data['metros_cuadrados'] <= 0) {
            $errors[] = 'Los metros cuadrados son obligatorios y deben ser mayor a 0.';
        }
        
        if (!isset($data['habitaciones']) || !is_numeric($data['habitaciones']) || $data['habitaciones'] < 0) {
            $errors[] = 'El número de habitaciones es obligatorio y debe ser 0 o mayor.';
        }
        
        if (!isset($data['banos']) || !is_numeric($data['banos']) || $data['banos'] < 0) {
            $errors[] = 'El número de baños es obligatorio y debe ser 0 o mayor.';
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Errores de validación:',
                'errors' => $errors
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Obtener estadísticas de propiedades
     * 
     * @return array Estadísticas
     */
    public function getStats() {
        $stats = [];
        
        // Total de propiedades
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->selectOne($query);
        $stats['total'] = $result['total'];
        
        // Propiedades activas
        $query = "SELECT COUNT(*) as activas FROM {$this->table} WHERE estado_publicacion = 'activa'";
        $result = $this->db->selectOne($query);
        $stats['activas'] = $result['activas'];
        
        // Propiedades vendidas
        $query = "SELECT COUNT(*) as vendidas FROM {$this->table} WHERE estado_publicacion = 'vendida'";
        $result = $this->db->selectOne($query);
        $stats['vendidas'] = $result['vendidas'];
        
        // Propiedades en revisión
        $query = "SELECT COUNT(*) as en_revision FROM {$this->table} WHERE estado_publicacion = 'en_revision'";
        $result = $this->db->selectOne($query);
        $stats['en_revision'] = $result['en_revision'];
        
        return $stats;
    }
    
    /**
     * Obtener el agente con menos propiedades activas o en revisión
     * @return array|null Datos del agente
     */
    public function getAgenteConMenosPropiedades() {
        $query = "SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, COUNT(p.id) as total
                  FROM usuarios u
                  LEFT JOIN propiedades p ON u.id = p.agente_id AND p.estado_publicacion IN ('activa', 'en_revision')
                  WHERE u.rol = 'agente' AND u.estado = 'activo'
                  GROUP BY u.id
                  ORDER BY total ASC, u.id ASC
                  LIMIT 1";
        return $this->db->selectOne($query);
    }
    
    /**
     * Validar propiedad por parte del agente
     * 
     * @param int $propertyId ID de la propiedad
     * @param int $agenteId ID del agente
     * @param string $comentario Comentario de validación
     * @return array Resultado de la operación
     */
    public function validarPropiedad($propertyId, $agenteId, $comentario = '') {
        // Verificar que la propiedad existe y está asignada al agente
        $property = $this->getById($propertyId);
        if (!$property) {
            return [
                'success' => false,
                'message' => 'Propiedad no encontrada.'
            ];
        }
        
        if ($property['agente_id'] != $agenteId) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para validar esta propiedad.'
            ];
        }
        
        if ($property['estado_publicacion'] !== 'en_revision' && $property['estado_publicacion'] !== 'pending') {
            return [
                'success' => false,
                'message' => 'Esta propiedad ya no está en revisión.'
            ];
        }
        
        // Actualizar estado de la propiedad
        $query = "UPDATE {$this->table} 
                  SET estado_publicacion = 'activa', 
                      fecha_actualizacion = NOW()
                  WHERE id = ? AND agente_id = ?";
        
        $result = $this->db->update($query, [$propertyId, $agenteId]);
        
        if ($result) {
            // Registrar la actividad
            $this->logActivity($agenteId, 'validar_propiedad', $this->table, $propertyId, [
                'estado_anterior' => $property['estado_publicacion'],
                'estado_nuevo' => 'activa',
                'comentario' => $comentario
            ]);
            
            return [
                'success' => true,
                'message' => 'Propiedad validada exitosamente. Ahora está visible para el público.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al validar la propiedad. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Rechazar propiedad por parte del agente
     * 
     * @param int $propertyId ID de la propiedad
     * @param int $agenteId ID del agente
     * @param string $motivo Motivo del rechazo
     * @return array Resultado de la operación
     */
    public function rechazarPropiedad($propertyId, $agenteId, $motivo = '') {
        // Verificar que la propiedad existe y está asignada al agente
        $property = $this->getById($propertyId);
        if (!$property) {
            return [
                'success' => false,
                'message' => 'Propiedad no encontrada.'
            ];
        }
        
        if ($property['agente_id'] != $agenteId) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para rechazar esta propiedad.'
            ];
        }
        
        if ($property['estado_publicacion'] !== 'en_revision' && $property['estado_publicacion'] !== 'pending') {
            return [
                'success' => false,
                'message' => 'Esta propiedad ya no está en revisión.'
            ];
        }
        
        // Actualizar estado de la propiedad
        $query = "UPDATE {$this->table} 
                  SET estado_publicacion = 'rechazada', 
                      fecha_actualizacion = NOW()
                  WHERE id = ? AND agente_id = ?";
        
        $result = $this->db->update($query, [$propertyId, $agenteId]);
        
        if ($result) {
            // Registrar la actividad
            $this->logActivity($agenteId, 'rechazar_propiedad', $this->table, $propertyId, [
                'estado_anterior' => $property['estado_publicacion'],
                'estado_nuevo' => 'rechazada',
                'motivo' => $motivo
            ]);
            
            return [
                'success' => true,
                'message' => 'Propiedad rechazada. El cliente será notificado.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al rechazar la propiedad. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Obtener propiedades pendientes de validación para un agente
     * 
     * @param int $agenteId ID del agente
     * @param string $search Término de búsqueda (nombre de cliente o token)
     * @return array Lista de propiedades pendientes
     */
    public function getPropiedadesPendientes($agenteId, $search = '') {
        $whereConditions = ["p.agente_id = ?", "(p.estado_publicacion = 'en_revision' OR p.estado_publicacion = 'pending')"];
        $params = [$agenteId];
        
        // Aplicar búsqueda por nombre de cliente o token
        if (!empty($search)) {
            $whereConditions[] = "(u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ? OR p.token_validacion LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $query = "SELECT p.*, 
                         u.nombre as cliente_nombre, 
                         u.apellido as cliente_apellido,
                         u.email as cliente_email,
                         u.telefono as cliente_telefono,
                         (SELECT ruta FROM {$this->tableImages} 
                          WHERE propiedad_id = p.id AND es_principal = 1 
                          LIMIT 1) as imagen_principal
                  FROM {$this->table} p
                  LEFT JOIN usuarios u ON p.cliente_vendedor_id = u.id
                  {$whereClause}
                  ORDER BY p.fecha_creacion ASC";
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener estadísticas de propiedades para un agente específico
     * 
     * @param int $agenteId ID del agente
     * @return array Estadísticas del agente
     */
    public function getStatsByAgent($agenteId) {
        $stats = [];
        
        // Propiedades activas del agente
        $query = "SELECT COUNT(*) as activas FROM {$this->table} WHERE agente_id = ? AND estado_publicacion = 'activa'";
        $result = $this->db->selectOne($query, [$agenteId]);
        $stats['activas'] = $result['activas'];
        
        // Propiedades en revisión del agente
        $query = "SELECT COUNT(*) as en_revision FROM {$this->table} WHERE agente_id = ? AND estado_publicacion = 'en_revision'";
        $result = $this->db->selectOne($query, [$agenteId]);
        $stats['en_revision'] = $result['en_revision'];
        
        // Propiedades rechazadas del agente
        $query = "SELECT COUNT(*) as rechazadas FROM {$this->table} WHERE agente_id = ? AND estado_publicacion = 'rechazada'";
        $result = $this->db->selectOne($query, [$agenteId]);
        $stats['rechazadas'] = $result['rechazadas'];
        
        // Propiedades vendidas del agente
        $query = "SELECT COUNT(*) as vendidas FROM {$this->table} WHERE agente_id = ? AND estado_publicacion = 'vendida'";
        $result = $this->db->selectOne($query, [$agenteId]);
        $stats['vendidas'] = $result['vendidas'];
        
        // Total de propiedades asignadas
        $stats['total'] = $stats['activas'] + $stats['en_revision'] + $stats['rechazadas'] + $stats['vendidas'];
        
        return $stats;
    }
    
    /**
     * Obtener total de propiedades
     * 
     * @return int Total de propiedades
     */
    public function getTotalPropiedades() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $resultado = $this->db->selectOne($query);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener total de propiedades (alias para compatibilidad)
     * 
     * @return int Total de propiedades
     */
    public function getTotalProperties() {
        return $this->getTotalPropiedades();
    }
    
    /**
     * Obtener total de propiedades activas
     * 
     * @return int Total de propiedades activas
     */
    public function getTotalPropiedadesActivas() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado_publicacion = 'activa'";
        $resultado = $this->db->selectOne($query);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener total de propiedades con filtros aplicados
     * 
     * @param array $filters Filtros de búsqueda
     * @return int Total de propiedades que coinciden con los filtros
     */
    public function getTotalPropertiesWithFilters($filters = []) {
        $whereConditions = [];
        $params = [];
        
        // Aplicar filtros
        if (!empty($filters['tipo'])) {
            $whereConditions[] = "tipo = ?";
            $params[] = $filters['tipo'];
        }
        
        if (!empty($filters['ciudad'])) {
            $whereConditions[] = "ciudad LIKE ?";
            $params[] = '%' . $filters['ciudad'] . '%';
        }
        
        if (!empty($filters['sector'])) {
            $whereConditions[] = "sector LIKE ?";
            $params[] = '%' . $filters['sector'] . '%';
        }
        
        if (!empty($filters['precio_min'])) {
            $whereConditions[] = "precio >= ?";
            $params[] = floatval($filters['precio_min']);
        }
        
        if (!empty($filters['precio_max'])) {
            $whereConditions[] = "precio <= ?";
            $params[] = floatval($filters['max_price']);
        }
        
        if (!empty($filters['habitaciones'])) {
            $whereConditions[] = "habitaciones >= ?";
            $params[] = intval($filters['habitaciones']);
        }
        
        if (!empty($filters['banos'])) {
            $whereConditions[] = "banos >= ?";
            $params[] = intval($filters['banos']);
        }
        
        // Solo contar propiedades activas
        $whereConditions[] = "estado_publicacion = 'activa'";
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $resultado = $this->db->selectOne($query, $params);
        
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener las propiedades más recientes
     * 
     * @param int $limit Límite de propiedades a retornar
     * @return array Lista de propiedades más recientes
     */
    public function getPropiedadesRecientes($limit = 6) {
        $query = "SELECT 
                    p.*,
                    u.nombre as agente_nombre,
                    u.apellido as agente_apellido,
                    u.telefono as agente_telefono,
                    0 as total_favoritos,
                    (SELECT ruta FROM imagenes_propiedades 
                     WHERE propiedad_id = p.id AND es_principal = 1 
                     LIMIT 1) as imagen_principal,
                    (SELECT COUNT(*) FROM imagenes_propiedades WHERE propiedad_id = p.id) as total_imagenes
                  FROM {$this->table} p
                  LEFT JOIN usuarios u ON p.agente_id = u.id
                  WHERE p.estado_publicacion = 'activa'
                  ORDER BY p.fecha_creacion DESC
                  LIMIT ?";
        
        return $this->db->select($query, [$limit]);
    }
    
    /**
     * Registrar actividad en el sistema
     * 
     * @param int $userId ID del usuario
     * @param string $accion Acción realizada
     * @param string $tabla Tabla afectada
     * @param int $registroId ID del registro afectado
     * @param array $datos Datos adicionales
     */
    private function logActivity($userId, $accion, $tabla, $registroId, $datos = []) {
        $query = "INSERT INTO logs_actividad 
                  (usuario_id, accion, tabla_afectada, registro_id, datos_nuevos, ip_address, user_agent) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->insert($query, [
            $userId,
            $accion,
            $tabla,
            $registroId,
            json_encode($datos),
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    /**
     * Obtener propiedades por estado
     * 
     * @param string $status Estado de la propiedad
     * @return int Total de propiedades con ese estado
     */
    public function getPropertiesByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado_publicacion = ?";
        $resultado = $this->db->selectOne($query, [$status]);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener total de ventas
     * 
     * @return float Total de ventas
     */
    public function getTotalSales() {
        $query = "SELECT SUM(precio_venta) as total FROM {$this->table} WHERE estado_publicacion = 'vendida' AND precio_venta IS NOT NULL";
        $resultado = $this->db->selectOne($query);
        return $resultado ? (float)$resultado['total'] : 0;
    }
    
    /**
     * Obtener propiedades más vistas
     * 
     * @param int $limit Límite de propiedades
     * @return array Lista de propiedades más vistas
     */
    public function getMostViewedProperties($limit = 5) {
        $query = "SELECT p.*, p.vistas 
                  FROM {$this->table} p 
                  WHERE p.estado_publicacion = 'activa' 
                  ORDER BY p.vistas DESC 
                  LIMIT ?";
        return $this->db->select($query, [$limit]);
    }
    
    /**
     * Obtener propiedades por mes
     * 
     * @param int $months Número de meses
     * @return array Datos de propiedades por mes
     */
    public function getPropertiesByMonth($months = 12) {
        $query = "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') as mes, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                  GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m') 
                  ORDER BY mes";
        return $this->db->select($query, [$months]);
    }
    
    /**
     * Obtener ventas por mes
     * 
     * @param int $months Número de meses
     * @return array Datos de ventas por mes
     */
    public function getSalesByMonth($months = 12) {
        $query = "SELECT DATE_FORMAT(fecha_actualizacion, '%Y-%m') as mes, SUM(precio_venta) as total 
                  FROM {$this->table} 
                  WHERE estado_publicacion = 'vendida' 
                  AND fecha_actualizacion >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                  GROUP BY DATE_FORMAT(fecha_actualizacion, '%Y-%m') 
                  ORDER BY mes";
        return $this->db->select($query, [$months]);
    }
    
    /**
     * Obtener propiedades por tipo
     * 
     * @return array Datos de propiedades por tipo
     */
    public function getPropertiesByType() {
        $query = "SELECT tipo, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE estado_publicacion = 'activa' 
                  GROUP BY tipo";
        return $this->db->select($query);
    }
    
    /**
     * Obtener propiedades por ciudad
     * 
     * @return array Datos de propiedades por ciudad
     */
    public function getPropertiesByCity() {
        $query = "SELECT ciudad, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE estado_publicacion = 'activa' 
                  GROUP BY ciudad 
                  ORDER BY total DESC";
        return $this->db->select($query);
    }
    
    /**
     * Obtener propiedades recientes
     * 
     * @param int $limit Límite de propiedades
     * @return array Lista de propiedades recientes
     */
    public function getRecentProperties($limit = 10) {
        $query = "SELECT p.*, u.nombre as agente_nombre, u.apellido as agente_apellido 
                  FROM {$this->table} p 
                  LEFT JOIN usuarios u ON p.agente_id = u.id 
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT ?";
        return $this->db->select($query, [$limit]);
    }
    
    /**
     * Obtener ventas por agente
     * 
     * @return array Datos de ventas por agente
     */
    public function getSalesByAgent() {
        $query = "SELECT u.nombre as agente_nombre, u.apellido as agente_apellido, 
                         COUNT(*) as propiedades_vendidas, SUM(p.precio_venta) as total_ventas 
                  FROM {$this->table} p 
                  LEFT JOIN usuarios u ON p.agente_id = u.id 
                  WHERE p.estado_publicacion = 'vendida' 
                  GROUP BY p.agente_id, u.nombre, u.apellido 
                  ORDER BY total_ventas DESC";
        return $this->db->select($query);
    }
    
    /**
     * Obtener ventas por tipo de propiedad
     * 
     * @return array Datos de ventas por tipo
     */
    public function getSalesByPropertyType() {
        $query = "SELECT tipo, COUNT(*) as propiedades_vendidas, SUM(precio_venta) as total_ventas 
                  FROM {$this->table} 
                  WHERE estado_publicacion = 'vendida' 
                  GROUP BY tipo 
                  ORDER BY total_ventas DESC";
        return $this->db->select($query);
    }
    
    /**
     * Obtener propiedades para API
     * 
     * @param array $filters Filtros de búsqueda
     * @return array Lista de propiedades
     */
    public function getPropertiesForAPI($filters = []) {
        $whereConditions = ["p.estado_publicacion = 'activa'"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $whereConditions[] = "p.estado_publicacion = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['type'])) {
            $whereConditions[] = "p.tipo = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['city'])) {
            $whereConditions[] = "p.ciudad LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $whereConditions[] = "p.precio >= ?";
            $params[] = floatval($filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $whereConditions[] = "p.precio <= ?";
            $params[] = floatval($filters['max_price']);
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        $limit = $filters['limit'] ?? 10;
        $offset = (($filters['page'] ?? 1) - 1) * $limit;
        
        $query = "SELECT p.*, u.nombre as agente_nombre, u.apellido as agente_apellido 
                  FROM {$this->table} p 
                  LEFT JOIN usuarios u ON p.agente_id = u.id 
                  {$whereClause} 
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener total de propiedades para API
     * 
     * @param array $filters Filtros de búsqueda
     * @return int Total de propiedades
     */
    public function getTotalPropertiesForAPI($filters = []) {
        $whereConditions = ["estado_publicacion = 'activa'"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $whereConditions[] = "estado_publicacion = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['type'])) {
            $whereConditions[] = "tipo = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['city'])) {
            $whereConditions[] = "ciudad LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $whereConditions[] = "precio >= ?";
            $params[] = floatval($filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $whereConditions[] = "precio <= ?";
            $params[] = floatval($filters['max_price']);
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $resultado = $this->db->selectOne($query, $params);
        
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener imágenes de una propiedad
     * 
     * @param int $propertyId ID de la propiedad
     * @return array Lista de imágenes
     */
    public function getPropertyImages($propertyId) {
        $query = "SELECT * FROM imagenes_propiedades WHERE propiedad_id = ? ORDER BY es_principal DESC, id ASC";
        return $this->db->select($query, [$propertyId]);
    }
    
    /**
     * Obtener ciudades disponibles
     * 
     * @return array Lista de ciudades
     */
    public function getAvailableCities() {
        $query = "SELECT DISTINCT ciudad FROM {$this->table} WHERE estado_publicacion = 'activa' AND ciudad IS NOT NULL ORDER BY ciudad";
        $result = $this->db->select($query);
        return array_column($result, 'ciudad');
    }
    
    /**
     * Obtener tipos de propiedades disponibles
     * 
     * @return array Lista de tipos
     */
    public function getAvailablePropertyTypes() {
        $query = "SELECT DISTINCT tipo FROM {$this->table} WHERE estado_publicacion = 'activa' AND tipo IS NOT NULL ORDER BY tipo";
        $result = $this->db->select($query);
        return array_column($result, 'tipo');
    }
    
    /**
     * Buscar propiedades
     * 
     * @param string $query Término de búsqueda
     * @param array $filters Filtros adicionales
     * @return array Lista de propiedades
     */
    public function searchProperties($query, $filters = []) {
        $whereConditions = ["p.estado_publicacion = 'activa'"];
        $params = [];
        
        if (!empty($query)) {
            $whereConditions[] = "(p.titulo LIKE ? OR p.descripcion LIKE ? OR p.ciudad LIKE ? OR p.sector LIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['type'])) {
            $whereConditions[] = "p.tipo = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['city'])) {
            $whereConditions[] = "p.ciudad LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $whereConditions[] = "p.precio >= ?";
            $params[] = floatval($filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $whereConditions[] = "p.precio <= ?";
            $params[] = floatval($filters['max_price']);
        }
        
        if (!empty($filters['bedrooms'])) {
            $whereConditions[] = "p.habitaciones >= ?";
            $params[] = intval($filters['bedrooms']);
        }
        
        if (!empty($filters['bathrooms'])) {
            $whereConditions[] = "p.banos >= ?";
            $params[] = intval($filters['bathrooms']);
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $query = "SELECT p.*, u.nombre as agente_nombre, u.apellido as agente_apellido 
                  FROM {$this->table} p 
                  LEFT JOIN usuarios u ON p.agente_id = u.id 
                  {$whereClause} 
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT 20";
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener propiedades por agente
     * 
     * @param int $agentId ID del agente
     * @param array $statuses Estados de propiedades a incluir
     * @return array Lista de propiedades
     */
    public function getPropertiesByAgent($agentId, $statuses = ['activa']) {
        $placeholders = str_repeat('?,', count($statuses) - 1) . '?';
        $query = "SELECT p.*, u.nombre as agente_nombre, u.apellido as agente_apellido 
                  FROM {$this->table} p 
                  LEFT JOIN usuarios u ON p.agente_id = u.id 
                  WHERE p.agente_id = ? AND p.estado_publicacion IN ({$placeholders}) 
                  ORDER BY p.fecha_creacion DESC";
        
        $params = array_merge([$agentId], $statuses);
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener el total de propiedades en el sistema
     * 
     * @return int Total de propiedades
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->selectOne($query);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener propiedades nuevas hoy
     */
    public function getNewPropertiesToday() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(fecha_creacion) = CURDATE()";
        $result = $this->db->selectOne($query);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener ventas del mes actual
     */
    public function getSalesThisMonth() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado_publicacion = 'vendida' AND MONTH(fecha_venta) = MONTH(NOW()) AND YEAR(fecha_venta) = YEAR(NOW())";
        $result = $this->db->selectOne($query);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener datos de propiedades por período (semana, trimestre, año)
     * @param string $periodType 'week', 'quarter', 'year'
     * @param int $limit Número de períodos a obtener
     * @return array ['labels' => [], 'data' => []]
     */
    public function getPropertiesByPeriod($periodType, $limit) {
        $query = "";
        $labels = [];
        $data = [];
        $dateColumn = 'fecha_creacion'; // For properties, use creation date

        switch ($periodType) {
            case 'week':
                $query = "SELECT
                            YEARWEEK({$dateColumn}, 1) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE {$dateColumn} >= DATE_SUB(CURDATE(), INTERVAL ? WEEK)
                          GROUP BY period_key
                          ORDER BY period_key ASC";
                $results = $this->db->select($query, [$limit]);

                // Generate labels and map data
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $labels[] = 'Sem ' . ($limit - $i);
                    $data[] = 0;
                }

                // Map actual data
                foreach ($results as $row) {
                    $data[] = (int)$row['total'];
                }
                
                while (count($data) < $limit) {
                    $data[] = 0;
                }
                $data = array_slice($data, -$limit);
                
                return ['labels' => $labels, 'data' => $data];

            case 'quarter':
                $query = "SELECT
                            CONCAT(YEAR({$dateColumn}), '-Q', QUARTER({$dateColumn})) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE {$dateColumn} >= DATE_SUB(CURDATE(), INTERVAL ? QUARTER)
                          GROUP BY period_key
                          ORDER BY period_key ASC";
                $results = $this->db->select($query, [$limit]);

                $currentQuarter = (int)ceil(date('n') / 3);
                $currentYear = (int)date('Y');
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $qOffset = $i;
                    $targetQuarter = $currentQuarter - $qOffset;
                    $targetYear = $currentYear;
                    while ($targetQuarter <= 0) {
                        $targetQuarter += 4;
                        $targetYear--;
                    }
                    $labels[] = $targetYear . '-Q' . $targetQuarter;
                    $data[] = 0;
                }

                // Map actual data
                foreach ($results as $row) {
                    $data[] = (int)$row['total'];
                }
                
                while (count($data) < $limit) {
                    $data[] = 0;
                }
                $data = array_slice($data, -$limit);
                
                return ['labels' => $labels, 'data' => $data];

            case 'year':
                $query = "SELECT
                            YEAR({$dateColumn}) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE {$dateColumn} >= DATE_SUB(CURDATE(), INTERVAL ? YEAR)
                          GROUP BY period_key
                          ORDER BY period_key ASC";
                $results = $this->db->select($query, [$limit]);

                $currentYear = (int)date('Y');
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $year = $currentYear - $i;
                    $labels[] = (string)$year;
                    $data[] = 0;
                }

                // Map actual data
                foreach ($results as $row) {
                    $data[] = (int)$row['total'];
                }
                
                while (count($data) < $limit) {
                    $data[] = 0;
                }
                $data = array_slice($data, -$limit);
                
                return ['labels' => $labels, 'data' => $data];

            default:
                return ['labels' => [], 'data' => []];
        }
    }
    
    /**
     * Obtener datos de ventas por período (semana, trimestre, año)
     * @param string $periodType 'week', 'quarter', 'year'
     * @param int $limit Número de períodos a obtener
     * @return array ['labels' => [], 'data' => []]
     */
    public function getSalesByPeriod($periodType, $limit) {
        $query = "";
        $labels = [];
        $data = [];
        $dateColumn = 'fecha_venta'; // For sales, use sale date
        $statusColumn = 'estado_publicacion'; // Assuming 'vendida' status for sales

        switch ($periodType) {
            case 'week':
                $query = "SELECT
                            YEARWEEK({$dateColumn}, 1) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE {$statusColumn} = 'vendida' AND {$dateColumn} >= DATE_SUB(CURDATE(), INTERVAL ? WEEK)
                          GROUP BY period_key
                          ORDER BY period_key ASC";
                $results = $this->db->select($query, [$limit]);

                // Generate labels and map data
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $labels[] = 'Sem ' . ($limit - $i);
                    $data[] = 0;
                }

                // Map actual data
                foreach ($results as $row) {
                    $data[] = (int)$row['total'];
                }
                
                while (count($data) < $limit) {
                    $data[] = 0;
                }
                $data = array_slice($data, -$limit);
                
                return ['labels' => $labels, 'data' => $data];

            case 'quarter':
                $query = "SELECT
                            CONCAT(YEAR({$dateColumn}), '-Q', QUARTER({$dateColumn})) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE {$statusColumn} = 'vendida' AND {$dateColumn} >= DATE_SUB(CURDATE(), INTERVAL ? QUARTER)
                          GROUP BY period_key
                          ORDER BY period_key ASC";
                $results = $this->db->select($query, [$limit]);

                $currentQuarter = (int)ceil(date('n') / 3);
                $currentYear = (int)date('Y');
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $qOffset = $i;
                    $targetQuarter = $currentQuarter - $qOffset;
                    $targetYear = $currentYear;
                    while ($targetQuarter <= 0) {
                        $targetQuarter += 4;
                        $targetYear--;
                    }
                    $labels[] = $targetYear . '-Q' . $targetQuarter;
                    $data[] = 0;
                }

                // Map actual data
                foreach ($results as $row) {
                    $data[] = (int)$row['total'];
                }
                
                while (count($data) < $limit) {
                    $data[] = 0;
                }
                $data = array_slice($data, -$limit);
                
                return ['labels' => $labels, 'data' => $data];

            case 'year':
                $query = "SELECT
                            YEAR({$dateColumn}) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE {$statusColumn} = 'vendida' AND {$dateColumn} >= DATE_SUB(CURDATE(), INTERVAL ? YEAR)
                          GROUP BY period_key
                          ORDER BY period_key ASC";
                $results = $this->db->select($query, [$limit]);

                $currentYear = (int)date('Y');
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $year = $currentYear - $i;
                    $labels[] = (string)$year;
                    $data[] = 0;
                }

                // Map actual data
                foreach ($results as $row) {
                    $data[] = (int)$row['total'];
                }
                
                while (count($data) < $limit) {
                    $data[] = 0;
                }
                $data = array_slice($data, -$limit);
                
                return ['labels' => $labels, 'data' => $data];

            default:
                return ['labels' => [], 'data' => []];
        }
    }
    
    /**
     * Obtener conteo de propiedades de un agente
     * 
     * @param int $agenteId ID del agente
     * @param string|null $estado Estado de la propiedad (opcional)
     * @return int Total de propiedades
     */
    public function getCountByAgent($agenteId, $estado = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE agente_id = ?";
        $params = [$agenteId];
        
        if ($estado !== null) {
            $query .= " AND estado_publicacion = ?";
            $params[] = $estado;
        }
        
        $result = $this->db->selectOne($query, $params);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener total de ventas de un agente
     * 
     * @param int $agenteId ID del agente
     * @return float Total de ventas
     */
    public function getTotalSalesByAgent($agenteId) {
        $query = "SELECT COALESCE(SUM(precio_venta), 0) as total FROM {$this->table} 
                  WHERE agente_id = ? AND estado_publicacion = 'vendida' AND precio_venta IS NOT NULL";
        $result = $this->db->selectOne($query, [$agenteId]);
        return $result ? (float)$result['total'] : 0.0;
    }
    
    /**
     * Obtener agentes disponibles para asignación
     * 
     * @return array Lista de agentes con información básica
     */
    public function getAgentesDisponibles() {
        $query = "SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.telefono,
                    u.ciudad,
                    u.sector,
                    u.foto_perfil,
                    (SELECT COUNT(*) FROM {$this->table} WHERE agente_id = u.id AND estado_publicacion = 'activa') as propiedades_activas,
                    (SELECT COUNT(*) FROM {$this->table} WHERE agente_id = u.id AND estado_publicacion = 'vendida') as propiedades_vendidas
                  FROM usuarios u
                  WHERE u.rol = 'agente' 
                  AND u.estado = 'activo'
                  ORDER BY u.nombre ASC, u.apellido ASC";
        
        return $this->db->select($query);
    }
    
    /**
     * Obtener agentes disponibles con paginación y búsqueda
     * 
     * @param string $search Término de búsqueda
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de agentes con información básica
     */
    public function getAgentesDisponiblesPaginated($search = '', $limit = 20, $offset = 0) {
        $whereConditions = ["u.rol = 'agente'", "u.estado = 'activo'"];
        $params = [];
        
        // Agregar búsqueda por nombre o ciudad
        if (!empty($search)) {
            // Dividir el término de búsqueda en palabras
            $searchWords = array_filter(explode(' ', trim($search)));
            
            if (!empty($searchWords)) {
                $searchConditions = [];
                
                // Para cada palabra, buscar en nombre, apellido, ciudad y sector
                foreach ($searchWords as $word) {
                    $wordTerm = '%' . $word . '%';
                    $searchConditions[] = "(u.nombre LIKE ? OR u.apellido LIKE ? OR u.ciudad LIKE ? OR u.sector LIKE ?)";
                    $params[] = $wordTerm;
                    $params[] = $wordTerm;
                    $params[] = $wordTerm;
                    $params[] = $wordTerm;
                }
                
                // También buscar el término completo para nombres completos
                $fullSearchTerm = '%' . $search . '%';
                $searchConditions[] = "(CONCAT(u.nombre, ' ', u.apellido) LIKE ? OR CONCAT(u.apellido, ' ', u.nombre) LIKE ?)";
                $params[] = $fullSearchTerm;
                $params[] = $fullSearchTerm;
                
                $whereConditions[] = '(' . implode(' OR ', $searchConditions) . ')';
            }
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $query = "SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.telefono,
                    u.ciudad,
                    u.sector,
                    u.foto_perfil,
                    (SELECT COUNT(*) FROM {$this->table} WHERE agente_id = u.id AND estado_publicacion = 'activa') as propiedades_activas,
                    (SELECT COUNT(*) FROM {$this->table} WHERE agente_id = u.id AND estado_publicacion = 'vendida') as propiedades_vendidas
                  FROM usuarios u
                  {$whereClause}
                  ORDER BY u.nombre ASC, u.apellido ASC
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener un agente por ID
     * 
     * @param int $agenteId ID del agente
     * @return array|null Datos del agente
     */
    public function getAgenteById($agenteId) {
        $query = "SELECT id, nombre, apellido, email, telefono, ciudad, sector, rol, estado, foto_perfil
                  FROM usuarios 
                  WHERE id = ? AND rol = 'agente'";
        
        return $this->db->selectOne($query, [$agenteId]);
    }

    /**
     * Obtener propiedades enviadas por un cliente específico
     * 
     * @param int $clienteId ID del cliente
     * @return array Lista de propiedades enviadas
     */
    public function getPropiedadesEnviadasPorCliente($clienteId) {
        $sql = "SELECT 
                    p.*,
                    u.nombre as agente_nombre,
                    u.apellido as agente_apellido,
                    u.foto_perfil as agente_foto,
                    u.telefono as agente_telefono,
                    u.email as agente_email,
                    (SELECT COUNT(*) FROM imagenes_propiedades WHERE propiedad_id = p.id) as total_imagenes,
                    (SELECT ruta FROM imagenes_propiedades WHERE propiedad_id = p.id LIMIT 1) as imagen_principal
                FROM propiedades p
                LEFT JOIN usuarios u ON p.agente_id = u.id
                WHERE p.cliente_vendedor_id = ?
                ORDER BY p.fecha_creacion DESC";
        
        try {
            $propiedades = $this->db->select($sql, [$clienteId]);
            
            // Procesar cada propiedad para agregar información adicional
            foreach ($propiedades as &$propiedad) {
                // Formatear precio
                $propiedad['precio_formateado'] = number_format($propiedad['precio'], 0, ',', '.') . ' ' . $propiedad['moneda'];
                
                // Obtener todas las imágenes
                $propiedad['imagenes'] = $this->getImages($propiedad['id']);
                
                // Estado de la publicación en español
                $estados = [
                    'en_revision' => 'En Revisión',
                    'activa' => 'Activa',
                    'vendida' => 'Vendida',
                    'rechazada' => 'Rechazada',
                    'inactiva' => 'Inactiva'
                ];
                $propiedad['estado_publicacion_texto'] = $estados[$propiedad['estado_publicacion']] ?? 'Desconocido';
                
                // Fecha formateada
                $propiedad['fecha_creacion_formateada'] = date('d/m/Y', strtotime($propiedad['fecha_creacion']));
                
                // Información del agente
                if ($propiedad['agente_nombre']) {
                    $propiedad['agente_nombre_completo'] = $propiedad['agente_nombre'] . ' ' . $propiedad['agente_apellido'];
                } else {
                    $propiedad['agente_nombre_completo'] = 'Sin asignar';
                }
            }
            
            return $propiedades;
        } catch (Exception $e) {
            error_log("Error al obtener propiedades enviadas por cliente: " . $e->getMessage());
            return [];
        }
    }
} 