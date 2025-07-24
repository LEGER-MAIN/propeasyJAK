<?php $pageTitle = 'Restablecer Contraseña - ' . APP_NAME; ?>
<?php ob_start(); ?>
<div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
        <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <i class="fas fa-lock text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Restablecer Contraseña</h1>
            <p class="text-lg text-gray-700 text-center mt-2">Ingresa tu nueva contraseña y confírmala.</p>
        </div>
        
        <?php foreach(getFlashMessages() as $msg): ?>
            <div class="mb-6 text-center p-4 rounded-xl <?= $msg['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                <i class="fas <?= $msg['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= htmlspecialchars($msg['message']) ?>
            </div>
        <?php endforeach; ?>
        
        <form method="POST" action="/reset-password" class="space-y-6">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? $_POST['token'] ?? '') ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <div>
                <label for="password" class="block text-base font-semibold text-gray-800 mb-2">
                    <i class="fas fa-lock text-blue-600 mr-2"></i>
                    Nueva contraseña
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="8" 
                        placeholder="Nueva contraseña" 
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
            
            <div>
                <label for="confirm_password" class="block text-base font-semibold text-gray-800 mb-2">
                    <i class="fas fa-lock text-blue-600 mr-2"></i>
                    Confirmar contraseña
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required 
                        minlength="8" 
                        placeholder="Repite la contraseña" 
                        class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button 
                            type="button" 
                            onclick="togglePassword('confirm_password')"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none p-1"
                        >
                            <i id="confirm_password-icon" class="fas fa-eye text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-lg font-bold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
            >
                <i class="fas fa-key mr-2"></i>
                Restablecer contraseña
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

<script>
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const iconId = fieldId === 'password' ? 'password-icon' : 'confirm_password-icon';
    const passwordIcon = document.getElementById(iconId);
    
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
