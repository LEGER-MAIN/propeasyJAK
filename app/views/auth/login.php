<?php $pageTitle = 'Iniciar Sesión - ' . APP_NAME; ?>
<?php ob_start(); ?>
<div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
        <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <i class="fas fa-home text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Iniciar Sesión</h1>
            <p class="text-lg text-gray-700 text-center mt-2">Accede a tu cuenta de PropEasy</p>
        </div>
        
        <?php foreach(getFlashMessages() as $msg): ?>
            <div class="mb-6 text-center p-4 rounded-xl <?= $msg['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                <i class="fas <?= $msg['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= htmlspecialchars($msg['message']) ?>
            </div>
        <?php endforeach; ?>
        
        <form method="POST" action="/login" class="space-y-6">
            <div>
                <label for="email" class="block text-base font-semibold text-gray-800 mb-2">
                    <i class="fas fa-envelope text-blue-600 mr-2"></i>
                    Correo Electrónico
                </label>
                <div class="relative">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="tu@email.com" 
                        class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="password" class="block text-base font-semibold text-gray-800 mb-2">
                    <i class="fas fa-lock text-blue-600 mr-2"></i>
                    Contraseña
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Tu contraseña" 
                        class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button 
                            type="button" 
                            onclick="togglePassword('password')"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none p-1"
                        >
                            <i id="password-icon" class="fas fa-eye text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember_me" 
                        name="remember_me" 
                        type="checkbox" 
                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-2 border-gray-300 rounded"
                    >
                    <label for="remember_me" class="ml-3 block text-base text-gray-800 font-medium">Recordarme</label>
                </div>
                <div class="text-base">
                    <a href="/forgot-password" class="font-semibold text-blue-600 hover:text-blue-800 underline">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-lg font-bold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <p class="text-gray-700 text-lg">
                ¿No tienes una cuenta? 
                <a href="/register" class="text-blue-600 hover:text-blue-800 font-semibold underline text-lg">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash text-lg';
    } else {
        passwordInput.type = 'password';
        passwordIcon.className = 'fas fa-eye text-lg';
    }
}
</script>

<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?> 