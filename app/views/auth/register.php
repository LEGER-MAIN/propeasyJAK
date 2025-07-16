<?php
// Incluir el layout principal
ob_start();
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header del formulario -->
        <div class="text-center mb-8">
            <div class="mx-auto h-20 w-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-plus text-white text-3xl"></i>
            </div>
            <h2 class="mt-6 text-4xl font-bold text-gray-900">
                Crear Cuenta
            </h2>
            <p class="mt-3 text-lg text-gray-700">
                Únete a <?= APP_NAME ?> y encuentra tu propiedad ideal
            </p>
        </div>

        <!-- Formulario de registro -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
            <form class="space-y-6" method="POST" action="/register" id="registerForm">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                
                <div class="space-y-6">
                    <!-- Nombre y Apellido -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-base font-semibold text-gray-800 mb-2">
                                <i class="fas fa-user text-blue-600 mr-2"></i>
                                Nombre *
                            </label>
                            <div class="relative">
                                <input 
                                    id="nombre" 
                                    name="nombre" 
                                    type="text" 
                                    required 
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                    placeholder="Tu nombre"
                                    value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                    minlength="2"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                            </div>
                            <div id="nombre-error" class="hidden text-red-600 text-sm mt-2 font-medium"></div>
                        </div>

                        <div>
                            <label for="apellido" class="block text-base font-semibold text-gray-800 mb-2">
                                <i class="fas fa-user text-blue-600 mr-2"></i>
                                Apellido *
                            </label>
                            <div class="relative">
                                <input 
                                    id="apellido" 
                                    name="apellido" 
                                    type="text" 
                                    required 
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                    placeholder="Tu apellido"
                                    value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>"
                                    minlength="2"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                            </div>
                            <div id="apellido-error" class="hidden text-red-600 text-sm mt-2 font-medium"></div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-envelope text-blue-600 mr-2"></i>
                            Correo Electrónico *
                        </label>
                        <div class="relative">
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="tu@email.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                        </div>
                        <div id="email-error" class="hidden text-red-600 text-sm mt-2 font-medium"></div>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-phone text-blue-600 mr-2"></i>
                            Teléfono *
                        </label>
                        <div class="relative">
                            <input 
                                id="telefono" 
                                name="telefono" 
                                type="tel" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="(809) 555-0000"
                                value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                                minlength="10"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                        </div>
                        <div id="telefono-error" class="hidden text-red-600 text-sm mt-2 font-medium"></div>
                    </div>

                    <!-- Rol -->
                    <div>
                        <label for="rol" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-users text-blue-600 mr-2"></i>
                            Tipo de Usuario *
                        </label>
                        <div class="relative">
                            <select 
                                id="rol" 
                                name="rol" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white"
                            >
                                <option value="">Selecciona tu tipo de usuario</option>
                                <option value="cliente" <?= ($_POST['rol'] ?? '') === 'cliente' ? 'selected' : '' ?>>👤 Cliente - Busco comprar propiedades</option>
                                <option value="agente" <?= ($_POST['rol'] ?? '') === 'agente' ? 'selected' : '' ?>>🏠 Agente Inmobiliario - Vendo propiedades</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-lg"></i>
                            </div>
                        </div>
                        <div id="rol-error" class="hidden text-red-600 text-sm mt-2 font-medium"></div>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-lock text-blue-600 mr-2"></i>
                            Contraseña *
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Mínimo 8 caracteres"
                                minlength="8"
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
                        <div id="password-error" class="hidden text-red-600 text-sm mt-2 font-medium"></div>
                        
                        <!-- Indicador de fortaleza de contraseña -->
                        <div class="mt-3">
                            <div class="flex space-x-2">
                                <div id="strength-1" class="h-2 flex-1 bg-gray-200 rounded-full transition-all duration-300"></div>
                                <div id="strength-2" class="h-2 flex-1 bg-gray-200 rounded-full transition-all duration-300"></div>
                                <div id="strength-3" class="h-2 flex-1 bg-gray-200 rounded-full transition-all duration-300"></div>
                                <div id="strength-4" class="h-2 flex-1 bg-gray-200 rounded-full transition-all duration-300"></div>
                            </div>
                            <p id="strength-text" class="text-sm text-gray-600 mt-2 font-medium">Fortaleza de la contraseña</p>
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="confirm_password" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-lock text-blue-600 mr-2"></i>
                            Confirmar Contraseña *
                        </label>
                        <div class="relative">
                            <input 
                                id="confirm_password" 
                                name="confirm_password" 
                                type="password" 
                                required 
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                placeholder="Repite tu contraseña"
                                minlength="8"
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
                        <div id="confirm_password-error" class="hidden text-red-600 text-sm mt-2 font-medium"></div>
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="flex items-start space-x-3">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            required 
                            class="mt-1 h-5 w-5 text-blue-600 focus:ring-blue-500 border-2 border-gray-300 rounded"
                        >
                        <label for="terms" class="text-sm text-gray-700 leading-relaxed">
                            Acepto los 
                            <a href="/terms" class="text-blue-600 hover:text-blue-800 font-semibold underline">términos y condiciones</a> 
                            y la 
                            <a href="/privacy" class="text-blue-600 hover:text-blue-800 font-semibold underline">política de privacidad</a>
                        </label>
                    </div>

                    <!-- Botón de registro -->
                    <div class="pt-4">
                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-lg font-bold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                        >
                            <i class="fas fa-user-plus mr-2"></i>
                            Crear Cuenta
                        </button>
                    </div>
                </div>
            </form>

            <!-- Enlace para iniciar sesión -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 text-lg">
                    ¿Ya tienes una cuenta? 
                    <a href="/login" class="text-blue-600 hover:text-blue-800 font-semibold underline text-lg">
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
                    bar.className = 'h-2 flex-1 bg-red-500 rounded-full';
                } else if (strength <= 3) {
                    bar.className = 'h-2 flex-1 bg-yellow-500 rounded-full';
                } else {
                    bar.className = 'h-2 flex-1 bg-green-500 rounded-full';
                }
            } else {
                bar.className = 'h-2 flex-1 bg-gray-200 rounded-full';
            }
        });
        
        // Actualizar texto
        if (strength <= 2) {
            strengthText.textContent = 'Contraseña débil';
            strengthText.className = 'text-sm text-red-500 mt-2 font-medium';
        } else if (strength <= 3) {
            strengthText.textContent = 'Contraseña media';
            strengthText.className = 'text-sm text-yellow-500 mt-2 font-medium';
        } else {
            strengthText.textContent = 'Contraseña fuerte';
            strengthText.className = 'text-sm text-green-500 mt-2 font-medium';
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