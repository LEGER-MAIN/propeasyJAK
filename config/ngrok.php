<?php
/**
 * Configuración flexible para WebSocket - Local y ngrok
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
        $_SERVER['HTTP_HOST'] === 'localhost:80' ||
        $_SERVER['HTTP_HOST'] === '127.0.0.1:8000' ||
        $_SERVER['HTTP_HOST'] === '127.0.0.1:80' ||
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
        return 'http://localhost:80';
    }
    
    // Fallback a la configuración por defecto
    return defined('APP_URL') ? APP_URL : 'http://localhost:80';
}

// Configurar APP_URL dinámicamente
if (!function_exists('getAppUrl')) {
    function getAppUrl() {
        return getDynamicBaseUrl();
    }
}

// Configuración flexible para WebSocket
function getWebSocketUrl() {
    // 1. Intentar detectar ngrok automáticamente
    if (isNgrok()) {
        // Intentar obtener la URL del WebSocket desde la API de ngrok
        try {
            $tunnels = @file_get_contents('http://localhost:4040/api/tunnels');
            if ($tunnels !== false) {
                $data = json_decode($tunnels, true);
                
                if ($data && isset($data['tunnels'])) {
                    foreach ($data['tunnels'] as $tunnel) {
                        if ($tunnel['name'] === 'websocket' || 
                            (isset($tunnel['config']['addr']) && $tunnel['config']['addr'] === 'http://localhost:8080')) {
                            return 'wss://' . parse_url($tunnel['public_url'], PHP_URL_HOST);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Continuar con fallback
        }
        
        // Si no se puede obtener, usar la URL hardcodeada como fallback
        return 'wss://e4c5a0211f2f.ngrok-free.app';
    }
    
    // 2. Para desarrollo local (sin ngrok)
    if (isLocalDevelopment()) {
        return 'ws://localhost:8080';
    }
    
    // 3. Fallback para cualquier otro caso
    return 'ws://localhost:8080';
}

// Función para obtener la URL del WebSocket desde JavaScript
function getWebSocketUrlForJS() {
    $wsUrl = getWebSocketUrl();
    // Escapar para JavaScript
    return str_replace('"', '\\"', $wsUrl);
}

// Función para verificar si el WebSocket está disponible
function isWebSocketAvailable() {
    $wsUrl = getWebSocketUrl();
    
    if (strpos($wsUrl, 'wss://') === 0) {
        // Para ngrok, asumir que está disponible
        return true;
    } else {
        // Para local, verificar si el puerto está abierto
        $host = 'localhost';
        $port = 8080;
        
        $connection = @fsockopen($host, $port, $errno, $errstr, 2);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
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

// Configuración de sesión flexible
function configureSessionForEnvironment() {
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

// Función para obtener información del entorno
function getEnvironmentInfo() {
    return [
        'is_ngrok' => isNgrok(),
        'is_local' => isLocalDevelopment(),
        'base_url' => getDynamicBaseUrl(),
        'websocket_url' => getWebSocketUrl(),
        'websocket_available' => isWebSocketAvailable()
    ];
}

// Aplicar configuración automáticamente
if (isNgrok() || isLocalDevelopment()) {
    // Solo configurar sesión si no se han enviado headers
    if (!headers_sent()) {
        configureSessionForEnvironment();
    }
    if (isNgrok() && !headers_sent()) {
        setNgrokHeaders();
    }
} 







