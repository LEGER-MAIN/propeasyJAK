<?php
/**
 * Configuración General de la Aplicación
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene configuraciones generales del sistema
 * y funciones de inicialización.
 */

// Incluir configuración de base de datos
require_once __DIR__ . '/database.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Configuración de zona horaria
date_default_timezone_set('America/Santo_Domingo');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Iniciar sesión
session_start();

// Configuración de rutas
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . '/app');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', ROOT_PATH . '/uploads');
if (!defined('UPLOADS_URL')) define('UPLOADS_URL', '/uploads');

// Configuración de roles de usuario
if (!defined('ROLE_CLIENTE')) define('ROLE_CLIENTE', 'cliente');
if (!defined('ROLE_AGENTE')) define('ROLE_AGENTE', 'agente');
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 'admin');

// Estados de propiedades
if (!defined('PROPERTY_STATUS_PENDING')) define('PROPERTY_STATUS_PENDING', 'en_revision');
if (!defined('PROPERTY_STATUS_ACTIVE')) define('PROPERTY_STATUS_ACTIVE', 'activa');
if (!defined('PROPERTY_STATUS_SOLD')) define('PROPERTY_STATUS_SOLD', 'vendida');
if (!defined('PROPERTY_STATUS_REJECTED')) define('PROPERTY_STATUS_REJECTED', 'rechazada');

// Estados de solicitudes
if (!defined('REQUEST_STATUS_NEW')) define('REQUEST_STATUS_NEW', 'nuevo');
if (!defined('REQUEST_STATUS_REVIEW')) define('REQUEST_STATUS_REVIEW', 'en_revision');
if (!defined('REQUEST_STATUS_MEETING')) define('REQUEST_STATUS_MEETING', 'reunion_agendada');
if (!defined('REQUEST_STATUS_CLOSED')) define('REQUEST_STATUS_CLOSED', 'cerrado');

// Estados de citas
if (!defined('APPOINTMENT_STATUS_PROPOSED')) define('APPOINTMENT_STATUS_PROPOSED', 'propuesta');
if (!defined('APPOINTMENT_STATUS_ACCEPTED')) define('APPOINTMENT_STATUS_ACCEPTED', 'aceptada');
if (!defined('APPOINTMENT_STATUS_REJECTED')) define('APPOINTMENT_STATUS_REJECTED', 'rechazada');
if (!defined('APPOINTMENT_STATUS_COMPLETED')) define('APPOINTMENT_STATUS_COMPLETED', 'realizada');

// Estados de reportes
if (!defined('REPORT_STATUS_PENDING')) define('REPORT_STATUS_PENDING', 'pendiente');
if (!defined('REPORT_STATUS_ATTENDED')) define('REPORT_STATUS_ATTENDED', 'atendido');
if (!defined('REPORT_STATUS_DISCARDED')) define('REPORT_STATUS_DISCARDED', 'descartado');

// Configuración de Email (PHPMailer)
if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.gmail.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', 'propeasycorp@gmail.com');
if (!defined('SMTP_PASS')) define('SMTP_PASS', 'pytxdgkuxcatapyn');
if (!defined('SMTP_FROM')) define('SMTP_FROM', 'propeasycorp@gmail.com');
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'PropEasy - Sistema de Bienes Raíces');

// Configuración de Email de Soporte
if (!defined('SUPPORT_EMAIL')) define('SUPPORT_EMAIL', 'propeasy.soporte@gmail.com');
if (!defined('SUPPORT_PHONE')) define('SUPPORT_PHONE', '809 359 5322');

// Configuración de la aplicación
if (!defined('APP_NAME')) define('APP_NAME', 'PropEasy');
if (!defined('APP_URL')) define('APP_URL', 'http://localhost');
if (!defined('APP_VERSION')) define('APP_VERSION', '1.0.0');

// Configuración de entorno
if (!defined('APP_ENV')) define('APP_ENV', 'production');

// Función para mostrar errores detallados en modo desarrollo
function showDetailedError($error, $file = '', $line = '') {
    if (APP_ENV === 'development') {
        echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px; font-family: monospace;">';
        echo '<h3>Error en modo desarrollo:</h3>';
        echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($error) . '</p>';
        if ($file) echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($file) . '</p>';
        if ($line) echo '<p><strong>Línea:</strong> ' . htmlspecialchars($line) . '</p>';
        echo '<p><strong>Backtrace:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5), true)) . '</pre>';
        echo '</div>';
    }
}

// Manejador de errores personalizado
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . " - Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($error_message, 3, __DIR__ . '/../logs/error.log');
    
    if (APP_ENV === 'development') {
        showDetailedError($errstr, $errfile, $errline);
    }
    
    return true;
}

// Manejador de excepciones personalizado
function customExceptionHandler($exception) {
    $error_message = date('Y-m-d H:i:s') . " - Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($error_message, 3, __DIR__ . '/../logs/error.log');
    
    if (APP_ENV === 'development') {
        showDetailedError($exception->getMessage(), $exception->getFile(), $exception->getLine());
    } else {
        // En producción, mostrar página de error genérica
        http_response_code(500);
        include APP_PATH . '/views/errors/500.php';
    }
}

// Configurar manejadores de errores
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Configuración de archivos
if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 5 * 1024 * 1024);
if (!defined('ALLOWED_EXTENSIONS')) define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configuración de sesión
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 3600);
if (!defined('SESSION_NAME')) define('SESSION_NAME', 'propeasy_session');

// Configuración de tokens
if (!defined('TOKEN_EXPIRY')) define('TOKEN_EXPIRY', 3600);
if (!defined('PASSWORD_RESET_EXPIRY')) define('PASSWORD_RESET_EXPIRY', 1800);

/**
 * Función para cargar clases automáticamente
 */
spl_autoload_register(function ($class) {
    // Convertir namespace a ruta de archivo
    $file = ROOT_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Función para obtener la URL base de la aplicación
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $path;
}

/**
 * Función para redirigir a una URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Función para generar CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Función para verificar CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Función para limpiar datos de entrada
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Función para validar email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Función para generar mensajes flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Función para obtener y limpiar mensajes flash
 */
function getFlashMessages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Función para verificar si el usuario está autenticado
 */
function isAuthenticated() {
    $authenticated = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    error_log("isAuthenticated() - Session ID: " . session_id() . ", User ID: " . ($_SESSION['user_id'] ?? 'NO SET') . ", Result: " . ($authenticated ? 'TRUE' : 'FALSE'));
    return $authenticated;
}

/**
 * Función para verificar el rol del usuario
 */
function hasRole($role) {
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === $role;
}

/**
 * Función para verificar si el usuario tiene al menos uno de los roles especificados
 */
function hasAnyRole($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return isset($_SESSION['user_rol']) && in_array($_SESSION['user_rol'], $roles);
}

/**
 * Función para requerir autenticación
 */
function requireAuth() {
    if (!isAuthenticated()) {
        setFlashMessage('error', 'Debes iniciar sesión para acceder a esta página.');
        redirect('/login');
    }
}

/**
 * Función para requerir un rol específico
 */
function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        setFlashMessage('error', 'No tienes permisos para acceder a esta página.');
        redirect('/dashboard');
    }
}

/**
 * Función para requerir al menos uno de los roles especificados
 */
function requireAnyRole($roles) {
    requireAuth();
    if (!hasAnyRole($roles)) {
        setFlashMessage('error', 'No tienes permisos para acceder a esta página.');
        redirect('/dashboard');
    }
} 