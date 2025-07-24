<?php
/**
 * Modelo ActivityLog - Gestión de Actividades del Sistema
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja el registro y consulta de actividades del sistema
 * para el dashboard administrativo.
 */

require_once APP_PATH . '/core/Database.php';

class ActivityLog {
    
    private $db;
    private $table = 'logs_actividad';
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Registrar una nueva actividad en el sistema
     * 
     * @param int $userId ID del usuario que realizó la acción
     * @param string $action Acción realizada
     * @param string $table Tabla afectada
     * @param int $recordId ID del registro afectado
     * @param array $previousData Datos anteriores (opcional)
     * @param array $newData Datos nuevos (opcional)
     * @return bool True si se registró correctamente
     */
    public function logActivity($userId, $action, $table, $recordId = null, $previousData = null, $newData = null) {
        try {
            $query = "INSERT INTO {$this->table} 
                      (usuario_id, accion, tabla_afectada, registro_id, datos_anteriores, datos_nuevos, ip_address, user_agent) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $userId,
                $action,
                $table,
                $recordId,
                $previousData ? json_encode($previousData) : null,
                $newData ? json_encode($newData) : null,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ];
            
            return $this->db->insert($query, $params);
            
        } catch (Exception $e) {
            error_log("Error registrando actividad: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener actividades recientes del sistema
     * 
     * @param int $limit Límite de actividades a retornar
     * @return array Lista de actividades recientes
     */
    public function getRecentActivities($limit = 20) {
        try {
            $query = "SELECT 
                        la.*,
                        u.nombre,
                        u.apellido,
                        u.rol,
                        u.email
                      FROM {$this->table} la
                      LEFT JOIN usuarios u ON la.usuario_id = u.id
                      ORDER BY la.fecha_actividad DESC
                      LIMIT ?";
            
            $activities = $this->db->select($query, [$limit]);
            
            // Formatear las actividades para el dashboard
            $formattedActivities = [];
            foreach ($activities as $activity) {
                $formattedActivities[] = $this->formatActivityForDashboard($activity);
            }
            
            return $formattedActivities;
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades recientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener actividades por tipo
     * 
     * @param string $action Tipo de acción
     * @param int $limit Límite de resultados
     * @return array Lista de actividades del tipo especificado
     */
    public function getActivitiesByAction($action, $limit = 10) {
        try {
            $query = "SELECT 
                        la.*,
                        u.nombre,
                        u.apellido,
                        u.rol
                      FROM {$this->table} la
                      LEFT JOIN usuarios u ON la.usuario_id = u.id
                      WHERE la.accion = ?
                      ORDER BY la.fecha_actividad DESC
                      LIMIT ?";
            
            return $this->db->select($query, [$action, $limit]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades por acción: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener actividades por usuario
     * 
     * @param int $userId ID del usuario
     * @param int $limit Límite de resultados
     * @return array Lista de actividades del usuario
     */
    public function getActivitiesByUser($userId, $limit = 10) {
        try {
            $query = "SELECT 
                        la.*,
                        u.nombre,
                        u.apellido,
                        u.rol
                      FROM {$this->table} la
                      LEFT JOIN usuarios u ON la.usuario_id = u.id
                      WHERE la.usuario_id = ?
                      ORDER BY la.fecha_actividad DESC
                      LIMIT ?";
            
            return $this->db->select($query, [$userId, $limit]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades por usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de actividades
     * 
     * @return array Estadísticas de actividades
     */
    public function getActivityStats() {
        try {
            $stats = [];
            
            // Total de actividades hoy
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(fecha_actividad) = CURDATE()";
            $result = $this->db->selectOne($query);
            $stats['activities_today'] = $result ? (int)$result['total'] : 0;
            
            // Total de actividades esta semana
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE YEARWEEK(fecha_actividad) = YEARWEEK(NOW())";
            $result = $this->db->selectOne($query);
            $stats['activities_this_week'] = $result ? (int)$result['total'] : 0;
            
            // Actividades por tipo
            $query = "SELECT accion, COUNT(*) as total FROM {$this->table} GROUP BY accion ORDER BY total DESC";
            $stats['activities_by_type'] = $this->db->select($query);
            
            // Usuarios más activos
            $query = "SELECT 
                        u.nombre,
                        u.apellido,
                        u.rol,
                        COUNT(la.id) as total_activities
                      FROM {$this->table} la
                      LEFT JOIN usuarios u ON la.usuario_id = u.id
                      WHERE la.usuario_id IS NOT NULL
                      GROUP BY la.usuario_id, u.nombre, u.apellido, u.rol
                      ORDER BY total_activities DESC
                      LIMIT 10";
            $stats['most_active_users'] = $this->db->select($query);
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas de actividades: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener actividades por período
     * 
     * @param string $period Período (today, week, month, year)
     * @param int $limit Límite de resultados
     * @return array Lista de actividades del período
     */
    public function getActivitiesByPeriod($period = 'week', $limit = 20) {
        try {
            $dateCondition = '';
            switch ($period) {
                case 'today':
                    $dateCondition = "DATE(fecha_actividad) = CURDATE()";
                    break;
                case 'week':
                    $dateCondition = "YEARWEEK(fecha_actividad) = YEARWEEK(NOW())";
                    break;
                case 'month':
                    $dateCondition = "YEAR(fecha_actividad) = YEAR(NOW()) AND MONTH(fecha_actividad) = MONTH(NOW())";
                    break;
                case 'year':
                    $dateCondition = "YEAR(fecha_actividad) = YEAR(NOW())";
                    break;
                default:
                    $dateCondition = "YEARWEEK(fecha_actividad) = YEARWEEK(NOW())";
            }
            
            $query = "SELECT 
                        la.*,
                        u.nombre,
                        u.apellido,
                        u.rol
                      FROM {$this->table} la
                      LEFT JOIN usuarios u ON la.usuario_id = u.id
                      WHERE {$dateCondition}
                      ORDER BY la.fecha_actividad DESC
                      LIMIT ?";
            
            return $this->db->select($query, [$limit]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades por período: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Formatear actividad para el dashboard
     * 
     * @param array $activity Actividad de la base de datos
     * @return array Actividad formateada
     */
    private function formatActivityForDashboard($activity) {
        $action = $activity['accion'];
        $table = $activity['tabla_afectada'];
        $userName = $activity['nombre'] . ' ' . $activity['apellido'];
        $userRole = $activity['rol'];
        
        // Determinar el tipo de actividad y formatear la descripción
        $formattedActivity = [
            'type' => $this->getActivityType($action, $table),
            'action' => $this->getActivityTitle($action, $table),
            'description' => $this->getActivityDescription($action, $table, $userName, $userRole, $activity),
            'time' => $activity['fecha_actividad'],
            'icon' => $this->getActivityIcon($action, $table),
            'color' => $this->getActivityColor($action, $table),
            'user' => [
                'name' => $userName,
                'role' => $userRole,
                'email' => $activity['email']
            ]
        ];
        
        return $formattedActivity;
    }
    
    /**
     * Obtener el tipo de actividad
     * 
     * @param string $action Acción realizada
     * @param string $table Tabla afectada
     * @return string Tipo de actividad
     */
    private function getActivityType($action, $table) {
        if (strpos($action, 'login') !== false) return 'auth';
        if (strpos($action, 'register') !== false) return 'user';
        if ($table === 'propiedades') return 'property';
        if ($table === 'usuarios') return 'user';
        if ($table === 'solicitudes_compra') return 'request';
        if ($table === 'citas') return 'appointment';
        if ($table === 'reportes_irregularidades') return 'report';
        
        return 'system';
    }
    
    /**
     * Obtener el título de la actividad
     * 
     * @param string $action Acción realizada
     * @param string $table Tabla afectada
     * @return string Título de la actividad
     */
    private function getActivityTitle($action, $table) {
        $titles = [
            'login' => 'Inicio de sesión',
            'logout' => 'Cierre de sesión',
            'register' => 'Registro de usuario',
            'create' => 'Creación de registro',
            'create_propiedad' => 'Nueva propiedad',
            'update' => 'Actualización de registro',
            'delete' => 'Eliminación de registro',
            'validate' => 'Validación de registro',
            'reject' => 'Rechazo de registro',
            'approve' => 'Aprobación de registro',
            'confirm' => 'Confirmación',
            'cancel' => 'Cancelación',
            'resolve' => 'Resolución'
        ];
        
        // Mapeo específico por tabla
        $tableSpecificTitles = [
            'propiedades' => [
                'create' => 'Nueva propiedad',
                'create_propiedad' => 'Nueva propiedad',
                'update' => 'Propiedad actualizada',
                'delete' => 'Propiedad eliminada',
                'validate' => 'Propiedad validada',
                'validar_propiedad' => 'Propiedad validada',
                'reject' => 'Propiedad rechazada',
                'rechazar_propiedad' => 'Propiedad rechazada',
                'approve' => 'Propiedad aprobada'
            ],
            'usuarios' => [
                'create' => 'Nuevo usuario',
                'register' => 'Nuevo usuario',
                'update' => 'Usuario actualizado',
                'delete' => 'Usuario eliminado',
                'login' => 'Inicio de sesión',
                'logout' => 'Cierre de sesión'
            ],
            'solicitudes_compra' => [
                'create' => 'Nueva solicitud',
                'update' => 'Solicitud actualizada',
                'delete' => 'Solicitud eliminada',
                'approve' => 'Solicitud aprobada',
                'reject' => 'Solicitud rechazada'
            ],
            'citas' => [
                'create' => 'Nueva cita',
                'update' => 'Cita actualizada',
                'delete' => 'Cita cancelada',
                'confirm' => 'Cita confirmada',
                'cancel' => 'Cita cancelada'
            ],
            'reportes_irregularidades' => [
                'create' => 'Nuevo reporte',
                'update' => 'Reporte actualizado',
                'delete' => 'Reporte eliminado',
                'resolve' => 'Reporte resuelto'
            ]
        ];
        
        // Buscar título específico por tabla
        if (isset($tableSpecificTitles[$table][$action])) {
            return $tableSpecificTitles[$table][$action];
        }
        
        // Buscar título genérico
        if (isset($titles[$action])) {
            return $titles[$action];
        }
        
        // Título genérico profesional
        $actionName = ucfirst(str_replace('_', ' ', $action));
        return $actionName;
    }
    
    /**
     * Obtener la descripción de la actividad
     * 
     * @param string $action Acción realizada
     * @param string $table Tabla afectada
     * @param string $userName Nombre del usuario
     * @param string $userRole Rol del usuario
     * @param array $activity Datos completos de la actividad
     * @return string Descripción de la actividad
     */
    private function getActivityDescription($action, $table, $userName, $userRole, $activity) {
        $descriptions = [
            'usuarios' => [
                'create' => "Nuevo usuario registrado: {$userName} ({$userRole})",
                'update' => "Perfil actualizado: {$userName}",
                'delete' => "Usuario eliminado: {$userName}",
                'register' => "Nuevo usuario registrado: {$userName} ({$userRole})",
                'login' => "Inicio de sesión: {$userName}",
                'logout' => "Cierre de sesión: {$userName}"
            ],
            'propiedades' => [
                'create' => "Nueva propiedad agregada",
                'create_propiedad' => "Nueva propiedad agregada",
                'update' => "Propiedad actualizada",
                'delete' => "Propiedad eliminada",
                'validate' => "Propiedad validada",
                'validar_propiedad' => "Propiedad validada",
                'reject' => "Propiedad rechazada",
                'rechazar_propiedad' => "Propiedad rechazada",
                'approve' => "Propiedad aprobada"
            ],
            'solicitudes_compra' => [
                'create' => "Nueva solicitud de compra",
                'update' => "Solicitud actualizada",
                'delete' => "Solicitud eliminada",
                'approve' => "Solicitud aprobada",
                'reject' => "Solicitud rechazada"
            ],
            'citas' => [
                'create' => "Nueva cita programada",
                'update' => "Cita actualizada",
                'delete' => "Cita cancelada",
                'confirm' => "Cita confirmada",
                'cancel' => "Cita cancelada"
            ],
            'reportes_irregularidades' => [
                'create' => "Nuevo reporte de irregularidad",
                'update' => "Reporte actualizado",
                'delete' => "Reporte eliminado",
                'resolve' => "Reporte resuelto"
            ],
            'favoritos' => [
                'create' => "Propiedad agregada a favoritos",
                'delete' => "Propiedad removida de favoritos"
            ],
            'mensajes_chat' => [
                'create' => "Nuevo mensaje enviado",
                'update' => "Mensaje actualizado",
                'delete' => "Mensaje eliminado"
            ]
        ];
        
        // Buscar descripción específica
        if (isset($descriptions[$table][$action])) {
            return $descriptions[$table][$action];
        }
        
        // Mapeo de acciones genéricas a descripciones profesionales
        $actionDescriptions = [
            'create' => 'Nuevo registro creado',
            'create_propiedad' => 'Nueva propiedad agregada',
            'update' => 'Registro actualizado',
            'delete' => 'Registro eliminado',
            'register' => 'Nuevo usuario registrado',
            'login' => 'Inicio de sesión realizado',
            'logout' => 'Cierre de sesión realizado',
            'validate' => 'Registro validado',
            'validar_propiedad' => 'Propiedad validada',
            'reject' => 'Registro rechazado',
            'rechazar_propiedad' => 'Propiedad rechazada',
            'approve' => 'Registro aprobado',
            'confirm' => 'Registro confirmado',
            'cancel' => 'Registro cancelado',
            'resolve' => 'Registro resuelto'
        ];
        
        // Mapeo de tablas a nombres legibles
        $tableNames = [
            'usuarios' => 'usuario',
            'propiedades' => 'propiedad',
            'solicitudes_compra' => 'solicitud de compra',
            'citas' => 'cita',
            'reportes_irregularidades' => 'reporte',
            'favoritos' => 'favorito',
            'mensajes_chat' => 'mensaje',
            'logs_actividad' => 'actividad del sistema'
        ];
        
        // Si tenemos una descripción para la acción, usarla
        if (isset($actionDescriptions[$action])) {
            $tableName = $tableNames[$table] ?? $table;
            return "{$actionDescriptions[$action]} de {$tableName} por {$userName}";
        }
        
        // Descripción genérica profesional
        $tableName = $tableNames[$table] ?? $table;
        $actionName = ucfirst(str_replace('_', ' ', $action));
        return "{$actionName} de {$tableName} por {$userName}";
    }
    
    /**
     * Obtener el icono de la actividad
     * 
     * @param string $action Acción realizada
     * @param string $table Tabla afectada
     * @return string Clase del icono
     */
    private function getActivityIcon($action, $table) {
        $icons = [
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'register' => 'fas fa-user-plus',
            'usuarios' => 'fas fa-user',
            'propiedades' => 'fas fa-home',
            'solicitudes_compra' => 'fas fa-file-alt',
            'citas' => 'fas fa-calendar-alt',
            'reportes_irregularidades' => 'fas fa-exclamation-triangle',
            'create' => 'fas fa-plus-circle',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
            'validate' => 'fas fa-check-circle',
            'reject' => 'fas fa-times-circle',
            'approve' => 'fas fa-thumbs-up'
        ];
        
        return $icons[$action] ?? $icons[$table] ?? 'fas fa-info-circle';
    }
    
    /**
     * Obtener el color de la actividad
     * 
     * @param string $action Acción realizada
     * @param string $table Tabla afectada
     * @return string Color de la actividad
     */
    private function getActivityColor($action, $table) {
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'register' => 'primary',
            'usuarios' => 'info',
            'propiedades' => 'primary',
            'solicitudes_compra' => 'warning',
            'citas' => 'success',
            'reportes_irregularidades' => 'danger',
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'validate' => 'success',
            'reject' => 'danger',
            'approve' => 'success'
        ];
        
        return $colors[$action] ?? $colors[$table] ?? 'secondary';
    }
    
    /**
     * Limpiar logs antiguos
     * 
     * @param int $days Días a mantener
     * @return bool True si se limpiaron correctamente
     */
    public function cleanOldLogs($days = 90) {
        try {
            $query = "DELETE FROM {$this->table} WHERE fecha_actividad < DATE_SUB(NOW(), INTERVAL ? DAY)";
            return $this->db->delete($query, [$days]);
            
        } catch (Exception $e) {
            error_log("Error limpiando logs antiguos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todas las actividades con paginación
     * 
     * @param int $limit Límite de actividades por página
     * @param int $offset Offset para paginación
     * @return array Lista de actividades paginadas
     */
    public function getAllActivities($limit = 50, $offset = 0) {
        try {
            $query = "SELECT 
                        la.*,
                        u.nombre,
                        u.apellido,
                        u.rol,
                        u.email
                      FROM {$this->table} la
                      LEFT JOIN usuarios u ON la.usuario_id = u.id
                      ORDER BY la.fecha_actividad DESC
                      LIMIT ? OFFSET ?";
            
            $activities = $this->db->select($query, [$limit, $offset]);
            
            // Formatear las actividades
            $formattedActivities = [];
            foreach ($activities as $activity) {
                $formattedActivities[] = $this->formatActivityForDashboard($activity);
            }
            
            return $formattedActivities;
            
        } catch (Exception $e) {
            error_log("Error obteniendo todas las actividades: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener actividades de hoy
     * 
     * @return int Total de actividades de hoy
     */
    public function getActivitiesToday() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(fecha_actividad) = CURDATE()";
            $result = $this->db->selectOne($query);
            return $result ? (int)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades de hoy: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener actividades de esta semana
     * 
     * @return int Total de actividades de esta semana
     */
    public function getActivitiesThisWeek() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE YEARWEEK(fecha_actividad) = YEARWEEK(NOW())";
            $result = $this->db->selectOne($query);
            return $result ? (int)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades de esta semana: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener actividades de este mes
     * 
     * @return int Total de actividades de este mes
     */
    public function getActivitiesThisMonth() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE MONTH(fecha_actividad) = MONTH(NOW()) AND YEAR(fecha_actividad) = YEAR(NOW())";
            $result = $this->db->selectOne($query);
            return $result ? (int)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades de este mes: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener total de actividades
     * 
     * @return int Total de actividades
     */
    public function getTotalCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->db->selectOne($query);
            return $result ? (int)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error obteniendo total de actividades: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Verificar si la tabla existe
     * 
     * @return bool True si la tabla existe
     */
    public function checkTableExists() {
        try {
            $query = "SHOW TABLES LIKE '{$this->table}'";
            $result = $this->db->selectOne($query);
            return $result !== null;
        } catch (Exception $e) {
            error_log("Error verificando tabla: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Método helper para registrar actividades comunes
     * 
     * @param int $userId ID del usuario
     * @param string $action Acción realizada
     * @param string $table Tabla afectada
     * @param int $recordId ID del registro afectado
     * @param array $additionalData Datos adicionales
     * @return bool True si se registró correctamente
     */
    public static function log($userId, $action, $table, $recordId = null, $additionalData = []) {
        try {
            $activityLog = new self();
            return $activityLog->logActivity($userId, $action, $table, $recordId, null, $additionalData);
        } catch (Exception $e) {
            error_log("Error registrando actividad: " . $e->getMessage());
            return false;
        }
    }
} 
