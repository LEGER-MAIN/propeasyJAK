<?php
/**
 * Vista: Dashboard del Usuario
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$content = ob_start();
?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <h1 class="text-3xl font-bold text-primary-700 mb-4">¡Bienvenido a tu Panel!</h1>
        <p class="text-gray-700 mb-6">
            Aquí podrás ver tus propiedades favoritas, tus solicitudes, reportes y comunicarte con agentes.
        </p>
        <div class="flex flex-col md:flex-row justify-center gap-4">
            <a href="/properties" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-search mr-2"></i>Buscar Propiedades
            </a>
            <?php if (hasRole(ROLE_CLIENTE)): ?>
            <a href="/solicitudes" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-handshake mr-2"></i>Mis Solicitudes
            </a>
            <?php elseif (hasRole(ROLE_AGENTE)): ?>
            <a href="/solicitudes" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-handshake mr-2"></i>Ver Solicitudes
            </a>
            <a href="/properties/create" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Publicar Propiedad
            </a>
            <?php elseif (hasRole(ROLE_ADMIN)): ?>
            <a href="/reportes/admin" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-clipboard-list mr-2"></i>Administrar Reportes
            </a>
            <a href="/admin/dashboard" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-cog mr-2"></i>Panel de Administración
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 