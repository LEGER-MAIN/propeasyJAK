<?php
/**
 * Controlador AuthController - Gestión de Autenticación
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con la autenticación:
 * login, registro, verificación de email, recuperación de contraseñas, etc.
 */

require_once APP_PATH . '/models/User.php';

class AuthController {
    private $userModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Mostrar página de inicio de sesión
     */
    public function showLogin() {
        // Si ya está autenticado, redirigir al dashboard
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $pageTitle = 'Iniciar Sesión - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        include APP_PATH . '/views/auth/login.php';
    }
    
    /**
     * Procesar inicio de sesión
     */
    public function login() {
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/login');
        }
        
        // Obtener y validar datos
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            setFlashMessage('error', 'Email y contraseña son requeridos.');
            redirect('/login');
        }
        
        // Intentar autenticar
        $result = $this->userModel->login($email, $password);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            
            // Redirigir según el rol
            switch ($_SESSION['user_rol']) {
                case ROLE_ADMIN:
                    redirect('/admin/dashboard');
                    break;
                case ROLE_AGENTE:
                    redirect('/agente/dashboard');
                    break;
                default:
                    redirect('/dashboard');
                    break;
            }
        } else {
            setFlashMessage('error', $result['message']);
            redirect('/login');
        }
    }
    
    /**
     * Mostrar página de registro
     */
    public function showRegister() {
        // Si ya está autenticado, redirigir al dashboard
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $pageTitle = 'Registrarse - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        include APP_PATH . '/views/auth/register.php';
    }
    
    /**
     * Procesar registro de usuario
     */
    public function register() {
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/register');
        }
        
        // Obtener datos del formulario
        $userData = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'rol' => $_POST['rol'] ?? ROLE_CLIENTE
        ];
        
        // Validar confirmación de contraseña
        if ($userData['password'] !== $userData['confirm_password']) {
            setFlashMessage('error', 'Las contraseñas no coinciden.');
            redirect('/register');
        }
        
        // Intentar registrar
        $result = $this->userModel->register($userData);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            redirect('/login');
        } else {
            setFlashMessage('error', $result['message']);
            redirect('/register');
        }
    }
    
    /**
     * Verificar email del usuario
     */
    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            setFlashMessage('error', 'Token de verificación requerido.');
            redirect('/login');
        }
        
        $result = $this->userModel->verifyEmail($token);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/login');
    }
    
    /**
     * Mostrar página de recuperación de contraseña
     */
    public function showForgotPassword() {
        // Si ya está autenticado, redirigir al dashboard
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $pageTitle = 'Recuperar Contraseña - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        include APP_PATH . '/views/auth/forgot-password.php';
    }
    
    /**
     * Procesar solicitud de recuperación de contraseña
     */
    public function forgotPassword() {
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/forgot-password');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/forgot-password');
        }
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            setFlashMessage('error', 'Email requerido.');
            redirect('/forgot-password');
        }
        
        $result = $this->userModel->requestPasswordReset($email);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/forgot-password');
    }
    
    /**
     * Mostrar página de reset de contraseña
     */
    public function showResetPassword() {
        // Si ya está autenticado, redirigir al dashboard
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            setFlashMessage('error', 'Token de recuperación requerido.');
            redirect('/login');
        }
        
        $pageTitle = 'Restablecer Contraseña - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        include APP_PATH . '/views/auth/reset-password.php';
    }
    
    /**
     * Procesar reset de contraseña
     */
    public function resetPassword() {
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/login');
        }
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($token) || empty($password) || empty($confirm_password)) {
            setFlashMessage('error', 'Todos los campos son requeridos.');
            redirect('/reset-password?token=' . $token);
        }
        
        if ($password !== $confirm_password) {
            setFlashMessage('error', 'Las contraseñas no coinciden.');
            redirect('/reset-password?token=' . $token);
        }
        
        $result = $this->userModel->resetPassword($token, $password);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            redirect('/login');
        } else {
            setFlashMessage('error', $result['message']);
            redirect('/reset-password?token=' . $token);
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        $result = $this->userModel->logout();
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
        } else {
            setFlashMessage('error', 'Error al cerrar sesión.');
        }
        
        redirect('/login');
    }
    
    /**
     * Mostrar página de perfil del usuario
     */
    public function showProfile() {
        requireAuth();
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            setFlashMessage('error', 'Usuario no encontrado.');
            redirect('/dashboard');
        }
        
        $pageTitle = 'Mi Perfil - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        include APP_PATH . '/views/auth/profile.php';
    }
    
    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile() {
        requireAuth();
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener datos del formulario
        $userData = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];
        
        // Validar confirmación de contraseña si se proporciona
        if (!empty($userData['password'])) {
            if ($userData['password'] !== $userData['confirm_password']) {
                setFlashMessage('error', 'Las contraseñas no coinciden.');
                redirect('/profile');
            }
        } else {
            // Si no se proporciona contraseña, remover del array
            unset($userData['password']);
            unset($userData['confirm_password']);
        }
        
        $result = $this->userModel->updateProfile($userId, $userData);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            
            // Actualizar datos de sesión si se cambió el nombre
            if (isset($userData['nombre'])) {
                $_SESSION['user_nombre'] = $userData['nombre'];
            }
            if (isset($userData['apellido'])) {
                $_SESSION['user_apellido'] = $userData['apellido'];
            }
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/profile');
    }
    
    /**
     * Mostrar página de gestión de usuarios (solo administradores)
     */
    public function showUsers() {
        requireRole(ROLE_ADMIN);
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $users = $this->userModel->getAll($limit, $offset);
        
        $pageTitle = 'Gestión de Usuarios - ' . APP_NAME;
        
        include APP_PATH . '/views/admin/users.php';
    }
    
    /**
     * Cambiar estado de usuario (solo administradores)
     */
    public function changeUserStatus() {
        requireRole(ROLE_ADMIN);
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/users');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/admin/users');
        }
        
        $userId = (int)($_POST['user_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        
        if ($userId <= 0 || empty($estado)) {
            setFlashMessage('error', 'Datos inválidos.');
            redirect('/admin/users');
        }
        
        $result = $this->userModel->changeStatus($userId, $estado);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/admin/users');
    }
} 