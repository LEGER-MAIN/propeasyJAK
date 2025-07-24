<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 400 - Solicitud Incorrecta | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Icono de Error -->
        <div class="mx-auto w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Error 400</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-6">Solicitud Incorrecta</h2>

        <!-- Mensaje -->
        <p class="text-gray-600 mb-8">
            La solicitud que has enviado no es válida. 
            Verifica los datos e intenta nuevamente.
        </p>

        <!-- Botones de Acción -->
        <div class="space-y-4">
            <a href="/" class="inline-block w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                Volver al Inicio
            </a>
            
            <a href="javascript:history.back()" class="inline-block w-full bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-lg hover:bg-gray-300 transition duration-200">
                Volver Atrás
            </a>
        </div>

        <!-- Información Adicional -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                Si el problema persiste, contacta a nuestro equipo de soporte.
            </p>
        </div>
    </div>
</body>
</html> 
