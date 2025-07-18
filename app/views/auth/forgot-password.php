<?php $pageTitle = 'Recuperar Contraseña - ' . APP_NAME; ?>
<?php ob_start(); ?>
<div class="min-h-screen flex flex-col justify-center items-center py-8" style="background: linear-gradient(135deg, var(--bg-primary) 0%, rgba(29, 53, 87, 0.05) 100%);">
    <div class="w-full max-w-lg rounded-2xl shadow-2xl p-8" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.95) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
        <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-4 shadow-lg" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
                <i class="fas fa-key text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold" style="color: var(--text-primary);">Recuperar Contraseña</h1>
            <p class="text-lg text-center mt-2" style="color: var(--text-secondary);">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>
        
        <?php foreach(getFlashMessages() as $msg): ?>
            <div class="mb-6 text-center p-4 rounded-xl" style="<?= $msg['type'] === 'success' ? 'background-color: var(--color-verde-esmeralda); color: white; border: 1px solid var(--color-verde-esmeralda);' : 'background-color: var(--color-rojo-error); color: white; border: 1px solid var(--color-rojo-error);' ?>">
                <i class="fas <?= $msg['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= htmlspecialchars($msg['message']) ?>
            </div>
        <?php endforeach; ?>
        
        <form method="POST" action="/forgot-password" class="space-y-6">
            <div>
                <label for="email" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                    <i class="fas fa-envelope mr-2" style="color: var(--color-azul-marino);"></i>
                    Correo electrónico
                </label>
                <div class="relative">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="Ingresa tu email" 
                        class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                        style="border-color: var(--color-gris-claro); color: var(--text-primary);" 
                        onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                        onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope" style="color: var(--text-secondary);"></i>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <button 
                type="submit" 
                class="w-full text-white text-lg font-bold py-4 px-6 rounded-xl focus:outline-none transition-all duration-200 shadow-lg transform"
                style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(29, 53, 87, 0.3)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)'"
            >
                <i class="fas fa-paper-plane mr-2"></i>
                Enviar enlace de recuperación
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <a href="/login" class="font-semibold underline text-lg transition-all duration-200" style="color: var(--color-azul-marino);" onmouseover="this.style.color='var(--color-azul-marino-hover)'" onmouseout="this.style.color='var(--color-azul-marino)'">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?> 