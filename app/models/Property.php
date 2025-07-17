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
            // Asignar automáticamente el agente con menos propiedades
            $agenteAsignado = $this->getAgenteConMenosPropiedades();
            $data['agente_id'] = $agenteAsignado ? $agenteAsignado['id'] : null;
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
        
        // Solo mostrar propiedades activas para el público
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
                         u.email as agente_email
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
            $isPrincipal = $index === 0; // La primera imagen es la principal
            
            $imageData = [
                'propiedad_id' => $propertyId,
                'nombre_archivo' => $image['name'],
                'ruta' => $image['path'], // Usar la ruta procesada por el controlador
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
            $params[] = floatval($filters['precio_max']);
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
} 