<?php
// Incluir el layout principal
ob_start();
?>

<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, var(--bg-primary) 0%, rgba(29, 53, 87, 0.05) 100%);">
    <div class="max-w-2xl mx-auto">
        <!-- Header del formulario -->
        <div class="text-center mb-8">
            <div class="mx-auto h-20 w-20 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
                <i class="fas fa-user-plus text-white text-3xl"></i>
            </div>
            <h2 class="mt-6 text-4xl font-bold" style="color: var(--text-primary);">
                Crear Cuenta
            </h2>
            <p class="mt-3 text-lg" style="color: var(--text-secondary);">
                Únete a <?= APP_NAME ?> y encuentra tu propiedad ideal
            </p>
        </div>

        <!-- Formulario de registro -->
        <div class="rounded-2xl shadow-2xl p-8" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.95) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
            <form class="space-y-6" method="POST" action="/register" id="registerForm">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                
                <div class="space-y-6">
                    <!-- Nombre y Apellido -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                                <i class="fas fa-user mr-2" style="color: var(--color-azul-marino);"></i>
                                Nombre *
                            </label>
                            <div class="relative">
                                <input 
                                    id="nombre" 
                                    name="nombre" 
                                    type="text" 
                                    required 
                                    class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                                    style="border-color: var(--color-gris-claro); color: var(--text-primary);" 
                                    onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                                    onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                                    placeholder="Tu nombre"
                                    value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                    minlength="2"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user" style="color: var(--text-secondary);"></i>
                                </div>
                            </div>
                            <div id="nombre-error" class="hidden text-sm mt-2 font-medium" style="color: var(--color-rojo-error);"></div>
                        </div>

                        <div>
                            <label for="apellido" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                                <i class="fas fa-user mr-2" style="color: var(--color-azul-marino);"></i>
                                Apellido *
                            </label>
                            <div class="relative">
                                <input 
                                    id="apellido" 
                                    name="apellido" 
                                    type="text" 
                                    required 
                                    class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                                    style="border-color: var(--color-gris-claro); color: var(--text-primary);" 
                                    onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                                    onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                                    placeholder="Tu apellido"
                                    value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>"
                                    minlength="2"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user" style="color: var(--text-secondary);"></i>
                                </div>
                            </div>
                            <div id="apellido-error" class="hidden text-sm mt-2 font-medium" style="color: var(--color-rojo-error);"></div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-envelope mr-2" style="color: var(--color-azul-marino);"></i>
                            Correo Electrónico *
                        </label>
                        <div class="relative">
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                                style="border-color: var(--color-gris-claro); color: var(--text-primary);" 
                                onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                                onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                                placeholder="tu@email.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope" style="color: var(--text-secondary);"></i>
                            </div>
                        </div>
                        <div id="email-error" class="hidden text-sm mt-2 font-medium" style="color: var(--color-rojo-error);"></div>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-phone mr-2" style="color: var(--color-azul-marino);"></i>
                            Teléfono *
                        </label>
                        <div class="relative">
                            <input 
                                id="telefono" 
                                name="telefono" 
                                type="tel" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                                style="border-color: var(--color-gris-claro); color: var(--text-primary);" 
                                onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                                onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                                placeholder="(809) 555-0000"
                                value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                                minlength="10"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone" style="color: var(--text-secondary);"></i>
                            </div>
                        </div>
                        <div id="telefono-error" class="hidden text-sm mt-2 font-medium" style="color: var(--color-rojo-error);"></div>
                    </div>

                    <!-- Rol -->
                    <div>
                        <label for="rol" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-users mr-2" style="color: var(--color-azul-marino);"></i>
                            Tipo de Usuario *
                        </label>
                        <div class="relative">
                            <select 
                                id="rol" 
                                name="rol" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200 appearance-none"
                                style="border-color: var(--color-gris-claro); color: var(--text-primary); background-color: var(--bg-light);" 
                                onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                                onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                            >
                                <option value="">Selecciona tu tipo de usuario</option>
                                <option value="cliente" <?= ($_POST['rol'] ?? '') === 'cliente' ? 'selected' : '' ?>>👤 Cliente - Busco comprar propiedades</option>
                                <option value="agente" <?= ($_POST['rol'] ?? '') === 'agente' ? 'selected' : '' ?>>🏠 Agente Inmobiliario - Vendo propiedades</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-lg" style="color: var(--text-secondary);"></i>
                            </div>
                        </div>
                        <div id="rol-error" class="hidden text-sm mt-2 font-medium" style="color: var(--color-rojo-error);"></div>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-lock mr-2" style="color: var(--color-azul-marino);"></i>
                            Contraseña *
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                                style="border-color: var(--color-gris-claro); color: var(--text-primary);" 
                                onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                                onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                                placeholder="Mínimo 8 caracteres"
                                minlength="8"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button 
                                    type="button" 
                                    onclick="togglePassword('password')"
                                    class="focus:outline-none p-1 transition-all duration-200"
                                    style="color: var(--text-secondary);"
                                    onmouseover="this.style.color='var(--color-azul-marino)'"
                                    onmouseout="this.style.color='var(--text-secondary)'"
                                >
                                    <i id="password-icon" class="fas fa-eye text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div id="password-error" class="hidden text-sm mt-2 font-medium" style="color: var(--color-rojo-error);"></div>
                        
                        <!-- Indicador de fortaleza de contraseña -->
                        <div class="mt-3">
                            <div class="flex space-x-2">
                                <div id="strength-1" class="h-2 flex-1 rounded-full transition-all duration-300" style="background-color: var(--color-gris-claro);"></div>
                                <div id="strength-2" class="h-2 flex-1 rounded-full transition-all duration-300" style="background-color: var(--color-gris-claro);"></div>
                                <div id="strength-3" class="h-2 flex-1 rounded-full transition-all duration-300" style="background-color: var(--color-gris-claro);"></div>
                                <div id="strength-4" class="h-2 flex-1 rounded-full transition-all duration-300" style="background-color: var(--color-gris-claro);"></div>
                            </div>
                            <p id="strength-text" class="text-sm mt-2 font-medium" style="color: var(--text-secondary);">Fortaleza de la contraseña</p>
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="confirm_password" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-lock mr-2" style="color: var(--color-azul-marino);"></i>
                            Confirmar Contraseña *
                        </label>
                        <div class="relative">
                            <input 
                                id="confirm_password" 
                                name="confirm_password" 
                                type="password" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                                style="border-color: var(--color-gris-claro); color: var(--text-primary);" 
                                onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" 
                                onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'"
                                placeholder="Repite tu contraseña"
                                minlength="8"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button 
                                    type="button" 
                                    onclick="togglePassword('confirm_password')"
                                    class="focus:outline-none p-1 transition-all duration-200"
                                    style="color: var(--text-secondary);"
                                    onmouseover="this.style.color='var(--color-azul-marino)'"
                                    onmouseout="this.style.color='var(--text-secondary)'"
                                >
                                    <i id="confirm_password-icon" class="fas fa-eye text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div id="confirm_password-error" class="hidden text-sm mt-2 font-medium" style="color: var(--color-rojo-error);"></div>
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="flex items-start space-x-3">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            required 
                            class="mt-1 h-5 w-5 border-2 rounded transition-all duration-200"
                            style="border-color: var(--color-gris-claro);"
                            onfocus="this.style.borderColor='var(--color-azul-marino)'"
                            onblur="this.style.borderColor='var(--color-gris-claro)'"
                        >
                        <label for="terms" class="text-sm leading-relaxed" style="color: var(--text-secondary);">
                            Acepto los 
                            <a href="/terms" class="font-semibold underline transition-all duration-200" style="color: var(--color-azul-marino);" onmouseover="this.style.color='var(--color-azul-marino-hover)'" onmouseout="this.style.color='var(--color-azul-marino)'">términos y condiciones</a> 
                            y la 
                            <a href="/privacy" class="font-semibold underline transition-all duration-200" style="color: var(--color-azul-marino);" onmouseover="this.style.color='var(--color-azul-marino-hover)'" onmouseout="this.style.color='var(--color-azul-marino)'">política de privacidad</a>
                        </label>
                    </div>

                    <!-- Botón de registro -->
                    <div class="pt-4">
                        <button 
                            type="submit" 
                            class="w-full text-white text-lg font-bold py-4 px-6 rounded-xl focus:outline-none transition-all duration-200 shadow-lg transform"
                            style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(29, 53, 87, 0.3)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)'"
                        >
                            <i class="fas fa-user-plus mr-2"></i>
                            Crear Cuenta
                        </button>
                    </div>
                </div>
            </form>

            <!-- Enlace para iniciar sesión -->
            <div class="mt-8 text-center">
                <p class="text-lg" style="color: var(--text-secondary);">
                    ¿Ya tienes una cuenta? 
                    <a href="/login" class="font-semibold underline text-lg transition-all duration-200" style="color: var(--color-azul-marino);" onmouseover="this.style.color='var(--color-azul-marino-hover)'" onmouseout="this.style.color='var(--color-azul-marino)'">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos para la página de registro -->
<script>
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const iconId = fieldId === 'password' ? 'password-icon' : 'confirm_password-icon';
        const passwordIcon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }

    // Validación de fortaleza de contraseña
    function checkPasswordStrength(password) {
        let strength = 0;
        const feedback = [];
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        // Actualizar indicadores visuales
        const strengthBars = ['strength-1', 'strength-2', 'strength-3', 'strength-4'];
        const strengthText = document.getElementById('strength-text');
        
        strengthBars.forEach((barId, index) => {
            const bar = document.getElementById(barId);
            if (index < strength) {
                if (strength <= 2) {
                    bar.className = 'h-2 flex-1 rounded-full transition-all duration-300';
                    bar.style.backgroundColor = 'var(--color-rojo-error)';
                } else if (strength <= 3) {
                    bar.className = 'h-2 flex-1 rounded-full transition-all duration-300';
                    bar.style.backgroundColor = 'var(--color-dorado-suave)';
                } else {
                    bar.className = 'h-2 flex-1 rounded-full transition-all duration-300';
                    bar.style.backgroundColor = 'var(--color-verde-esmeralda)';
                }
            } else {
                bar.className = 'h-2 flex-1 rounded-full transition-all duration-300';
                bar.style.backgroundColor = 'var(--color-gris-claro)';
            }
        });
        
        // Actualizar texto
        if (strength <= 2) {
            strengthText.textContent = 'Contraseña débil';
            strengthText.className = 'text-sm mt-2 font-medium';
            strengthText.style.color = 'var(--color-rojo-error)';
        } else if (strength <= 3) {
            strengthText.textContent = 'Contraseña media';
            strengthText.className = 'text-sm mt-2 font-medium';
            strengthText.style.color = 'var(--color-dorado-suave)';
        } else {
            strengthText.textContent = 'Contraseña fuerte';
            strengthText.className = 'text-sm mt-2 font-medium';
            strengthText.style.color = 'var(--color-verde-esmeralda)';
        }
        
        return strength;
    }

    // Validación en tiempo real
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        checkPasswordStrength(password);
        
        // Validar confirmación de contraseña
        const confirmPassword = document.getElementById('confirm_password').value;
        if (confirmPassword && password !== confirmPassword) {
            showError('confirm_password', 'Las contraseñas no coinciden');
        } else {
            hideError('confirm_password');
        }
    });

    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            showError('confirm_password', 'Las contraseñas no coinciden');
        } else {
            hideError('confirm_password');
        }
    });

    // Funciones de validación
    function showError(fieldId, message) {
        const errorDiv = document.getElementById(fieldId + '-error');
        const field = document.getElementById(fieldId);
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
        field.style.borderColor = 'var(--color-rojo-error)';
    }

    function hideError(fieldId) {
        const errorDiv = document.getElementById(fieldId + '-error');
        const field = document.getElementById(fieldId);
        errorDiv.classList.add('hidden');
        field.style.borderColor = 'var(--color-gris-claro)';
    }

    // Validación del formulario
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validar campos requeridos
        const requiredFields = ['nombre', 'apellido', 'email', 'telefono', 'rol', 'password', 'confirm_password'];
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const value = field.value.trim();
            
            if (!value) {
                showError(fieldId, 'Este campo es requerido');
                isValid = false;
            } else {
                hideError(fieldId);
            }
        });
        
        // Validaciones específicas
        const nombre = document.getElementById('nombre').value.trim();
        const apellido = document.getElementById('apellido').value.trim();
        const email = document.getElementById('email').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (nombre.length < 2) {
            showError('nombre', 'El nombre debe tener al menos 2 caracteres');
            isValid = false;
        }
        
        if (apellido.length < 2) {
            showError('apellido', 'El apellido debe tener al menos 2 caracteres');
            isValid = false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError('email', 'Ingresa un email válido');
            isValid = false;
        }
        
        if (telefono.length < 10) {
            showError('telefono', 'Ingresa un teléfono válido');
            isValid = false;
        }
        
        if (password.length < 8) {
            showError('password', 'La contraseña debe tener al menos 8 caracteres');
            isValid = false;
        }
        
        if (password !== confirmPassword) {
            showError('confirm_password', 'Las contraseñas no coinciden');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Mostrar estado de carga
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    });

    // Animación de entrada
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.max-w-2xl');
        form.classList.add('animate-fade-in');
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Estilos para campos con error */
    .border-red-500 {
        border-color: #ef4444;
    }
    
    /* Estilos para el botón de envío durante la carga */
    button[type="submit"]:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 
