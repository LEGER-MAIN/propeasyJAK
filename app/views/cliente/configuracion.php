<?php
/**
 * Vista: Configuración del Cliente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista permite a los clientes configurar sus preferencias
 */
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Configuración</h1>
                    <p class="mt-2 text-gray-600">Gestiona tus preferencias y configuración de cuenta</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/cliente/dashboard" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Configuración Principal -->
            <div class="lg:col-span-2">
                <!-- Notificaciones -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Notificaciones</h2>
                        <p class="mt-1 text-sm text-gray-600">Configura cómo quieres recibir las notificaciones</p>
                    </div>
                    
                    <form action="/cliente/configuracion" method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="notificaciones_email" 
                                           name="notificaciones_email" 
                                           type="checkbox" 
                                           class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded"
                                           <?= ($config['notificaciones_email'] ?? true) ? 'checked' : '' ?>>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="notificaciones_email" class="font-medium text-gray-700">Notificaciones por Email</label>
                                    <p class="text-gray-500">Recibe notificaciones sobre nuevas propiedades, actualizaciones de solicitudes y mensajes de agentes</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="notificaciones_push" 
                                           name="notificaciones_push" 
                                           type="checkbox" 
                                           class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded"
                                           <?= ($config['notificaciones_push'] ?? true) ? 'checked' : '' ?>>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="notificaciones_push" class="font-medium text-gray-700">Notificaciones Push</label>
                                    <p class="text-gray-500">Recibe notificaciones instantáneas en tu navegador cuando estés en la plataforma</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Privacidad -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Privacidad</h2>
                        <p class="mt-1 text-sm text-gray-600">Controla la visibilidad de tu información</p>
                    </div>
                    
                    <form action="/cliente/configuracion" method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <div>
                            <label for="privacidad_perfil" class="block text-sm font-medium text-gray-700 mb-2">
                                Visibilidad del Perfil
                            </label>
                            <select id="privacidad_perfil" 
                                    name="privacidad_perfil" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="publico" <?= ($config['privacidad_perfil'] ?? 'publico') === 'publico' ? 'selected' : '' ?>>Público</option>
                                <option value="agentes" <?= ($config['privacidad_perfil'] ?? 'publico') === 'agentes' ? 'selected' : '' ?>>Solo Agentes</option>
                                <option value="privado" <?= ($config['privacidad_perfil'] ?? 'publico') === 'privado' ? 'selected' : '' ?>>Privado</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">
                                <strong>Público:</strong> Cualquier persona puede ver tu perfil<br>
                                <strong>Solo Agentes:</strong> Solo los agentes inmobiliarios pueden ver tu perfil<br>
                                <strong>Privado:</strong> Tu perfil no es visible para nadie
                            </p>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Preferencias de Búsqueda -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Preferencias de Búsqueda</h2>
                        <p class="mt-1 text-sm text-gray-600">Configura tus preferencias para encontrar propiedades más fácilmente</p>
                    </div>
                    
                    <form action="/cliente/configuracion" method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="precio_min" class="block text-sm font-medium text-gray-700 mb-2">
                                    Precio Mínimo (USD)
                                </label>
                                <input type="number" 
                                       id="precio_min" 
                                       name="precio_min" 
                                       value="<?= $config['precio_min'] ?? '' ?>" 
                                       placeholder="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="precio_max" class="block text-sm font-medium text-gray-700 mb-2">
                                    Precio Máximo (USD)
                                </label>
                                <input type="number" 
                                       id="precio_max" 
                                       name="precio_max" 
                                       value="<?= $config['precio_max'] ?? '' ?>" 
                                       placeholder="1000000"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tipo_propiedad" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Propiedad Preferido
                                </label>
                                <select id="tipo_propiedad" 
                                        name="tipo_propiedad" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Cualquier tipo</option>
                                    <option value="casa" <?= ($config['tipo_propiedad'] ?? '') === 'casa' ? 'selected' : '' ?>>Casa</option>
                                    <option value="apartamento" <?= ($config['tipo_propiedad'] ?? '') === 'apartamento' ? 'selected' : '' ?>>Apartamento</option>
                                    <option value="terreno" <?= ($config['tipo_propiedad'] ?? '') === 'terreno' ? 'selected' : '' ?>>Terreno</option>
                                    <option value="local" <?= ($config['tipo_propiedad'] ?? '') === 'local' ? 'selected' : '' ?>>Local Comercial</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="ciudad_preferida" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ciudad Preferida
                                </label>
                                <input type="text" 
                                       id="ciudad_preferida" 
                                       name="ciudad_preferida" 
                                       value="<?= htmlspecialchars($config['ciudad_preferida'] ?? '') ?>" 
                                       placeholder="Santo Domingo"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Preferencias
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
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
                    </div>
                </div>
                
                <!-- Acciones Rápidas -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="/profile" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-user mr-3 text-primary-600"></i>
                            Mi Perfil
                        </a>
                        <a href="/cliente/dashboard" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-tachometer-alt mr-3 text-primary-600"></i>
                            Dashboard
                        </a>
                        <a href="/cliente/historial" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-history mr-3 text-primary-600"></i>
                            Mi Historial
                        </a>
                        <a href="/favorites" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-heart mr-3 text-primary-600"></i>
                            Mis Favoritos
                        </a>
                        <a href="/solicitudes" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i class="fas fa-handshake mr-3 text-primary-600"></i>
                            Mis Solicitudes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 