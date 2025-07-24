<?php
/**
 * Archivo Principal de Entrada
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo es el punto de entrada principal de la aplicación.
 * Maneja todas las solicitudes HTTP y las dirige al controlador apropiado.
 */

// Incluir configuración de la aplicación
require_once '../config/config.php';

// Incluir autoload de Composer (para PHPMailer y otras dependencias)
require_once '../vendor/autoload.php';

// Incluir clases principales
require_once APP_PATH . '/core/Router.php';

// Configurar manejo de errores
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Configurar manejo de excepciones
set_exception_handler(function($exception) {
    error_log("Excepción no manejada: " . $exception->getMessage());
    
    if (defined('APP_ENV') && APP_ENV === 'development') {
        // En desarrollo, mostrar detalles del error
        echo "<h1>Error de la Aplicación</h1>";
        echo "<p><strong>Mensaje:</strong> " . $exception->getMessage() . "</p>";
        echo "<p><strong>Archivo:</strong> " . $exception->getFile() . "</p>";
        echo "<p><strong>Línea:</strong> " . $exception->getLine() . "</p>";
        echo "<h2>Stack Trace:</h2>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        // En producción, mostrar página de error genérica
        if (!headers_sent()) {
            http_response_code(500);
        }
        $pageTitle = 'Error del Servidor - ' . APP_NAME;
        include APP_PATH . '/views/errors/500.php';
    }
});

// Configurar manejo de errores fatales
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Error fatal: " . $error['message']);
        
        if (defined('APP_ENV') && APP_ENV === 'development') {
            echo "<h1>Error Fatal</h1>";
            echo "<p><strong>Mensaje:</strong> " . $error['message'] . "</p>";
            echo "<p><strong>Archivo:</strong> " . $error['file'] . "</p>";
            echo "<p><strong>Línea:</strong> " . $error['line'] . "</p>";
        } else {
            if (!headers_sent()) {
                http_response_code(500);
            }
            $pageTitle = 'Error del Servidor - ' . APP_NAME;
            include APP_PATH . '/views/errors/500.php';
        }
    }
});

try {
    // Crear instancia del router
    $router = new Router();
    
    // Configurar todas las rutas
    $router->configureRoutes();
    
    // Ejecutar el router
    $router->run();
    
} catch (Exception $e) {
    // Manejar excepciones del router
    error_log("Error en el router: " . $e->getMessage());
    
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo "<h1>Error del Router</h1>";
        echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
        echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    } else {
        if (!headers_sent()) {
            http_response_code(500);
        }
        $pageTitle = 'Error del Servidor - ' . APP_NAME;
        include APP_PATH . '/views/errors/500.php';
    }
} 
