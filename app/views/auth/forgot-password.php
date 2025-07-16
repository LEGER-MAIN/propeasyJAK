<?php $pageTitle = 'Recuperar Contraseña - ' . APP_NAME; ?>
<?php ob_start(); ?>
<div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
        <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <i class="fas fa-key text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Recuperar Contraseña</h1>
            <p class="text-lg text-gray-700 text-center mt-2">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>
        
        <?php foreach(getFlashMessages() as $msg): ?>
            <div class="mb-6 text-center p-4 rounded-xl <?= $msg['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                <i class="fas <?= $msg['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= htmlspecialchars($msg['message']) ?>
            </div>
        <?php endforeach; ?>
        
        <form method="POST" action="/forgot-password" class="space-y-6">
            <div>
                <label for="email" class="block text-base font-semibold text-gray-800 mb-2">
                    <i class="fas fa-envelope text-blue-600 mr-2"></i>
                    Correo electrónico
                </label>
                <div class="relative">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="Ingresa tu email" 
                        class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-lg font-bold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
            >
                <i class="fas fa-paper-plane mr-2"></i>
                Enviar enlace de recuperación
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <a href="/login" class="text-blue-600 hover:text-blue-800 font-semibold underline text-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?> 