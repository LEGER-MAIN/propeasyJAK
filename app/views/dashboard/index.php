<?php
/**
 * Vista: Dashboard del Usuario
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$content = ob_start();

// Obtener estadísticas según el rol del usuario
$stats = [];
$recentAppointments = [];

try {
    if (hasRole(ROLE_AGENTE)) {
        require_once APP_PATH . '/models/Appointment.php';
        require_once APP_PATH . '/models/Property.php';
        require_once APP_PATH . '/models/SolicitudCompra.php';
        
        $appointmentModel = new Appointment();
        $propertyModel = new Property();
        $solicitudModel = new SolicitudCompra();
        
        // Estadísticas de citas
        $stats = $appointmentModel->getAgentStats($_SESSION['user_id']);
        
        // Citas recientes
        $recentAppointments = $appointmentModel->getByAgent($_SESSION['user_id'], '', 5);
        
        // Estadísticas de propiedades
        $totalProperties = $propertyModel->getCountByAgent($_SESSION['user_id']);
        $activeProperties = $propertyModel->getCountByAgentAndStatus($_SESSION['user_id'], 'activa');
        
        // Estadísticas de solicitudes
        $totalSolicitudes = $solicitudModel->getCountByAgent($_SESSION['user_id']);
        $pendingSolicitudes = $solicitudModel->getCountByAgentAndStatus($_SESSION['user_id'], REQUEST_STATUS_NEW);
        
    } elseif (hasRole(ROLE_CLIENTE)) {
        require_once APP_PATH . '/models/Appointment.php';
        require_once APP_PATH . '/models/Property.php';
        
        $appointmentModel = new Appointment();
        $propertyModel = new Property();
        
        // Estadísticas de citas del cliente
        $clientAppointments = $appointmentModel->getByClient($_SESSION['user_id']);
        $stats = [
            'total_citas' => count($clientAppointments),
            'propuestas' => 0,
            'aceptadas' => 0,
            'rechazadas' => 0,
            'realizadas' => 0,
            'proximas' => 0
        ];
        
        foreach ($clientAppointments as $appointment) {
            switch ($appointment['estado']) {
                case 'propuesta':
                    $stats['propuestas']++;
                    break;
                case 'aceptada':
                    $stats['aceptadas']++;
                    if (strtotime($appointment['fecha_cita']) > time()) {
                        $stats['proximas']++;
                    }
                    break;
                case 'rechazada':
                    $stats['rechazadas']++;
                    break;
                case 'completada':
                    $stats['realizadas']++;
                    break;
            }
        }
        
        // Citas recientes
        $recentAppointments = array_slice($clientAppointments, 0, 5);
        
        // Propiedades favoritas
        require_once APP_PATH . '/models/Favorite.php';
        $favoriteModel = new Favorite();
        $favoriteProperties = $favoriteModel->getFavoritosUsuario($_SESSION['user_id'], 5);
        
    } elseif (hasRole(ROLE_ADMIN)) {
        require_once APP_PATH . '/models/Appointment.php';
        require_once APP_PATH . '/models/Property.php';
        require_once APP_PATH . '/models/User.php';
        require_once APP_PATH . '/models/SolicitudCompra.php';
        
        $appointmentModel = new Appointment();
        $propertyModel = new Property();
        $userModel = new User();
        $solicitudModel = new SolicitudCompra();
        
        // Estadísticas generales del sistema
        $stats = [
            'total_usuarios' => $userModel->getTotalCount(),
            'total_agentes' => $userModel->getCountByRole(ROLE_AGENTE),
            'total_clientes' => $userModel->getCountByRole(ROLE_CLIENTE),
            'total_propiedades' => $propertyModel->getTotalCount(),
            'total_citas' => $appointmentModel->getTotalCount(),
            'total_solicitudes' => $solicitudModel->getTotalCount()
        ];
        
        // Citas recientes del sistema
        $recentAppointments = $appointmentModel->getRecent(10);
    }
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas del dashboard: " . $e->getMessage());
    $stats = [];
    $recentAppointments = [];
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header del Dashboard -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-2">Bienvenido a tu panel de control</p>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <?php if (hasRole(ROLE_AGENTE)): ?>
            <!-- Estadísticas para Agentes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Citas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_citas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Citas Aceptadas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['aceptadas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Próximas Citas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['proximas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Propiedades Activas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $activeProperties ?? 0 ?></p>
                    </div>
                </div>
            </div>

        <?php elseif (hasRole(ROLE_CLIENTE)): ?>
            <!-- Estadísticas para Clientes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Mis Citas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_citas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Citas Confirmadas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['aceptadas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Próximas Citas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['proximas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Favoritos</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= count($favoriteProperties ?? []) ?></p>
                    </div>
                </div>
            </div>

        <?php elseif (hasRole(ROLE_ADMIN)): ?>
            <!-- Estadísticas para Administradores -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_usuarios'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Propiedades</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_propiedades'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Citas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_citas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Solicitudes</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_solicitudes'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenido Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Citas Recientes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Citas Recientes</h3>
            </div>
            <div class="p-6">
                <?php if (!empty($recentAppointments)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recentAppointments as $appointment): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-medium">
                                                <?= strtoupper(substr($appointment['cliente_nombre'] ?? 'C', 0, 1)) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($appointment['cliente_nombre'] ?? 'Cliente') ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <?= date('d/m/Y H:i', strtotime($appointment['fecha_cita'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                               <?= $appointment['estado'] === 'aceptada' ? 'bg-green-100 text-green-800' : 
                                                  ($appointment['estado'] === 'propuesta' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-gray-100 text-gray-800') ?>">
                                        <?= ucfirst($appointment['estado']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4">
                        <a href="/appointments" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Ver todas las citas →
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay citas recientes</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza creando tu primera cita.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Acciones Rápidas</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <?php if (hasRole(ROLE_AGENTE)): ?>
                        <a href="/appointments/create" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Crear Nueva Cita</p>
                                <p class="text-sm text-gray-500">Programar una cita con un cliente</p>
                            </div>
                        </a>

                        <a href="/properties/create" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Publicar Propiedad</p>
                                <p class="text-sm text-gray-500">Agregar una nueva propiedad al catálogo</p>
                            </div>
                        </a>

                        <a href="/solicitudes" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Ver Solicitudes</p>
                                <p class="text-sm text-gray-500">Revisar solicitudes de compra pendientes</p>
                            </div>
                        </a>

                    <?php elseif (hasRole(ROLE_CLIENTE)): ?>
                        <a href="/properties" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Buscar Propiedades</p>
                                <p class="text-sm text-gray-500">Explorar el catálogo de propiedades</p>
                            </div>
                        </a>

                        <a href="/favorites" class="flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Mis Favoritos</p>
                                <p class="text-sm text-gray-500">Ver propiedades guardadas</p>
                            </div>
                        </a>

                        <a href="/solicitudes" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Mis Solicitudes</p>
                                <p class="text-sm text-gray-500">Ver el estado de mis solicitudes</p>
                            </div>
                        </a>

            <?php elseif (hasRole(ROLE_ADMIN)): ?>
                        <a href="/admin/dashboard" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Panel de Administración</p>
                                <p class="text-sm text-gray-500">Gestionar usuarios y configuraciones</p>
                            </div>
                        </a>

                        <a href="/admin/reports" class="flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Gestionar Reportes</p>
                                <p class="text-sm text-gray-500">Revisar reportes de irregularidades</p>
                            </div>
                        </a>

                        <a href="/properties" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Ver Propiedades</p>
                                <p class="text-sm text-gray-500">Explorar el catálogo completo</p>
                            </div>
            </a>
            <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 