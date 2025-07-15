<?php $pageTitle = 'Recuperar Contraseña - ' . APP_NAME; ?>
<?php ob_start(); ?>
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 py-8">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <div class="flex flex-col items-center mb-6">
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2zm0 0V7m0 4v4m0 0h4m-4 0H8"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Recuperar Contraseña</h1>
            <p class="text-gray-500 text-center mt-1">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>
        <?php foreach(getFlashMessages() as $msg): ?>
            <div class="mb-4 text-center <?= $msg['type'] === 'success' ? 'text-green-600' : 'text-red-600' ?>">
                <?= htmlspecialchars($msg['message']) ?>
            </div>
        <?php endforeach; ?>
        <form method="POST" action="/forgot-password" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" id="email" name="email" required placeholder="Ingresa tu email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 font-semibold transition-colors duration-200">Enviar enlace de recuperación</button>
        </form>
        <div class="mt-6 text-center">
            <a href="/login" class="text-blue-600 hover:underline">Volver al inicio de sesión</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?> 