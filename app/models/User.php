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
} 