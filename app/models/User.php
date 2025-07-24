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
            // Registrar actividad
            require_once APP_PATH . '/models/ActivityLog.php';
            ActivityLog::log($userId, 'register', $this->table, $userId, [
                'nombre' => $userData['nombre'],
                'apellido' => $userData['apellido'],
                'rol' => $userData['rol']
            ]);
            
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
        
        // Registrar actividad de login
        require_once APP_PATH . '/models/ActivityLog.php';
        ActivityLog::log($user['id'], 'login', $this->table, $user['id'], [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
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
        $query = "SELECT id, nombre, apellido, email, telefono, ciudad, sector, rol, estado, 
                         email_verificado, token_verificacion, fecha_registro, ultimo_acceso 
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
        // Validar datos requeridos
        if (empty($data['nombre']) || empty($data['apellido'])) {
            return [
                'success' => false,
                'message' => 'El nombre y apellido son obligatorios.'
            ];
        }
        
        // Validar teléfono si se proporciona
        if (isset($data['telefono']) && strlen($data['telefono']) > 20) {
            return [
                'success' => false,
                'message' => 'El teléfono no puede tener más de 20 caracteres.'
            ];
        }
        
        // Preparar datos para actualización
        $updateData = [
            'nombre' => sanitizeInput($data['nombre']),
            'apellido' => sanitizeInput($data['apellido'])
        ];
        
        // Agregar campos opcionales si están presentes
        if (isset($data['telefono'])) {
            $updateData['telefono'] = sanitizeInput($data['telefono']);
        }
        if (isset($data['ciudad'])) {
            $updateData['ciudad'] = sanitizeInput($data['ciudad']);
        }
        if (isset($data['sector'])) {
            $updateData['sector'] = sanitizeInput($data['sector']);
        }
        if (isset($data['foto_perfil'])) {
            $updateData['foto_perfil'] = $data['foto_perfil'];
        }
        
        // Campos específicos para agentes
        if (isset($data['experiencia_anos'])) {
            $updateData['experiencia_anos'] = (int)$data['experiencia_anos'];
        }
        if (isset($data['especialidades'])) {
            $updateData['especialidades'] = sanitizeInput($data['especialidades']);
        }
        if (isset($data['idiomas'])) {
            $updateData['idiomas'] = sanitizeInput($data['idiomas']);
        }
        if (isset($data['biografia'])) {
            $updateData['biografia'] = sanitizeInput($data['biografia']);
        }
        if (isset($data['licencia_inmobiliaria'])) {
            $updateData['licencia_inmobiliaria'] = sanitizeInput($data['licencia_inmobiliaria']);
        }
        if (isset($data['horario_disponibilidad'])) {
            $updateData['horario_disponibilidad'] = sanitizeInput($data['horario_disponibilidad']);
        }
        
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
        
        $result = $this->db->update($query, $values);
        
        if ($result !== false) {
            // Actualizar datos de sesión si están disponibles
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
                // Actualizar datos individuales en la sesión
                if (isset($updateData['nombre'])) {
                    $_SESSION['user_nombre'] = $updateData['nombre'];
                }
                if (isset($updateData['apellido'])) {
                    $_SESSION['user_apellido'] = $updateData['apellido'];
                }
                if (isset($updateData['telefono'])) {
                    $_SESSION['user_telefono'] = $updateData['telefono'];
                }
                if (isset($updateData['ciudad'])) {
                    $_SESSION['user_ciudad'] = $updateData['ciudad'];
                }
                if (isset($updateData['sector'])) {
                    $_SESSION['user_sector'] = $updateData['sector'];
                }
                
                // Actualizar el array completo de usuario en la sesión
                if (isset($_SESSION['user'])) {
                    $_SESSION['user'] = array_merge($_SESSION['user'], $updateData);
                }
            }
            
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
        // Registrar actividad de logout si hay usuario en sesión
        if (isset($_SESSION['user_id'])) {
            require_once APP_PATH . '/models/ActivityLog.php';
            ActivityLog::log($_SESSION['user_id'], 'logout', $this->table, $_SESSION['user_id'], [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        }
        
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
     * Actualizar última actividad del usuario (método público)
     * 
     * @param int $userId ID del usuario
     * @return bool Resultado de la actualización
     */
    public function updateLastActivity($userId) {
        $query = "UPDATE {$this->table} SET ultimo_acceso = NOW() WHERE id = ?";
        return $this->db->update($query, [$userId]);
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
        $_SESSION['user_sector'] = $user['sector'] ?? '';
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_estado'] = $user['estado'] ?? 'activo';
        $_SESSION['user_email_verificado'] = $user['email_verificado'] ?? 0;
        $_SESSION['user_fecha_registro'] = $user['fecha_registro'] ?? '';
        $_SESSION['user_ultimo_acceso'] = $user['ultimo_acceso'] ?? '';
        $_SESSION['user_foto_perfil'] = $user['foto_perfil'] ?? '';
        $_SESSION['user_logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Guardar todos los datos del usuario en un array para fácil acceso
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nombre' => $user['nombre'],
            'apellido' => $user['apellido'],
            'email' => $user['email'],
            'telefono' => $user['telefono'] ?? '',
            'ciudad' => $user['ciudad'] ?? '',
            'sector' => $user['sector'] ?? '',
            'rol' => $user['rol'],
            'estado' => $user['estado'] ?? 'activo',
            'email_verificado' => $user['email_verificado'] ?? 0,
            'fecha_registro' => $user['fecha_registro'] ?? '',
            'ultimo_acceso' => $user['ultimo_acceso'] ?? '',
            'foto_perfil' => $user['foto_perfil'] ?? ''
        ];
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
    public function buscarAgentes($nombre = '', $ciudad = '', $experiencia = '', $idioma = '', $ordenar = 'nombre', $limit = 20, $offset = 0) {
        $conditions = ["u.rol = 'agente'", "u.estado = 'activo'", "u.perfil_publico_activo = 1"];
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
        
        if (!empty($experiencia)) {
            $conditions[] = "u.experiencia >= ?";
            $params[] = (int)$experiencia;
        }
        
        if (!empty($idioma)) {
            $conditions[] = "u.idiomas LIKE ?";
            $params[] = "%{$idioma}%";
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Determinar el ordenamiento
        $orderBy = match($ordenar) {
            'nombre' => 'u.nombre, u.apellido',
            'experiencia' => 'u.experiencia DESC',
            'propiedades' => 'total_propiedades DESC',
            'vendidas' => 'total_vendidas DESC',
            'fecha' => 'u.fecha_registro DESC',
            default => 'u.nombre, u.apellido'
        };
        
        // Consulta base para obtener agentes
        $query = "SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.telefono,
                    u.ciudad,
                    u.sector,
                    u.fecha_registro,
                    u.ultimo_acceso
                  FROM {$this->table} u
                  WHERE {$whereClause}
                  ORDER BY {$orderBy}
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $agentes = $this->db->select($query, $params);
        
        // Para cada agente, obtener sus estadísticas
        foreach ($agentes as &$agente) {
            // Obtener estadísticas de propiedades
            $queryStats = "SELECT 
                            COUNT(CASE WHEN estado_publicacion = 'activa' THEN 1 END) as total_propiedades,
                            COUNT(CASE WHEN estado_publicacion = 'vendida' THEN 1 END) as total_vendidas
                          FROM propiedades 
                          WHERE agente_id = ?";
            
            $stats = $this->db->selectOne($queryStats, [$agente['id']]);
            
            $agente['total_propiedades'] = $stats['total_propiedades'] ?? 0;
            $agente['total_vendidas'] = $stats['total_vendidas'] ?? 0;
            

        }
        
        return $agentes;
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
        
        // Para agentes, solo contar los que tienen perfil público activo
        if ($tipo === 'agentes') {
            $conditions[] = "perfil_publico_activo = 1";
        }
        
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
        $conditions = ["estado = 'activo'"];
        
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
        
        // Agregar término de búsqueda - manejar nombres nulos
        $searchConditions = [
            "COALESCE(nombre, '') LIKE ?",
            "COALESCE(apellido, '') LIKE ?",
            "CONCAT(COALESCE(nombre, ''), ' ', COALESCE(apellido, '')) LIKE ?",
            "email LIKE ?"
        ];
        $searchTerm = '%' . sanitizeInput($query) . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        
        $whereClause = implode(' AND ', $conditions) . ' AND (' . implode(' OR ', $searchConditions) . ')';
        
        $query = "SELECT id, nombre, apellido, email, rol, ultimo_acceso, foto_perfil 
                  FROM {$this->table} 
                  WHERE {$whereClause} 
                  ORDER BY COALESCE(nombre, ''), COALESCE(apellido, '') 
                  LIMIT 10";
        
        return $this->db->select($query, $params);
    }

    /**
     * Buscar usuario por ID específico
     * 
     * @param int $userId ID del usuario a buscar
     * @param string $role Rol específico a filtrar (opcional)
     * @param int $excludeUserId ID del usuario a excluir (opcional)
     * @return array Usuario encontrado o array vacío
     */
    public function searchUsersById($userId, $role = null, $excludeUserId = null) {
        $params = [$userId];
        $conditions = ["estado = 'activo'", "id = ?"];
        
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
        
        $whereClause = implode(' AND ', $conditions);
        
        $query = "SELECT id, nombre, apellido, email, rol, ultimo_acceso, foto_perfil 
                  FROM {$this->table} 
                  WHERE {$whereClause} 
                  LIMIT 1";
        
        $result = $this->db->select($query, $params);
        return $result ?: [];
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
                        foto_perfil,
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
                        u.email,
                        u.telefono,
                        u.ciudad,
                        u.sector,
                        u.experiencia_anos,
                        u.especialidades,
                        u.idiomas,
                        u.biografia,
                        u.descripcion_corta,
                        u.licencia_inmobiliaria,
                        u.horario_atencion,
                        u.foto_perfil,
                        u.fecha_registro,
                        u.ultimo_acceso,
                        COUNT(p.id) as total_propiedades,
                        COUNT(CASE WHEN p.estado_publicacion = 'activa' THEN 1 END) as propiedades_activas,
                        COUNT(CASE WHEN p.estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas,
                        COALESCE(AVG(ca.calificacion), 0) as calificacion_promedio,
                        COUNT(ca.id) as total_calificaciones
                      FROM {$this->table} u
                      LEFT JOIN propiedades p ON u.id = p.agente_id
                      LEFT JOIN calificaciones_agentes ca ON u.id = ca.agente_id
                      WHERE {$whereClause}
                      GROUP BY u.id
                      ORDER BY u.nombre, u.apellido
                      LIMIT ? OFFSET ?";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $agentes = $this->db->select($query, $params);
            
            // Verificar si la consulta fue exitosa
            if ($agentes === false) {
                error_log("Error en consulta SQL para agentes: " . $query);
                return [];
            }
            
            // Procesar datos para cada agente
            foreach ($agentes as &$agente) {
                // Procesar especialidades
                if ($agente['especialidades']) {
                    $agente['especialidades'] = explode(',', $agente['especialidades']);
                    $agente['especialidades'] = array_map('trim', $agente['especialidades']);
                }
                
                // Procesar idiomas
                if ($agente['idiomas']) {
                    $agente['idiomas'] = explode(',', $agente['idiomas']);
                    $agente['idiomas'] = array_map('trim', $agente['idiomas']);
                }
                
                // Calcular tiempo desde el registro
                if ($agente['fecha_registro']) {
                    $fechaRegistro = new DateTime($agente['fecha_registro']);
                    $ahora = new DateTime();
                    $diferencia = $fechaRegistro->diff($ahora);
                    $agente['tiempo_registro'] = $diferencia->y > 0 ? $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '') : 
                                               ($diferencia->m > 0 ? $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '') : 
                                               $diferencia->d . ' día' . ($diferencia->d > 1 ? 's' : ''));
                }
                
                // Calcular tiempo desde último acceso
                if ($agente['ultimo_acceso']) {
                    $ultimoAcceso = new DateTime($agente['ultimo_acceso']);
                    $ahora = new DateTime();
                    $diferencia = $ultimoAcceso->diff($ahora);
                    $agente['ultimo_acceso_hace'] = $diferencia->y > 0 ? $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '') : 
                                                  ($diferencia->m > 0 ? $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '') : 
                                                  ($diferencia->d > 0 ? $diferencia->d . ' día' . ($diferencia->d > 1 ? 's' : '') : 
                                                  ($diferencia->h > 0 ? $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '') : 
                                                  $diferencia->i . ' minuto' . ($diferencia->i > 1 ? 's' : ''))));
                }
                
                // Formatear calificación promedio
                if ($agente['calificacion_promedio']) {
                    $agente['calificacion_promedio'] = round($agente['calificacion_promedio'], 1);
                }
            }
            
            return $agentes;
            
        } catch (Exception $e) {
            error_log("Error obteniendo agentes con perfil público: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener lista de agentes con perfiles públicos activos con filtros avanzados
     * 
     * @param string $ciudad Ciudad para filtrar (opcional)
     * @param string $experiencia Experiencia mínima (opcional)
     * @param string $idioma Idioma para filtrar (opcional)
     * @param string $ordenar Criterio de ordenamiento
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de agentes
     */
    public function getAgentesConPerfilPublicoFiltrados($ciudad = '', $experiencia = '', $idioma = '', $ordenar = 'nombre', $limit = 12, $offset = 0) {
        try {
            // Construir condiciones WHERE
            $conditions = [
                "u.rol = 'agente'",
                "u.estado = 'activo'",
                "u.perfil_publico_activo = 1"
            ];
            $params = [];
            
            // Filtro por ciudad
            if (!empty($ciudad)) {
                $conditions[] = "u.ciudad LIKE ?";
                $params[] = "%{$ciudad}%";
            }
            
            // Filtro por experiencia mínima
            if (!empty($experiencia)) {
                $conditions[] = "u.experiencia_anos >= ?";
                $params[] = intval($experiencia);
            }
            
            // Filtro por idioma
            if (!empty($idioma)) {
                $conditions[] = "u.idiomas LIKE ?";
                $params[] = "%{$idioma}%";
            }
            
            $whereClause = implode(' AND ', $conditions);
            
            // Ordenamiento
            $orderBy = 'u.nombre, u.apellido';
            switch($ordenar) {
                case 'experiencia':
                    $orderBy = 'u.experiencia_anos DESC, u.nombre, u.apellido';
                    break;
                case 'reciente':
                    $orderBy = 'u.fecha_registro DESC, u.nombre, u.apellido';
                    break;
                case 'propiedades':
                    // Por ahora ordenar por experiencia, después se puede mejorar
                    $orderBy = 'u.experiencia_anos DESC, u.nombre, u.apellido';
                    break;
                case 'calificacion':
                    // Por ahora ordenar por nombre, después se puede mejorar
                    $orderBy = 'u.nombre, u.apellido';
                    break;
            }
            
            // Consulta completa con todos los campos necesarios
            $query = "SELECT 
                        u.id,
                        u.nombre,
                        u.apellido,
                        u.email,
                        u.telefono,
                        u.ciudad,
                        u.sector,
                        u.experiencia_anos,
                        u.especialidades,
                        u.idiomas,
                        u.biografia,
                        u.licencia_inmobiliaria,
                        u.horario_disponibilidad,
                        u.foto_perfil,
                        u.fecha_registro,
                        u.ultimo_acceso,
                        COALESCE(p_stats.total_propiedades, 0) as total_propiedades,
                        COALESCE(p_stats.propiedades_activas, 0) as propiedades_activas,
                        COALESCE(p_stats.propiedades_vendidas, 0) as propiedades_vendidas,
                        COALESCE(ca_stats.calificacion_promedio, 0) as calificacion_promedio,
                        COALESCE(ca_stats.total_calificaciones, 0) as total_calificaciones
                      FROM {$this->table} u
                      LEFT JOIN (
                          SELECT 
                              agente_id,
                              COUNT(*) as total_propiedades,
                              COUNT(CASE WHEN estado_publicacion = 'activa' THEN 1 END) as propiedades_activas,
                              COUNT(CASE WHEN estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas
                          FROM propiedades 
                          GROUP BY agente_id
                      ) p_stats ON u.id = p_stats.agente_id
                      LEFT JOIN (
                          SELECT 
                              agente_id,
                              AVG(calificacion) as calificacion_promedio,
                              COUNT(*) as total_calificaciones
                          FROM calificaciones_agentes 
                          GROUP BY agente_id
                      ) ca_stats ON u.id = ca_stats.agente_id
                      WHERE {$whereClause}
                      ORDER BY {$orderBy}
                      LIMIT ? OFFSET ?";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $agentes = $this->db->select($query, $params);
            
            // Procesar datos para cada agente
            foreach ($agentes as &$agente) {
                // Procesar especialidades
                if (!empty($agente['especialidades'])) {
                    if (is_string($agente['especialidades'])) {
                        $agente['especialidades'] = array_filter(array_map('trim', explode(',', $agente['especialidades'])));
                    }
                } else {
                $agente['especialidades'] = [];
                }
                
                // Procesar idiomas
                if (!empty($agente['idiomas'])) {
                    if (is_string($agente['idiomas'])) {
                        $agente['idiomas'] = array_filter(array_map('trim', explode(',', $agente['idiomas'])));
                    }
                } else {
                $agente['idiomas'] = [];
                }
                
                // Calcular tiempo de registro
                if (!empty($agente['fecha_registro'])) {
                    $fechaRegistro = new DateTime($agente['fecha_registro']);
                    $ahora = new DateTime();
                    $diferencia = $fechaRegistro->diff($ahora);
                    
                    if ($diferencia->y > 0) {
                        $agente['tiempo_registro'] = $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '');
                    } elseif ($diferencia->m > 0) {
                        $agente['tiempo_registro'] = $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '');
                    } else {
                        $agente['tiempo_registro'] = $diferencia->d . ' día' . ($diferencia->d > 1 ? 's' : '');
                    }
                } else {
                    $agente['tiempo_registro'] = '';
                }
                
                // Calcular último acceso
                if (!empty($agente['ultimo_acceso'])) {
                    $ultimoAcceso = new DateTime($agente['ultimo_acceso']);
                    $ahora = new DateTime();
                    $diferencia = $ultimoAcceso->diff($ahora);
                    
                    if ($diferencia->y > 0) {
                        $agente['ultimo_acceso_hace'] = $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '');
                    } elseif ($diferencia->m > 0) {
                        $agente['ultimo_acceso_hace'] = $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '');
                    } elseif ($diferencia->d > 0) {
                        $agente['ultimo_acceso_hace'] = $diferencia->d . ' día' . ($diferencia->d > 1 ? 's' : '');
                    } elseif ($diferencia->h > 0) {
                        $agente['ultimo_acceso_hace'] = $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
                    } else {
                        $agente['ultimo_acceso_hace'] = 'Hace unos minutos';
                    }
                } else {
                    $agente['ultimo_acceso_hace'] = '';
                }
                
                // Asegurar que los campos numéricos sean números
                $agente['propiedades_activas'] = (int)($agente['propiedades_activas'] ?? 0);
                $agente['propiedades_vendidas'] = (int)($agente['propiedades_vendidas'] ?? 0);
                $agente['total_propiedades'] = (int)($agente['total_propiedades'] ?? 0);
                $agente['calificacion_promedio'] = (float)($agente['calificacion_promedio'] ?? 0);
                $agente['total_calificaciones'] = (int)($agente['total_calificaciones'] ?? 0);
                $agente['experiencia_anos'] = (int)($agente['experiencia_anos'] ?? 0);
                
                // Mapear campos para compatibilidad con la vista
                $agente['descripcion_corta'] = $agente['biografia'] ?? '';
                $agente['horario_atencion'] = $agente['horario_disponibilidad'] ?? '';
            }
            
            return $agentes;
        } catch (Exception $e) {
            error_log("Error obteniendo agentes con perfil público filtrados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener usuarios por rol
     * 
     * @param string $role Rol del usuario
     * @return int Total de usuarios con ese rol
     */
    public function getUsersByRole($role) {
        $query = "SELECT * FROM {$this->table} WHERE rol = ? ORDER BY fecha_registro DESC";
        return $this->db->select($query, [$role]);
    }
    
    /**
     * Obtener usuarios activos
     * 
     * @return int Total de usuarios activos
     */
    public function getActiveUsers() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado = 'activo'";
        $resultado = $this->db->selectOne($query);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener usuarios nuevos este mes
     * 
     * @return int Total de usuarios nuevos este mes
     */
    public function getNewUsersThisMonth() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE MONTH(fecha_registro) = MONTH(NOW()) AND YEAR(fecha_registro) = YEAR(NOW())";
        $resultado = $this->db->selectOne($query);
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Obtener todos los usuarios
     * 
     * @return array Lista de todos los usuarios
     */
    public function getAllUsers() {
        $query = "SELECT * FROM {$this->table} ORDER BY fecha_registro DESC";
        return $this->db->select($query);
    }
    
    /**
     * Buscar usuarios para el panel de administración
     * 
     * @param string $search Término de búsqueda (nombre, email, username)
     * @param string $role Filtro por rol
     * @param string $status Filtro por estado
     * @return array Lista de usuarios filtrados
     */
    public function searchUsersForAdmin($search = '', $role = '', $status = '') {
        $conditions = [];
        $params = [];
        
        // Filtro de búsqueda
        if (!empty($search)) {
            $conditions[] = "(nombre LIKE ? OR apellido LIKE ? OR email LIKE ? OR username LIKE ? OR CONCAT(nombre, ' ', apellido) LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        // Filtro por rol
        if (!empty($role)) {
            $conditions[] = "rol = ?";
            $params[] = $role;
        }
        
        // Filtro por estado
        if (!empty($status)) {
            $conditions[] = "estado = ?";
            $params[] = $status;
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        $query = "SELECT * FROM {$this->table} {$whereClause} ORDER BY fecha_registro DESC";
        return $this->db->select($query, $params);
    }
    
    /**
     * Cambiar estado de usuario
     * 
     * @param int $userId ID del usuario
     * @param string $newStatus Nuevo estado
     * @return bool True si se actualizó correctamente
     */
    public function changeUserStatus($userId, $newStatus) {
        $query = "UPDATE {$this->table} SET estado = ? WHERE id = ?";
        $result = $this->db->update($query, [$newStatus, $userId]);
        
        // Registrar actividad
        if ($result) {
            require_once APP_PATH . '/models/ActivityLog.php';
            $user = $this->getById($userId);
            if ($user) {
                ActivityLog::log($_SESSION['user_id'] ?? 1, 'update', $this->table, $userId, [
                    'campo' => 'estado',
                    'valor_anterior' => $user['estado'],
                    'valor_nuevo' => $newStatus,
                    'usuario_afectado' => $user['nombre'] . ' ' . $user['apellido']
                ]);
            }
        }
        
        return $result;
    }
    
    /**
     * Cambiar rol de usuario
     * 
     * @param int $userId ID del usuario
     * @param string $newRole Nuevo rol
     * @return bool True si se actualizó correctamente
     */
    public function changeUserRole($userId, $newRole) {
        $query = "UPDATE {$this->table} SET rol = ? WHERE id = ?";
        $result = $this->db->update($query, [$newRole, $userId]);
        
        // Registrar actividad
        if ($result) {
            require_once APP_PATH . '/models/ActivityLog.php';
            $user = $this->getById($userId);
            if ($user) {
                ActivityLog::log($_SESSION['user_id'] ?? 1, 'update', $this->table, $userId, [
                    'campo' => 'rol',
                    'valor_anterior' => $user['rol'],
                    'valor_nuevo' => $newRole,
                    'usuario_afectado' => $user['nombre'] . ' ' . $user['apellido']
                ]);
            }
        }
        
        return $result;
    }
    
    /**
     * Eliminar usuario
     * 
     * @param int $userId ID del usuario
     * @return bool True si se eliminó correctamente
     */
    public function deleteUser($userId) {
        // Verificar que no sea el usuario actual
        if ($userId == ($_SESSION['user_id'] ?? 0)) {
            return false;
        }
        
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $result = $this->db->delete($query, [$userId]);
        
        // Registrar actividad
        if ($result) {
            require_once APP_PATH . '/models/ActivityLog.php';
            ActivityLog::log($_SESSION['user_id'] ?? 1, 'delete', $this->table, $userId, [
                'accion' => 'usuario_eliminado',
                'eliminado_por' => $_SESSION['user_nombre'] ?? 'Administrador'
            ]);
        }
        
        return $result;
    }
    
    /**
     * Actualizar usuario por administrador
     * 
     * @param int $userId ID del usuario
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function updateUserByAdmin($userId, $data) {
        // Preparar campos a actualizar
        $updateFields = [];
        $params = [];
        
        $fields = ['nombre', 'apellido', 'email', 'telefono', 'rol', 'estado', 'ciudad', 'sector'];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        
        // Agregar contraseña si se proporciona
        if (isset($data['password'])) {
            $updateFields[] = "password = ?";
            $params[] = $data['password'];
        }
        
        // Agregar fecha de actualización (solo si la columna existe)
        // $updateFields[] = "fecha_actualizacion = NOW()";
        
        if (empty($updateFields)) {
            return false;
        }
        
        // Construir query
        $query = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $params[] = $userId;
        
        $result = $this->db->update($query, $params);
        
        // Registrar actividad
        if ($result) {
            require_once APP_PATH . '/models/ActivityLog.php';
            $user = $this->getById($userId);
            if ($user) {
                ActivityLog::log($_SESSION['user_id'] ?? 1, 'update', $this->table, $userId, [
                    'accion' => 'usuario_actualizado_por_admin',
                    'usuario_afectado' => $user['nombre'] . ' ' . $user['apellido'],
                    'campos_actualizados' => array_keys($data)
                ]);
            }
        }
        
        return $result;
    }
    
    /**
     * Obtener usuarios por mes
     * 
     * @param int $months Número de meses
     * @return array Datos de usuarios por mes
     */
    public function getUsersByMonth($months = 12) {
        $query = "SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as mes, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                  GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m') 
                  ORDER BY mes";
        return $this->db->select($query, [$months]);
    }
    
    /**
     * Obtener usuarios por mes para dashboard (formato específico)
     * 
     * @param int $months Número de meses
     * @return array Datos de usuarios por mes en formato dashboard
     */
    public function getUsersByMonthForDashboard($months = 12) {
        $query = "SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as mes, COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                  GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m') 
                  ORDER BY mes";
        $results = $this->db->select($query, [$months]);
        
        // Crear array con 12 meses (0-11)
        $data = array_fill(0, 12, ['total' => 0]);
        
        // Mapear los resultados a los meses correspondientes
        foreach ($results as $row) {
            $month = (int)substr($row['mes'], -2) - 1; // Convertir mes a índice 0-11
            if ($month >= 0 && $month < 12) {
                $data[$month] = ['total' => (int)$row['total']];
            }
        }
        
        return $data;
    }
    
    /**
     * Obtener usuarios por estado
     * 
     * @return array Datos de usuarios por estado
     */
    public function getUsersByStatus() {
        $query = "SELECT estado, COUNT(*) as total FROM {$this->table} GROUP BY estado";
        return $this->db->select($query);
    }
    
    /**
     * Obtener usuarios por ciudad
     * 
     * @return array Datos de usuarios por ciudad
     */
    public function getUsersByCity() {
        $query = "SELECT ciudad, COUNT(*) as total FROM {$this->table} WHERE ciudad IS NOT NULL GROUP BY ciudad ORDER BY total DESC";
        return $this->db->select($query);
    }
    
    /**
     * Obtener usuarios recientes
     * 
     * @param int $limit Límite de usuarios
     * @return array Lista de usuarios recientes
     */
    public function getRecentUsers($limit = 10) {
        $query = "SELECT * FROM {$this->table} ORDER BY fecha_registro DESC LIMIT ?";
        return $this->db->select($query, [$limit]);
    }
    
    /**
     * Obtener agentes para API
     * 
     * @return array Lista de agentes para API
     */
    public function getAgentsForAPI() {
        $query = "SELECT id, nombre, apellido, email, telefono, ciudad, experiencia_anos 
                  FROM {$this->table} 
                  WHERE rol = 'agente' AND estado = 'activo' 
                  ORDER BY nombre, apellido";
        return $this->db->select($query);
    }
    
    /**
     * Obtener perfil público de agente
     * 
     * @param int $agentId ID del agente
     * @return array Perfil del agente
     */
    public function getAgentPublicProfile($agentId) {
        $query = "SELECT id, nombre, apellido, email, telefono, ciudad, sector, 
                         experiencia_anos, especialidades, idiomas, biografia, 
                         descripcion_corta, licencia_inmobiliaria, horario_atencion, 
                         foto_perfil, fecha_registro 
                  FROM {$this->table} 
                  WHERE id = ? AND rol = 'agente' AND estado = 'activo'";
        return $this->db->selectOne($query, [$agentId]);
    }
    
    /**
     * Obtener estadísticas de agente
     * 
     * @param int $agentId ID del agente
     * @return array Estadísticas del agente
     */
    public function getAgentStats($agentId) {
        $query = "SELECT 
                    COUNT(p.id) as total_propiedades,
                    COUNT(CASE WHEN p.estado_publicacion = 'activa' THEN 1 END) as propiedades_activas,
                    COUNT(CASE WHEN p.estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas,
                    COALESCE(AVG(ca.calificacion), 0) as calificacion_promedio,
                    COUNT(ca.id) as total_calificaciones
                  FROM {$this->table} u
                  LEFT JOIN propiedades p ON u.id = p.agente_id
                  LEFT JOIN calificaciones_agentes ca ON u.id = ca.agente_id
                  WHERE u.id = ? AND u.rol = 'agente'";
        return $this->db->selectOne($query, [$agentId]);
    }
    
    /**
     * Obtener el total de usuarios en el sistema
     * 
     * @return int Total de usuarios
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->selectOne($query);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener el total de usuarios por rol
     * 
     * @param string $role Rol del usuario
     * @return int Total de usuarios con ese rol
     */
    public function getCountByRole($role) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE rol = ?";
        $result = $this->db->selectOne($query, [$role]);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener usuarios por estado específico
     */
    public function getUsersByStatusCount($status) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado = ?";
        $result = $this->db->selectOne($query, [$status]);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener usuarios nuevos hoy
     */
    public function getNewUsersToday() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(fecha_registro) = CURDATE()";
        $result = $this->db->selectOne($query);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener usuarios nuevos esta semana
     */
    public function getNewUsersThisWeek() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE YEARWEEK(fecha_registro) = YEARWEEK(NOW())";
        $result = $this->db->selectOne($query);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener usuarios activos por rol
     */
    public function getActiveUsersByRole($role) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE rol = ? AND estado = 'activo'";
        $result = $this->db->selectOne($query, [$role]);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Obtener datos de usuarios por período (semana, trimestre, año)
     * @param string $periodType 'week', 'quarter', 'year'
     * @param int $limit Número de períodos a obtener
     * @return array ['labels' => [], 'data' => []]
     */
    public function getUsersByPeriod($periodType, $limit) {
        $query = "";
        $labels = [];
        $data = [];

        switch ($periodType) {
            case 'week':
                // Get data for the last 'limit' weeks
                $query = "SELECT
                            YEARWEEK(fecha_registro, 1) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL ? WEEK)
                          GROUP BY period_key
                          ORDER BY period_key ASC";
                $results = $this->db->select($query, [$limit]);

                // Generate labels and map data
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $labels[] = 'Sem ' . ($limit - $i);
                    $data[] = 0; // Initialize with 0
                }

                // Map actual data to the correct positions
                foreach ($results as $row) {
                    $data[] = (int)$row['total'];
                }
                
                // Ensure we have exactly 'limit' data points
                while (count($data) < $limit) {
                    $data[] = 0;
                }
                $data = array_slice($data, -$limit);
                
                return ['labels' => $labels, 'data' => $data];

            case 'quarter':
                $query = "SELECT
                            CONCAT(YEAR(fecha_registro), '-Q', QUARTER(fecha_registro)) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL ? QUARTER)
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
                            YEAR(fecha_registro) as period_key,
                            COUNT(*) as total
                          FROM {$this->table}
                          WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL ? YEAR)
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
} 
