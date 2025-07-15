<?php
// Incluir el layout principal
ob_start();
?>

<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Header del formulario -->
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-primary-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Crear Cuenta
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Únete a <?= APP_NAME ?> y encuentra tu propiedad ideal
            </p>
        </div>

        <!-- Formulario de registro -->
        <form class="mt-8 space-y-6" method="POST" action="/register" id="registerForm">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <div class="space-y-4">
                <!-- Nombre y Apellido -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">
                            Nombre *
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input 
                                id="nombre" 
                                name="nombre" 
                                type="text" 
                                required 
                                class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                                placeholder="Tu nombre"
                                value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                minlength="2"
                            >
                        </div>
                        <div id="nombre-error" class="hidden text-red-600 text-xs mt-1"></div>
                    </div>

                    <div>
                        <label for="apellido" class="block text-sm font-medium text-gray-700">
                            Apellido *
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input 
                                id="apellido" 
                                name="apellido" 
                                type="text" 
                                required 
                                class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                                placeholder="Tu apellido"
                                value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>"
                                minlength="2"
                            >
                        </div>
                        <div id="apellido-error" class="hidden text-red-600 text-xs mt-1"></div>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Correo Electrónico *
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                            placeholder="tu@email.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        >
                    </div>
                    <div id="email-error" class="hidden text-red-600 text-xs mt-1"></div>
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700">
                        Teléfono *
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input 
                            id="telefono" 
                            name="telefono" 
                            type="tel" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                            placeholder="(809) 555-0000"
                            value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                            minlength="10"
                        >
                    </div>
                    <div id="telefono-error" class="hidden text-red-600 text-xs mt-1"></div>
                </div>

                <!-- Rol -->
                <div>
                    <label for="rol" class="block text-sm font-medium text-gray-700">
                        Tipo de Usuario *
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-users text-gray-400"></i>
                        </div>
                        <select 
                            id="rol" 
                            name="rol" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                        >
                            <option value="">Selecciona tu tipo de usuario</option>
                            <option value="cliente" <?= ($_POST['rol'] ?? '') === 'cliente' ? 'selected' : '' ?>>Cliente - Busco comprar propiedades</option>
                            <option value="agente" <?= ($_POST['rol'] ?? '') === 'agente' ? 'selected' : '' ?>>Agente Inmobiliario - Vendo propiedades</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <div id="rol-error" class="hidden text-red-600 text-xs mt-1"></div>
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Contraseña *
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                            placeholder="Mínimo 8 caracteres"
                            minlength="8"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button" 
                                onclick="togglePassword('password')"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div id="password-error" class="hidden text-red-600 text-xs mt-1"></div>
                    
                    <!-- Indicador de fortaleza de contraseña -->
                    <div class="mt-2">
                        <div class="flex space-x-1">
                            <div id="strength-1" class="h-1 flex-1 bg-gray-200 rounded"></div>
                            <div id="strength-2" class="h-1 flex-1 bg-gray-200 rounded"></div>
                            <div id="strength-3" class="h-1 flex-1 bg-gray-200 rounded"></div>
                            <div id="strength-4" class="h-1 flex-1 bg-gray-200 rounded"></div>
                        </div>
                        <p id="strength-text" class="text-xs text-gray-500 mt-1">Fortaleza de la contraseña</p>
                    </div>
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Confirmar Contraseña *
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="confirm_password" 
                            name="confirm_password" 
                            type="password" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                            placeholder="Repite tu contraseña"
                            minlength="8"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button" 
                                onclick="togglePassword('confirm_password')"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <i id="confirm-password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div id="confirm-password-error" class="hidden text-red-600 text-xs mt-1"></div>
                </div>

                <!-- Términos y condiciones -->
                <div class="flex items-center">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        required
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                    >
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        Acepto los 
                        <a href="/terms" class="text-primary-600 hover:text-primary-500">términos y condiciones</a>
                        y la 
                        <a href="/privacy" class="text-primary-600 hover:text-primary-500">política de privacidad</a>
                    </label>
                </div>
            </div>

            <!-- Botón de envío -->
            <div>
                <button 
                    type="submit" 
                    id="submit-btn"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-primary-500 group-hover:text-primary-400"></i>
                    </span>
                    <span id="submit-text">Crear Cuenta</span>
                    <span id="submit-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin"></i> Creando cuenta...
                    </span>
                </button>
            </div>

            <!-- Enlaces adicionales -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes una cuenta? 
                    <a href="/login" class="font-medium text-primary-600 hover:text-primary-500">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Scripts específicos para la página de registro -->
<script>
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const iconId = fieldId === 'password' ? 'password-icon' : 'confirm-password-icon';
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
                    bar.className = 'h-1 flex-1 bg-red-500 rounded';
                } else if (strength <= 3) {
                    bar.className = 'h-1 flex-1 bg-yellow-500 rounded';
                } else {
                    bar.className = 'h-1 flex-1 bg-green-500 rounded';
                }
            } else {
                bar.className = 'h-1 flex-1 bg-gray-200 rounded';
            }
        });
        
        // Actualizar texto
        if (strength <= 2) {
            strengthText.textContent = 'Contraseña débil';
            strengthText.className = 'text-xs text-red-500 mt-1';
        } else if (strength <= 3) {
            strengthText.textContent = 'Contraseña media';
            strengthText.className = 'text-xs text-yellow-500 mt-1';
        } else {
            strengthText.textContent = 'Contraseña fuerte';
            strengthText.className = 'text-xs text-green-500 mt-1';
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
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
        document.getElementById(fieldId).classList.add('border-red-500');
    }

    function hideError(fieldId) {
        const errorDiv = document.getElementById(fieldId + '-error');
        errorDiv.classList.add('hidden');
        document.getElementById(fieldId).classList.remove('border-red-500');
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
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitLoading = document.getElementById('submit-loading');
        
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
    });

    // Animación de entrada
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.max-w-md');
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