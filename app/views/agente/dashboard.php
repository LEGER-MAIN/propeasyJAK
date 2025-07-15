<?php
/**
 * Vista: Dashboard del Agente Inmobiliario
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$content = ob_start();
?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <h1 class="text-3xl font-bold text-primary-700 mb-4">¡Bienvenido al Panel del Agente!</h1>
        <p class="text-gray-700 mb-6">
            Aquí podrás gestionar tus propiedades, validar nuevas publicaciones, ver tus estadísticas y atender a tus clientes.
        </p>
        <div class="flex flex-col md:flex-row justify-center gap-4">
            <a href="/properties/agent/list" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-home mr-2"></i>Mis Propiedades
            </a>
            <a href="/properties/pending-validation" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-clock mr-2"></i>Pendientes de Validación
            </a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 