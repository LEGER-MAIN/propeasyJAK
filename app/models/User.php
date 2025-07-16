<?php
/**
 * Modelo User - Gestión de Usuarios
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja todas las operaciones relacionadas con usuarios:
 * registro, autenticación, validación, recuperación de contraseñas, etc.
 */

require_once APP_PATH . '/core/Database.php';

class User {
    private $db;
    private $table = 'usuarios';
    
    // Propiedades del usuario
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $rol;
    public $estado;
    public $email_verificado;
    public $token_verificacion;
    public $token_reset_password;
    public $fecha_registro;
    public $ultimo_acceso;
    
    /**
     * Constructor del modelo User
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Registrar un nuevo usuario
     * 
     * @param array $data Datos del usuario
     * @return array Resultado de la operación
     */
    public function register($data) {
        // Validar datos de entrada
        $validation = $this->validateRegistrationData($data);
        if (!$validation['success']) {
            return $validation;
        }
        
        // Verificar si el email ya existe
        if ($this->emailExists($data['email'])) {
            return [
                'success' => false,
                'message' => 'El email ya está registrado en el sistema.'
            ];
        }
        
        // Generar token de verificación
        $verificationToken = $this->generateToken();
        
        // Hash de la contraseña
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => PASSWORD_COST]);
        
        // Preparar datos para inserción
        $userData = [
            'nombre' => sanitizeInput($data['nombre']),
            'apellido' => sanitizeInput($data['apellido']),
            'email' => strtolower(sanitizeInput($data['email'])),
            'password' => $hashedPassword,
            'telefono' => sanitizeInput($data['telefono']),
            'rol' => $data['rol'],
            'estado' => 'activo',
            'email_verificado' => 0,
            'token_verificacion' => $verificationToken,
            'fecha_registro' => date('Y-m-d H:i:s')
        ];
        
        // Insertar usuario en la base de datos
        $query = "INSERT INTO {$this->table} 
                  (nombre, apellido, email, password, telefono, rol, estado, 
                   email_verificado, token_verificacion, fecha_registro) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $userId = $this->db->insert($query, array_values($userData));
        
        if ($userId) {
            // Enviar email de verificación
            $this->sendVerificationEmail($userData['email'], $verificationToken, $userData['nombre']);
            
            return [
                'success' => true,
                'message' => 'Usuario registrado exitosamente. Por favor verifica tu email.',
                'user_id' => $userId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al registrar el usuario. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Autenticar usuario
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @return array Resultado de la autenticación
     */
    public function login($email, $password) {
        // Validar datos de entrada
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Email y contraseña son requeridos.'
            ];
        }
        
        // Buscar usuario por email
        $query = "SELECT * FROM {$this->table} WHERE email = ? AND estado = 'activo'";
        $user = $this->db->selectOne($query, [strtolower(sanitizeInput($email))]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Credenciales inválidas.'
            ];
        }
        
        // Verificar contraseña
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Credenciales inválidas.'
            ];
        }
        
        // Verificar si el email está verificado
        if (!$user['email_verificado']) {
            return [
                'success' => false,
                'message' => 'Por favor verifica tu email antes de iniciar sesión.'
            ];
        }
        
        // Actualizar último acceso
        $this->updateLastAccess($user['id']);
        
        // Crear sesión del usuario
        $this->createUserSession($user);
        
        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
            'user' => [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'apellido' => $user['apellido'],
                'email' => $user['email'],
                'rol' => $user['rol']
            ]
        ];
    }
    
    /**
     * Verificar email del usuario
     * 
     * @param string $token Token de verificación
     * @return array Resultado de la verificación
     */
    public function verifyEmail($token) {
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token de verificación requerido.'
            ];
        }
        
        // Buscar usuario por token
        $query = "SELECT id, email, nombre FROM {$this->table} 
                  WHERE token_verificacion = ? AND email_verificado = 0";
        $user = $this->db->selectOne($query, [sanitizeInput($token)]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Token de verificación inválido o ya verificado.'
            ];
        }
        
        // Actualizar estado de verificación
        $updateQuery = "UPDATE {$this->table} 
                       SET email_verificado = 1, token_verificacion = NULL 
                       WHERE id = ?";
        
        if ($this->db->update($updateQuery, [$user['id']])) {
            return [
                'success' => true,
                'message' => 'Email verificado exitosamente. Ya puedes iniciar sesión.',
                'user' => $user
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al verificar el email. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Solicitar recuperación de contraseña
     * 
     * @param string $email Email del usuario
     * @return array Resultado de la solicitud
     */
    public function requestPasswordReset($email) {
        if (empty($email) || !validateEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email válido requerido.'
            ];
        }
        
        // Buscar usuario por email
        $query = "SELECT id, nombre, email FROM {$this->table} 
                  WHERE email = ? AND estado = 'activo' AND email_verificado = 1";
        $user = $this->db->selectOne($query, [strtolower(sanitizeInput($email))]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'No se encontró una cuenta activa con este email.'
            ];
        }
        
        // Generar token de reset
        $resetToken = $this->generateToken();
        $expiryDate = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        
        // Actualizar token de reset
        $updateQuery = "UPDATE {$this->table} 
                       SET token_reset_password = ?, reset_password_expiry = ? 
                       WHERE id = ?";
        
        if ($this->db->update($updateQuery, [$resetToken, $expiryDate, $user['id']])) {
            // Enviar email de recuperación
            $this->sendPasswordResetEmail($user['email'], $resetToken, $user['nombre']);
            
            return [
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu email.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Resetear contraseña
     * 
     * @param string $token Token de reset
     * @param string $newPassword Nueva contraseña
     * @return array Resultado del reset
     */
    public function resetPassword($token, $newPassword) {
        if (empty($token) || empty($newPassword)) {
            return [
                'success' => false,
                'message' => 'Token y nueva contraseña son requeridos.'
            ];
        }
        
        // Validar nueva contraseña
        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 8 caracteres.'
            ];
        }
        
        // Buscar usuario por token válido
        $query = "SELECT id FROM {$this->table} 
                  WHERE token_reset_password = ? AND reset_password_expiry > NOW()";
        $user = $this->db->selectOne($query, [sanitizeInput($token)]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Token inválido o expirado.'
            ];
        }
        
        // Hash de la nueva contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => PASSWORD_COST]);
        
        // Actualizar contraseña y limpiar token
        $updateQuery = "UPDATE {$this->table} 
                       SET password = ?, token_reset_password = NULL, reset_password_expiry = NULL 
                       WHERE id = ?";
        
        if ($this->db->update($updateQuery, [$hashedPassword, $user['id']])) {
            return [
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar la contraseña. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Obtener usuario por ID
     * 
     * @param int $id ID del usuario
     * @return array|false Datos del usuario o false si no existe
     */
    public function getById($id) {
        $query = "SELECT id, nombre, apellido, email, telefono, rol, estado, 
                         email_verificado, fecha_registro, ultimo_acceso 
                  FROM {$this->table} WHERE id = ?";
        return $this->db->selectOne($query, [$id]);
    }
    
    /**
     * Obtener usuario por email
     * 
     * @param string $email Email del usuario
     * @return array|false Datos del usuario o false si no existe
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ?";
        return $this->db->selectOne($query, [strtolower(sanitizeInput($email))]);
    }
    
    /**
     * Actualizar perfil del usuario
     * 
     * @param int $id ID del usuario
     * @param array $data Datos a actualizar
     * @return array Resultado de la actualización
     */
    public function updateProfile($id, $data) {
        // Validar datos
        if (empty($data['nombre']) || empty($data['apellido']) || empty($data['telefono'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos son requeridos.'
            ];
        }
        if (strlen($data['telefono']) > 20) {
            return [
                'success' => false,
                'message' => 'El teléfono no puede tener más de 20 caracteres.'
            ];
        }
        
        // Preparar datos para actualización
        $updateData = [
            'nombre' => sanitizeInput($data['nombre']),
            'apellido' => sanitizeInput($data['apellido']),
            'telefono' => sanitizeInput($data['telefono'])
        ];
        
        // Si se proporciona nueva contraseña, validarla y hashearla
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                return [
                    'success' => false,
                    'message' => 'La contraseña debe tener al menos 8 caracteres.'
                ];
            }
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => PASSWORD_COST]);
        }
        
        // Construir query dinámicamente
        $fields = [];
        $values = [];
        foreach ($updateData as $field => $value) {
            $fields[] = "{$field} = ?";
            $values[] = $value;
        }
        $values[] = $id;
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        
        if ($this->db->update($query, $values)) {
            return [
                'success' => true,
                'message' => 'Perfil actualizado exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar el perfil. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Obtener todos los usuarios (para administradores)
     * 
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de usuarios
     */
    public function getAll($limit = 50, $offset = 0) {
        $query = "SELECT id, nombre, apellido, email, telefono, rol, estado, 
                         email_verificado, fecha_registro, ultimo_acceso 
                  FROM {$this->table} 
                  ORDER BY fecha_registro DESC 
                  LIMIT ? OFFSET ?";
        return $this->db->select($query, [$limit, $offset]);
    }
    
    /**
     * Cambiar estado del usuario (activar/desactivar)
     * 
     * @param int $id ID del usuario
     * @param string $estado Nuevo estado
     * @return array Resultado de la operación
     */
    public function changeStatus($id, $estado) {
        $allowedStatuses = ['activo', 'inactivo', 'suspendido'];
        
        if (!in_array($estado, $allowedStatuses)) {
            return [
                'success' => false,
                'message' => 'Estado no válido.'
            ];
        }
        
        $query = "UPDATE {$this->table} SET estado = ? WHERE id = ?";
        
        if ($this->db->update($query, [$estado, $id])) {
            return [
                'success' => true,
                'message' => 'Estado del usuario actualizado exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar el estado. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Cerrar sesión del usuario
     */
    public function logout() {
        // Destruir sesión
        session_destroy();
        
        // Limpiar cookies de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        return [
            'success' => true,
            'message' => 'Sesión cerrada exitosamente.'
        ];
    }
    
    /**
     * Validar datos de registro
     * 
     * @param array $data Datos a validar
     * @return array Resultado de la validación
     */
    private function validateRegistrationData($data) {
        $errors = [];
        
        // Validar nombre
        if (empty($data['nombre']) || strlen($data['nombre']) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres.';
        }
        
        // Validar apellido
        if (empty($data['apellido']) || strlen($data['apellido']) < 2) {
            $errors[] = 'El apellido debe tener al menos 2 caracteres.';
        }
        
        // Validar email
        if (empty($data['email']) || !validateEmail($data['email'])) {
            $errors[] = 'Email válido requerido.';
        }
        
        // Validar contraseña
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        
        // Validar teléfono
        if (empty($data['telefono']) || strlen($data['telefono']) < 10) {
            $errors[] = 'Teléfono válido requerido.';
        } else if (strlen($data['telefono']) > 20) {
            $errors[] = 'El teléfono no puede tener más de 20 caracteres.';
        }
        
        // Validar rol
        $allowedRoles = [ROLE_CLIENTE, ROLE_AGENTE, ROLE_ADMIN];
        if (empty($data['rol']) || !in_array($data['rol'], $allowedRoles)) {
            $errors[] = 'Rol válido requerido.';
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode(' ', $errors)
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Verificar si el email ya existe
     * 
     * @param string $email Email a verificar
     * @return bool True si existe, false si no
     */
    private function emailExists($email) {
        $query = "SELECT id FROM {$this->table} WHERE email = ?";
        $result = $this->db->selectOne($query, [strtolower(sanitizeInput($email))]);
        return $result !== false;
    }
    
    /**
     * Generar token aleatorio
     * 
     * @return string Token generado
     */
    private function generateToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Actualizar último acceso del usuario
     * 
     * @param int $userId ID del usuario
     */
    private function updateLastAccess($userId) {
        $query = "UPDATE {$this->table} SET ultimo_acceso = NOW() WHERE id = ?";
        $this->db->update($query, [$userId]);
    }
    
    /**
     * Crear sesión del usuario
     * 
     * @param array $user Datos del usuario
     */
    private function createUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nombre'] = $user['nombre'];
        $_SESSION['user_apellido'] = $user['apellido'];
        $_SESSION['user_telefono'] = $user['telefono'] ?? '';
        $_SESSION['user_ciudad'] = $user['ciudad'] ?? '';
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_logged_in'] = true;
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Enviar email de verificación
     * 
     * @param string $email Email del usuario
     * @param string $token Token de verificación
     * @param string $nombre Nombre del usuario
     */
    private function sendVerificationEmail($email, $token, $nombre) {
        require_once APP_PATH . '/helpers/EmailHelper.php';
        $emailHelper = new EmailHelper();
        return $emailHelper->sendVerificationEmail($email, $token, $nombre);
    }
    
    /**
     * Enviar email de recuperación de contraseña
     * 
     * @param string $email Email del usuario
     * @param string $token Token de reset
     * @param string $nombre Nombre del usuario
     */
    private function sendPasswordResetEmail($email, $token, $nombre) {
        require_once APP_PATH . '/helpers/EmailHelper.php';
        $emailHelper = new EmailHelper();
        return $emailHelper->sendPasswordResetEmail($email, $token, $nombre);
    }
    
    /**
     * Buscar agentes por nombre y ciudad
     * 
     * @param string $nombre Nombre o apellido del agente (opcional)
     * @param string $ciudad Ciudad del agente (opcional)
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de agentes encontrados
     */
    public function buscarAgentes($nombre = '', $ciudad = '', $limit = 20, $offset = 0) {
        $conditions = ["u.rol = 'agente'", "u.estado = 'activo'"];
        $params = [];
        
        if (!empty($nombre)) {
            $conditions[] = "(u.nombre LIKE ? OR u.apellido LIKE ?)";
            $params[] = "%{$nombre}%";
            $params[] = "%{$nombre}%";
        }
        
        if (!empty($ciudad)) {
            $conditions[] = "u.ciudad LIKE ?";
            $params[] = "%{$ciudad}%";
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        $query = "SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.telefono,
                    u.ciudad,
                    u.sector,
                    u.fecha_registro,
                    u.ultimo_acceso,
                    COUNT(DISTINCT p.id) as total_propiedades,
                    COUNT(DISTINCT sc.id) as total_solicitudes
                  FROM {$this->table} u
                  LEFT JOIN propiedades p ON u.id = p.agente_id AND p.estado_publicacion = 'activa'
                  LEFT JOIN solicitudes_compra sc ON u.id = sc.agente_id
                  WHERE {$whereClause}
                  GROUP BY u.id
                  ORDER BY u.nombre, u.apellido
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Buscar clientes por nombre
     * 
     * @param string $nombre Nombre o apellido del cliente
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de clientes encontrados
     */
    public function buscarClientes($nombre = '', $limit = 20, $offset = 0) {
        $conditions = ["u.rol = 'cliente'", "u.estado = 'activo'"];
        $params = [];
        
        if (!empty($nombre)) {
            $conditions[] = "(u.nombre LIKE ? OR u.apellido LIKE ?)";
            $params[] = "%{$nombre}%";
            $params[] = "%{$nombre}%";
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        $query = "SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.telefono,
                    u.ciudad,
                    u.sector,
                    u.fecha_registro,
                    u.ultimo_acceso,
                    COUNT(DISTINCT sc.id) as total_solicitudes
                  FROM {$this->table} u
                  LEFT JOIN solicitudes_compra sc ON u.id = sc.cliente_id
                  WHERE {$whereClause}
                  GROUP BY u.id
                  ORDER BY u.nombre, u.apellido
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obtener estadísticas de búsqueda
     * 
     * @param string $tipo Tipo de búsqueda ('agentes' o 'clientes')
     * @param string $nombre Nombre para filtrar
     * @param string $ciudad Ciudad para filtrar (solo para agentes)
     * @return array Estadísticas
     */
    public function getEstadisticasBusqueda($tipo, $nombre = '', $ciudad = '') {
        $conditions = ["rol = ?", "estado = 'activo'"];
        $params = [$tipo === 'agentes' ? 'agente' : 'cliente'];
        
        if (!empty($nombre)) {
            $conditions[] = "(nombre LIKE ? OR apellido LIKE ?)";
            $params[] = "%{$nombre}%";
            $params[] = "%{$nombre}%";
        }
        
        if ($tipo === 'agentes' && !empty($ciudad)) {
            $conditions[] = "ciudad LIKE ?";
            $params[] = "%{$ciudad}%";
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";
        
        $result = $this->db->selectOne($query, $params);
        return $result ? $result['total'] : 0;
    }

    /**
     * Obtener la última actividad de un usuario
     * 
     * @param int $userId ID del usuario
     * @return string|null Fecha de última actividad o null si no existe
     */
    public function getLastActivity($userId) {
        try {
            $query = "SELECT ultimo_acceso FROM {$this->table} WHERE id = ?";
            $user = $this->db->selectOne($query, [$userId]);
            
            return $user ? $user['ultimo_acceso'] : null;
        } catch (Exception $e) {
            error_log("Error obteniendo última actividad del usuario: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Buscar usuarios para el chat
     * 
     * @param string $query Término de búsqueda
     * @param string|null $role Rol específico a buscar (null para todos)
     * @param int $excludeUserId ID del usuario a excluir de la búsqueda
     * @return array Lista de usuarios encontrados
     */
    public function searchUsers($query, $role = null, $excludeUserId = null) {
        $params = [];
        $conditions = ["estado = 'activo'", "email_verificado = 1"];
        
        // Excluir usuario actual
        if ($excludeUserId) {
            $conditions[] = "id != ?";
            $params[] = $excludeUserId;
        }
        
        // Filtrar por rol si se especifica
        if ($role) {
            $conditions[] = "rol = ?";
            $params[] = $role;
        }
        
        // Agregar término de búsqueda
        $searchConditions = [
            "CONCAT(nombre, ' ', apellido) LIKE ?",
            "email LIKE ?"
        ];
        $searchTerm = '%' . sanitizeInput($query) . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        
        $whereClause = implode(' AND ', $conditions) . ' AND (' . implode(' OR ', $searchConditions) . ')';
        
        $query = "SELECT id, nombre, apellido, email, rol, ultimo_acceso 
                  FROM {$this->table} 
                  WHERE {$whereClause} 
                  ORDER BY nombre, apellido 
                  LIMIT 10";
        
        return $this->db->select($query, $params);
    }

    /**
     * Obtener usuarios disponibles para chat directo
     * 
     * @param int $currentUserId ID del usuario actual
     * @param string $currentUserRole Rol del usuario actual
     * @param string $searchQuery Término de búsqueda (opcional)
     * @return array Lista de usuarios disponibles
     */
    public function getUsersForDirectChat($currentUserId, $currentUserRole, $searchQuery = '') {
        try {
            $params = [$currentUserId];
            $conditions = ["estado = 'activo'", "email_verificado = 1", "id != ?"];
            
            // Si es cliente, mostrar solo agentes
            // Si es agente, mostrar clientes y otros agentes
            if ($currentUserRole === 'cliente') {
                $conditions[] = "rol = 'agente'";
            } else {
                // Para agentes, mostrar clientes y otros agentes
                $conditions[] = "(rol = 'cliente' OR rol = 'agente')";
            }
            
            // Agregar búsqueda si se proporciona
            if (!empty($searchQuery)) {
                $conditions[] = "(nombre LIKE ? OR apellido LIKE ? OR email LIKE ?)";
                $searchTerm = '%' . sanitizeInput($searchQuery) . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $whereClause = implode(' AND ', $conditions);
            
            $query = "SELECT 
                        id,
                        nombre,
                        apellido,
                        email,
                        rol,
                        ultimo_acceso,
                        CASE 
                            WHEN ultimo_acceso > DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN 1 
                            ELSE 0 
                        END as online
                      FROM {$this->table} 
                      WHERE {$whereClause} 
                      ORDER BY online DESC, nombre, apellido
                      LIMIT 20";
            
            return $this->db->select($query, $params);
            
        } catch (Exception $e) {
            error_log("Error obteniendo usuarios para chat directo: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener total de usuarios por rol
     * 
     * @param string $rol Rol de usuario (cliente, agente, admin)
     * @return int Total de usuarios con ese rol
     */
    public function getTotalUsuariosPorRol($rol) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE rol = ? AND estado = 'activo'";
        $resultado = $this->db->selectOne($query, [$rol]);
        return $resultado ? (int)$resultado['total'] : 0;
    }

    /**
     * Obtener perfil público de un agente
     * 
     * @param int $agenteId ID del agente
     * @return array|null Datos del perfil público o null si no existe
     */
    public function getPerfilPublicoAgente($agenteId) {
        try {
            $query = "SELECT 
                        id,
                        nombre,
                        apellido,
                        email,
                        telefono,
                        ciudad,
                        sector,
                        biografia,
                        experiencia_anos,
                        especialidades,
                        foto_perfil,
                        licencia_inmobiliaria,
                        horario_disponibilidad,
                        idiomas,
                        redes_sociales,
                        perfil_publico_activo,
                        fecha_registro,
                        ultimo_acceso
                      FROM {$this->table} 
                      WHERE id = ? AND rol = 'agente' AND estado = 'activo' AND perfil_publico_activo = 1";
            
            $agente = $this->db->selectOne($query, [$agenteId]);
            
            if ($agente) {
                // Decodificar JSON de redes sociales
                if ($agente['redes_sociales']) {
                    $agente['redes_sociales'] = json_decode($agente['redes_sociales'], true);
                }
                
                // Convertir especialidades de string a array
                if ($agente['especialidades']) {
                    $agente['especialidades'] = explode(',', $agente['especialidades']);
                    $agente['especialidades'] = array_map('trim', $agente['especialidades']);
                }
                
                // Convertir idiomas de string a array
                if ($agente['idiomas']) {
                    $agente['idiomas'] = explode(',', $agente['idiomas']);
                    $agente['idiomas'] = array_map('trim', $agente['idiomas']);
                }
            }
            
            return $agente;
            
        } catch (Exception $e) {
            error_log("Error obteniendo perfil público del agente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener estadísticas del agente para el perfil público
     * 
     * @param int $agenteId ID del agente
     * @return array Estadísticas del agente
     */
    public function getEstadisticasAgente($agenteId) {
        try {
            // Obtener estadísticas de propiedades
            $queryPropiedades = "SELECT 
                                  COUNT(*) as total_propiedades,
                                  COUNT(CASE WHEN estado_publicacion = 'activa' THEN 1 END) as propiedades_activas,
                                  COUNT(CASE WHEN estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas,
                                  SUM(CASE WHEN estado_publicacion = 'vendida' THEN precio_venta ELSE 0 END) as total_ventas
                                FROM propiedades 
                                WHERE agente_id = ?";
            
            $statsPropiedades = $this->db->selectOne($queryPropiedades, [$agenteId]);
            
            // Obtener estadísticas de solicitudes
            $querySolicitudes = "SELECT 
                                  COUNT(*) as total_solicitudes,
                                  COUNT(CASE WHEN estado = 'cerrado' THEN 1 END) as solicitudes_cerradas
                                FROM solicitudes_compra 
                                WHERE agente_id = ?";
            
            $statsSolicitudes = $this->db->selectOne($querySolicitudes, [$agenteId]);
            
            // Obtener estadísticas de citas
            $queryCitas = "SELECT 
                            COUNT(*) as total_citas,
                            COUNT(CASE WHEN estado = 'realizada' THEN 1 END) as citas_realizadas
                          FROM citas 
                          WHERE agente_id = ?";
            
            $statsCitas = $this->db->selectOne($queryCitas, [$agenteId]);
            
            // Obtener calificación promedio
            $queryCalificacion = "SELECT 
                                   AVG(calificacion) as calificacion_promedio,
                                   COUNT(*) as total_calificaciones
                                 FROM calificaciones_agentes 
                                 WHERE agente_id = ?";
            
            $statsCalificacion = $this->db->selectOne($queryCalificacion, [$agenteId]);
            
            // Combinar todas las estadísticas
            $estadisticas = array_merge(
                $statsPropiedades ?: [],
                $statsSolicitudes ?: [],
                $statsCitas ?: [],
                $statsCalificacion ?: []
            );
            
            // Asegurar que todos los valores sean numéricos
            $estadisticas = array_map(function($value) {
                return is_numeric($value) ? (float)$value : 0;
            }, $estadisticas);
            
            return $estadisticas;
            
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas del agente: " . $e->getMessage());
            return [
                'total_propiedades' => 0,
                'propiedades_activas' => 0,
                'propiedades_vendidas' => 0,
                'total_ventas' => 0,
                'total_solicitudes' => 0,
                'solicitudes_cerradas' => 0,
                'total_citas' => 0,
                'citas_realizadas' => 0,
                'calificacion_promedio' => 0,
                'total_calificaciones' => 0
            ];
        }
    }

    /**
     * Obtener propiedades recientes del agente
     * 
     * @param int $agenteId ID del agente
     * @param int $limit Límite de propiedades a mostrar
     * @return array Lista de propiedades recientes
     */
    public function getPropiedadesRecientesAgente($agenteId, $limit = 6) {
        try {
            $query = "SELECT 
                        p.id,
                        p.titulo,
                        p.descripcion,
                        p.tipo,
                        p.precio,
                        p.moneda,
                        p.ciudad,
                        p.sector,
                        p.metros_cuadrados,
                        p.habitaciones,
                        p.banos,
                        p.estado_publicacion,
                        p.fecha_creacion,
                        (SELECT ruta FROM imagenes_propiedades 
                         WHERE propiedad_id = p.id AND es_principal = 1 
                         LIMIT 1) as imagen_principal
                      FROM propiedades p
                      WHERE p.agente_id = ? AND p.estado_publicacion = 'activa'
                      ORDER BY p.fecha_creacion DESC
                      LIMIT ?";
            
            return $this->db->select($query, [$agenteId, $limit]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo propiedades recientes del agente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener calificaciones del agente
     * 
     * @param int $agenteId ID del agente
     * @param int $limit Límite de calificaciones a mostrar
     * @return array Lista de calificaciones
     */
    public function getCalificacionesAgente($agenteId, $limit = 5) {
        try {
            $query = "SELECT 
                        ca.id,
                        ca.calificacion,
                        ca.comentario,
                        ca.fecha_calificacion,
                        c.nombre as cliente_nombre,
                        c.apellido as cliente_apellido,
                        p.titulo as propiedad_titulo
                      FROM calificaciones_agentes ca
                      JOIN usuarios c ON ca.cliente_id = c.id
                      JOIN solicitudes_compra sc ON ca.solicitud_id = sc.id
                      JOIN propiedades p ON sc.propiedad_id = p.id
                      WHERE ca.agente_id = ?
                      ORDER BY ca.fecha_calificacion DESC
                      LIMIT ?";
            
            return $this->db->select($query, [$agenteId, $limit]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo calificaciones del agente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualizar perfil público del agente
     * 
     * @param int $agenteId ID del agente
     * @param array $data Datos del perfil a actualizar
     * @return array Resultado de la operación
     */
    public function actualizarPerfilPublico($agenteId, $data) {
        try {
            // Validar que el usuario existe y es agente
            $agente = $this->getById($agenteId);
            if (!$agente || $agente['rol'] !== 'agente') {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado o no es agente.'
                ];
            }
            
            // Preparar datos para actualización
            $updateData = [];
            $params = [];
            
            // Campos permitidos para actualización
            $allowedFields = [
                'biografia', 'experiencia_anos', 'especialidades', 'foto_perfil',
                'licencia_inmobiliaria', 'horario_disponibilidad', 'idiomas',
                'redes_sociales', 'perfil_publico_activo'
            ];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[] = "{$field} = ?";
                    
                    // Procesar campos especiales
                    if ($field === 'especialidades' && is_array($data[$field])) {
                        $params[] = implode(', ', array_map('trim', $data[$field]));
                    } elseif ($field === 'idiomas' && is_array($data[$field])) {
                        $params[] = implode(', ', array_map('trim', $data[$field]));
                    } elseif ($field === 'redes_sociales' && is_array($data[$field])) {
                        $params[] = json_encode($data[$field]);
                    } elseif ($field === 'experiencia_anos') {
                        $params[] = intval($data[$field]);
                    } elseif ($field === 'perfil_publico_activo') {
                        $params[] = $data[$field] ? 1 : 0;
                    } else {
                        $params[] = sanitizeInput($data[$field]);
                    }
                }
            }
            
            if (empty($updateData)) {
                return [
                    'success' => false,
                    'message' => 'No se proporcionaron datos para actualizar.'
                ];
            }
            
            $params[] = $agenteId;
            
            $query = "UPDATE {$this->table} SET " . implode(', ', $updateData) . " WHERE id = ?";
            
            if ($this->db->update($query, $params)) {
                return [
                    'success' => true,
                    'message' => 'Perfil público actualizado exitosamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar el perfil público.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error actualizando perfil público del agente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor.'
            ];
        }
    }

    /**
     * Obtener lista de agentes con perfiles públicos activos
     * 
     * @param string $ciudad Ciudad para filtrar (opcional)
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de agentes
     */
    public function getAgentesConPerfilPublico($ciudad = '', $limit = 12, $offset = 0) {
        try {
            $conditions = [
                "u.rol = 'agente'",
                "u.estado = 'activo'",
                "u.perfil_publico_activo = 1"
            ];
            $params = [];
            
            if (!empty($ciudad)) {
                $conditions[] = "u.ciudad LIKE ?";
                $params[] = "%{$ciudad}%";
            }
            
            $whereClause = implode(' AND ', $conditions);
            
            $query = "SELECT 
                        u.id,
                        u.nombre,
                        u.apellido,
                        u.ciudad,
                        u.sector,
                        u.experiencia_anos,
                        u.especialidades,
                        u.foto_perfil,
                        u.fecha_registro,
                        COUNT(p.id) as total_propiedades,
                        COUNT(CASE WHEN p.estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas
                      FROM {$this->table} u
                      LEFT JOIN propiedades p ON u.id = p.agente_id
                      WHERE {$whereClause}
                      GROUP BY u.id
                      ORDER BY u.nombre, u.apellido
                      LIMIT ? OFFSET ?";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $agentes = $this->db->select($query, $params);
            
            // Procesar especialidades para cada agente
            foreach ($agentes as &$agente) {
                if ($agente['especialidades']) {
                    $agente['especialidades'] = explode(',', $agente['especialidades']);
                    $agente['especialidades'] = array_map('trim', $agente['especialidades']);
                }
            }
            
            return $agentes;
            
        } catch (Exception $e) {
            error_log("Error obteniendo agentes con perfil público: " . $e->getMessage());
            return [];
        }
    }
} 