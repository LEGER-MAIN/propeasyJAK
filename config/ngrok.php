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
        $baseUrl = getDynamicBaseUrl();
        // Cambiar http/https por ws/wss
        $wsUrl = str_replace(['http://', 'https://'], ['ws://', 'wss://'], $baseUrl);
        return $wsUrl . ':8080';
    } elseif (isLocalDevelopment()) {
        return 'ws://localhost:8080';
    }
    
    // Fallback para desarrollo local
    return 'ws://localhost:8080';
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