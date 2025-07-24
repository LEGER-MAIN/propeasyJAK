<?php
/**
 * Vista: Perfil del Cliente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista permite a los clientes gestionar su perfil y ver sus actividades
 */
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mi Perfil de Cliente</h1>
                    <p class="mt-2 text-gray-600">Gestiona tu información personal y revisa tus actividades</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/dashboard" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Información del Perfil -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Información Personal</h2>
                        <p class="mt-1 text-sm text-gray-600">Actualiza tu información personal y de contacto</p>
                    </div>
                    
                    <form action="/cliente/perfil" method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <!-- Nombre y Apellido -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($user['nombre'] ?? '') ?>" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="apellido" class="block text-sm font-medium text-gray-700 mb-2">
                                    Apellido <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="apellido" 
                                       name="apellido" 
                                       value="<?= htmlspecialchars($user['apellido'] ?? '') ?>" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                                   disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                            <p class="mt-1 text-sm text-gray-500">El email no se puede cambiar por seguridad</p>
                        </div>
                        
                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="tel" 
                                   id="telefono" 
                                   name="telefono" 
                                   value="<?= htmlspecialchars($user['telefono'] ?? '') ?>" 
                                   placeholder="809-123-4567"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        <!-- Ciudad y Sector -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ciudad
                                </label>
                                <input type="text" 
                                       id="ciudad" 
                                       name="ciudad" 
                                       value="<?= htmlspecialchars($user['ciudad'] ?? '') ?>" 
                                       placeholder="Santo Domingo"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="sector" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sector
                                </label>
                                <input type="text" 
                                       id="sector" 
                                       name="sector" 
                                       value="<?= htmlspecialchars($user['sector'] ?? '') ?>" 
                                       placeholder="Invivienda"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <!-- Botón de Guardar -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Cambiar Contraseña -->
                <div class="mt-8 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Cambiar Contraseña</h2>
                        <p class="mt-1 text-sm text-gray-600">Actualiza tu contraseña para mantener tu cuenta segura</p>
                    </div>
                    
                    <form action="/cliente/perfil" method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nueva Contraseña
                                </label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       minlength="8"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">Mínimo 8 caracteres</p>
                            </div>
                            
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Contraseña
                                </label>
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       minlength="8"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-key mr-2"></i>
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-2">
                <!-- Información de la Cuenta -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Información de la Cuenta</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Estado:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($user['estado'] ?? '') === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($user['estado'] ?? 'Desconocido') ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Rol:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Cliente
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Email Verificado:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($user['email_verificado'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                <?= ($user['email_verificado'] ?? 0) ? 'Sí' : 'No' ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Miembro desde:</span>
                            <span class="text-sm text-gray-600">
                                <?= date('d/m/Y', strtotime($user['fecha_registro'] ?? 'now')) ?>
                            </span>
                        </div>
                        
                        <?php if (isset($user['ultimo_acceso'])): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Último acceso:</span>
                            <span class="text-sm text-gray-600">
                                <?= date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Estadísticas del Cliente -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Mis Estadísticas</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">
                                    <?= $stats['favoritos'] ?? 0 ?>
                                </div>
                                <div class="text-sm text-gray-600">Favoritos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">
                                    <?= $stats['solicitudes'] ?? 0 ?>
                                </div>
                                <div class="text-sm text-gray-600">Solicitudes</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones Rápidas -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="/favorites" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-heart mr-3 text-primary-600"></i>
                            Mis Favoritos
                        </a>
                        <a href="/solicitudes" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-handshake mr-3 text-primary-600"></i>
                            Mis Solicitudes
                        </a>
                        <a href="/properties" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-search mr-3 text-primary-600"></i>
                            Buscar Propiedades
                        </a>
                        <a href="/agentes" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-users mr-3 text-primary-600"></i>
                            Ver Agentes
                        </a>
                        <a href="/settings" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-cog mr-3 text-primary-600"></i>
                            Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de contraseñas
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    function validatePasswords() {
        if (passwordField.value && confirmPasswordField.value) {
            if (passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        }
    }
    
    passwordField.addEventListener('input', validatePasswords);
    confirmPasswordField.addEventListener('input', validatePasswords);
});
</script> 
