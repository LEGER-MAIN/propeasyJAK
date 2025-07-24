<?php
/**
 * Vista: Error 500 - Error Interno del Servidor
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Verificar si ya se está usando el layout principal
if (!defined('APP_NAME')) {
    // Si no está definido, usar HTML completo
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error 500 - Error Interno del Servidor</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                background: #f8fafc; 
                color: #333; 
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container { 
                max-width: 400px; 
                background: #fff; 
                border-radius: 8px; 
                box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
                padding: 32px; 
                text-align: center; 
            }
            h1 { color: #dc2626; font-size: 3em; margin-bottom: 0.2em; }
            h2 { color: #374151; margin-bottom: 1em; }
            p { margin-bottom: 1.5em; color: #6b7280; }
            a { 
                display: inline-block; 
                background: #2563eb; 
                color: #fff; 
                padding: 10px 24px; 
                border-radius: 6px; 
                text-decoration: none; 
                margin-top: 1em;
                transition: background-color 0.2s;
            }
            a:hover { background: #1d4ed8; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>500</h1>
            <h2>Error Interno del Servidor</h2>
            <p>Ha ocurrido un error inesperado.<br>Por favor, intenta nuevamente más tarde.</p>
            <a href="/">Volver al inicio</a>
        </div>
    </body>
    </html>
    <?php
    exit;
} else {
    // Si está definido, usar el layout principal
    $content = ob_start();
    ?>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl font-bold text-red-600">500</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Error Interno del Servidor</h1>
            <p class="text-gray-600 mb-8">
                Ha ocurrido un error inesperado. Por favor, intenta nuevamente más tarde.
            </p>
            <div class="space-y-3">
                <a href="/" class="block w-full bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Volver al inicio
                </a>
                <button onclick="history.back()" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
                    Volver atrás
                </button>
            </div>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    include APP_PATH . '/views/layouts/main.php';
}
?> 
