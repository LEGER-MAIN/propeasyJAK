<?php
/**
 * Configuración específica para ngrok
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Detectar si estamos en ngrok
function isNgrok() {
    return isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 
           $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' &&
           strpos($_SERVER['HTTP_HOST'], 'ngrok') !== false;
}

// Detectar si estamos en desarrollo local
function isLocalDevelopment() {
    return isset($_SERVER['HTTP_HOST']) && (
        $_SERVER['HTTP_HOST'] === 'localhost:8000' || 
        $_SERVER['HTTP_HOST'] === '127.0.0.1:8000' ||
        strpos($_SERVER['HTTP_HOST'], 'localhost') !== false
    );
}

// Obtener la URL base dinámicamente
function getDynamicBaseUrl() {
    if (isNgrok()) {
        $protocol = 'https';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host;
    } elseif (isLocalDevelopment()) {
        return 'http://localhost:8000';
    }
    
    // Fallback a la configuración por defecto
    return defined('APP_URL') ? APP_URL : 'http://localhost:8000';
}

// Configurar APP_URL dinámicamente si estamos en ngrok
if (isNgrok()) {
    if (defined('APP_URL')) {
        // Redefinir APP_URL si ya está definida
        $newUrl = getDynamicBaseUrl();
        if ($newUrl !== APP_URL) {
            // No podemos redefinir constantes, pero podemos usar una función
            function getAppUrl() {
                return getDynamicBaseUrl();
            }
        }
    } else {
        define('APP_URL', getDynamicBaseUrl());
    }
}

// Configuración específica para WebSocket en ngrok
function getWebSocketUrl() {
    if (isNgrok()) {
        // Intentar obtener la URL del WebSocket desde la API de ngrok
        try {
            $tunnels = file_get_contents('http://localhost:4040/api/tunnels');
            $data = json_decode($tunnels, true);
            
            if ($data && isset($data['tunnels'])) {
                foreach ($data['tunnels'] as $tunnel) {
                    if ($tunnel['name'] === 'websocket' || 
                        (isset($tunnel['config']['addr']) && $tunnel['config']['addr'] === 'http://localhost:8080')) {
                        return 'wss://' . parse_url($tunnel['public_url'], PHP_URL_HOST);
                    }
                }
            }
        } catch (Exception $e) {
            // Si no se puede obtener, usar la URL hardcodeada como fallback
        }
        
        // Fallback a la URL específica del WebSocket
        return 'wss://cb1c4a6910f9.ngrok-free.app';
    } elseif (isLocalDevelopment()) {
        return 'ws://localhost:8080';
    }
    
    // Fallback para desarrollo local
    return 'ws://localhost:8080';
}

// Función para obtener la URL del WebSocket desde JavaScript
function getWebSocketUrlForJS() {
    $wsUrl = getWebSocketUrl();
    // Escapar para JavaScript
    return str_replace('"', '\\"', $wsUrl);
}

// Configuración de CORS para ngrok
function setNgrokHeaders() {
    if (isNgrok()) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        
        // Manejar preflight OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
}

// Configuración de sesión para ngrok
function configureSessionForNgrok() {
    if (isNgrok()) {
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'None');
        ini_set('session.cookie_domain', '');
    } elseif (isLocalDevelopment()) {
        ini_set('session.cookie_secure', 0);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.cookie_domain', '');
    }
}

// Aplicar configuración automáticamente
if (isNgrok() || isLocalDevelopment()) {
    // Solo configurar sesión si no se han enviado headers
    if (!headers_sent()) {
        configureSessionForNgrok();
    }
    if (isNgrok() && !headers_sent()) {
        setNgrokHeaders();
    }
} 




