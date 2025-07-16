<?php
/**
 * Vista de Edición del Perfil Público del Agente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */
?>

<?php include APP_PATH . '/views/layouts/main.php'; ?>

<style>
.editar-perfil {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.editar-perfil h1 {
    text-align: center;
    margin-bottom: 0;
}

.formulario-perfil {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

.seccion-formulario {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.seccion-formulario:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.seccion-titulo {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.seccion-titulo i {
    margin-right: 0.5rem;
    color: #667eea;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

.textarea {
    min-height: 120px;
    resize: vertical;
}

.especialidades-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.especialidad-tag {
    background: #667eea;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.especialidad-tag .remove {
    cursor: pointer;
    font-weight: bold;
}

.especialidad-tag .remove:hover {
    opacity: 0.8;
}

.agregar-especialidad {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.agregar-especialidad input {
    flex: 1;
}

.btn-agregar {
    background: #28a745;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
}

.btn-agregar:hover {
    background: #218838;
}

.foto-perfil-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #667eea;
    margin-bottom: 1rem;
}

.foto-perfil-default {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 4px solid #667eea;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.redes-sociales-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.red-social-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.red-social-item i {
    width: 20px;
    color: #667eea;
}

.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #667eea;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.btn-guardar {
    background: #667eea;
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
    width: 100%;
}

.btn-guardar:hover {
    background: #5a6fd8;
}

.btn-guardar:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .formulario-perfil {
        padding: 1rem;
    }
    
    .redes-sociales-container {
        grid-template-columns: 1fr;
    }
    
    .agregar-especialidad {
        flex-direction: column;
    }
}
</style>

<div class="editar-perfil">
    <div class="container">
        <h1>Editar Perfil Público</h1>
    </div>
</div>

<div class="container">
    <div class="formulario-perfil">
        <form id="formPerfilPublico" enctype="multipart/form-data">
            <!-- Información Personal -->
            <div class="seccion-formulario">
                <h2 class="seccion-titulo">
                    <i class="fas fa-user"></i>
                    Información Personal
                </h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Foto de Perfil</label>
                            <input type="file" class="form-control" name="foto_perfil" accept="image/*" id="fotoPerfil">
                            <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Máximo 5MB.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="previewFoto">
                            <?php if ($agente['foto_perfil']): ?>
                                <img src="<?= htmlspecialchars($agente['foto_perfil']) ?>" alt="Foto actual" class="foto-perfil-preview">
                            <?php else: ?>
                                <div class="foto-perfil-default">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Biografía</label>
                    <textarea class="form-control textarea" name="biografia" placeholder="Cuéntanos sobre tu experiencia y especialización en el sector inmobiliario..."><?= htmlspecialchars($agente['biografia'] ?? '') ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Años de Experiencia</label>
                            <input type="number" class="form-control" name="experiencia_anos" value="<?= $agente['experiencia_anos'] ?? '' ?>" min="0" max="50">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Licencia Inmobiliaria</label>
                            <input type="text" class="form-control" name="licencia_inmobiliaria" value="<?= htmlspecialchars($agente['licencia_inmobiliaria'] ?? '') ?>" placeholder="Número de licencia">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Especialidades -->
            <div class="seccion-formulario">
                <h2 class="seccion-titulo">
                    <i class="fas fa-star"></i>
                    Especialidades
                </h2>
                
                <div id="especialidadesContainer" class="especialidades-container">
                    <?php 
                    $especialidades = [];
                    if ($agente['especialidades']) {
                        $especialidades = explode(',', $agente['especialidades']);
                        $especialidades = array_map('trim', $especialidades);
                    }
                    foreach ($especialidades as $especialidad): ?>
                        <span class="especialidad-tag">
                            <?= htmlspecialchars($especialidad) ?>
                            <span class="remove" onclick="removeEspecialidad(this)">×</span>
                        </span>
                    <?php endforeach; ?>
                </div>
                
                <div class="agregar-especialidad">
                    <input type="text" class="form-control" id="nuevaEspecialidad" placeholder="Agregar especialidad...">
                    <button type="button" class="btn-agregar" onclick="agregarEspecialidad()">Agregar</button>
                </div>
            </div>
            
            <!-- Idiomas -->
            <div class="seccion-formulario">
                <h2 class="seccion-titulo">
                    <i class="fas fa-language"></i>
                    Idiomas
                </h2>
                
                <div id="idiomasContainer" class="especialidades-container">
                    <?php 
                    $idiomas = [];
                    if ($agente['idiomas']) {
                        $idiomas = explode(',', $agente['idiomas']);
                        $idiomas = array_map('trim', $idiomas);
                    }
                    foreach ($idiomas as $idioma): ?>
                        <span class="especialidad-tag">
                            <?= htmlspecialchars($idioma) ?>
                            <span class="remove" onclick="removeIdioma(this)">×</span>
                        </span>
                    <?php endforeach; ?>
                </div>
                
                <div class="agregar-especialidad">
                    <input type="text" class="form-control" id="nuevoIdioma" placeholder="Agregar idioma...">
                    <button type="button" class="btn-agregar" onclick="agregarIdioma()">Agregar</button>
                </div>
            </div>
            
            <!-- Horario de Disponibilidad -->
            <div class="seccion-formulario">
                <h2 class="seccion-titulo">
                    <i class="fas fa-calendar-alt"></i>
                    Horario de Disponibilidad
                </h2>
                
                <div class="form-group">
                    <label class="form-label">Horarios de Atención</label>
                    <textarea class="form-control textarea" name="horario_disponibilidad" placeholder="Ej: Lunes a Viernes: 9:00 AM - 6:00 PM&#10;Sábados: 9:00 AM - 2:00 PM&#10;Domingos: Cerrado"><?= htmlspecialchars($agente['horario_disponibilidad'] ?? '') ?></textarea>
                </div>
            </div>
            
            <!-- Redes Sociales -->
            <div class="seccion-formulario">
                <h2 class="seccion-titulo">
                    <i class="fas fa-share-alt"></i>
                    Redes Sociales
                </h2>
                
                <div class="redes-sociales-container">
                    <div class="red-social-item">
                        <i class="fab fa-facebook"></i>
                        <input type="url" class="form-control" name="redes_sociales[facebook]" value="<?= htmlspecialchars($agente['redes_sociales']['facebook'] ?? '') ?>" placeholder="URL de Facebook">
                    </div>
                    <div class="red-social-item">
                        <i class="fab fa-twitter"></i>
                        <input type="url" class="form-control" name="redes_sociales[twitter]" value="<?= htmlspecialchars($agente['redes_sociales']['twitter'] ?? '') ?>" placeholder="URL de Twitter">
                    </div>
                    <div class="red-social-item">
                        <i class="fab fa-linkedin"></i>
                        <input type="url" class="form-control" name="redes_sociales[linkedin]" value="<?= htmlspecialchars($agente['redes_sociales']['linkedin'] ?? '') ?>" placeholder="URL de LinkedIn">
                    </div>
                    <div class="red-social-item">
                        <i class="fab fa-instagram"></i>
                        <input type="url" class="form-control" name="redes_sociales[instagram]" value="<?= htmlspecialchars($agente['redes_sociales']['instagram'] ?? '') ?>" placeholder="URL de Instagram">
                    </div>
                    <div class="red-social-item">
                        <i class="fab fa-youtube"></i>
                        <input type="url" class="form-control" name="redes_sociales[youtube]" value="<?= htmlspecialchars($agente['redes_sociales']['youtube'] ?? '') ?>" placeholder="URL de YouTube">
                    </div>
                    <div class="red-social-item">
                        <i class="fab fa-whatsapp"></i>
                        <input type="url" class="form-control" name="redes_sociales[whatsapp]" value="<?= htmlspecialchars($agente['redes_sociales']['whatsapp'] ?? '') ?>" placeholder="URL de WhatsApp">
                    </div>
                </div>
            </div>
            
            <!-- Configuración del Perfil -->
            <div class="seccion-formulario">
                <h2 class="seccion-titulo">
                    <i class="fas fa-cog"></i>
                    Configuración del Perfil
                </h2>
                
                <div class="form-group">
                    <label class="form-label d-flex align-items-center">
                        <span class="switch">
                            <input type="checkbox" name="perfil_publico_activo" <?= ($agente['perfil_publico_activo'] ?? true) ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </span>
                        <span style="margin-left: 1rem;">Perfil público activo</span>
                    </label>
                    <small class="form-text text-muted">Cuando está activo, tu perfil es visible para todos los usuarios de la plataforma.</small>
                </div>
            </div>
            
            <!-- Botones -->
            <div class="form-group">
                <button type="submit" class="btn-guardar" id="btnGuardar">
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Variables globales para especialidades e idiomas
let especialidades = <?= json_encode($especialidades) ?>;
let idiomas = <?= json_encode($idiomas) ?>;

// Preview de foto de perfil
document.getElementById('fotoPerfil').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewFoto');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="foto-perfil-preview">`;
        };
        reader.readAsDataURL(file);
    }
});

// Funciones para especialidades
function agregarEspecialidad() {
    const input = document.getElementById('nuevaEspecialidad');
    const especialidad = input.value.trim();
    
    if (especialidad && !especialidades.includes(especialidad)) {
        especialidades.push(especialidad);
        actualizarEspecialidades();
        input.value = '';
    }
}

function removeEspecialidad(element) {
    const especialidad = element.previousSibling.textContent.trim();
    especialidades = especialidades.filter(e => e !== especialidad);
    actualizarEspecialidades();
}

function actualizarEspecialidades() {
    const container = document.getElementById('especialidadesContainer');
    container.innerHTML = '';
    
    especialidades.forEach(especialidad => {
        const tag = document.createElement('span');
        tag.className = 'especialidad-tag';
        tag.innerHTML = `
            ${especialidad}
            <span class="remove" onclick="removeEspecialidad(this)">×</span>
        `;
        container.appendChild(tag);
    });
}

// Funciones para idiomas
function agregarIdioma() {
    const input = document.getElementById('nuevoIdioma');
    const idioma = input.value.trim();
    
    if (idioma && !idiomas.includes(idioma)) {
        idiomas.push(idioma);
        actualizarIdiomas();
        input.value = '';
    }
}

function removeIdioma(element) {
    const idioma = element.previousSibling.textContent.trim();
    idiomas = idiomas.filter(i => i !== idioma);
    actualizarIdiomas();
}

function actualizarIdiomas() {
    const container = document.getElementById('idiomasContainer');
    container.innerHTML = '';
    
    idiomas.forEach(idioma => {
        const tag = document.createElement('span');
        tag.className = 'especialidad-tag';
        tag.innerHTML = `
            ${idioma}
            <span class="remove" onclick="removeIdioma(this)">×</span>
        `;
        container.appendChild(tag);
    });
}

// Envío del formulario
document.getElementById('formPerfilPublico').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnGuardar = document.getElementById('btnGuardar');
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    
    // Crear FormData
    const formData = new FormData(this);
    
    // Agregar especialidades e idiomas como arrays
    formData.delete('especialidades[]');
    formData.delete('idiomas[]');
    
    especialidades.forEach(especialidad => {
        formData.append('especialidades[]', especialidad);
    });
    
    idiomas.forEach(idioma => {
        formData.append('idiomas[]', idioma);
    });
    
    // Enviar formulario
    fetch('/agente/perfil/actualizar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Perfil actualizado exitosamente', 'success');
            setTimeout(() => {
                window.location.href = '/agente/perfil';
            }, 2000);
        } else {
            mostrarAlerta(data.message || 'Error al actualizar el perfil', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error de conexión. Inténtalo de nuevo.', 'danger');
    })
    .finally(() => {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
    });
});

function mostrarAlerta(mensaje, tipo) {
    // Remover alertas existentes
    const alertasExistentes = document.querySelectorAll('.alert');
    alertasExistentes.forEach(alerta => alerta.remove());
    
    // Crear nueva alerta
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo}`;
    alerta.textContent = mensaje;
    
    // Insertar al inicio del formulario
    const formulario = document.querySelector('.formulario-perfil');
    formulario.insertBefore(alerta, formulario.firstChild);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}

// Enter para agregar especialidades e idiomas
document.getElementById('nuevaEspecialidad').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        agregarEspecialidad();
    }
});

document.getElementById('nuevoIdioma').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        agregarIdioma();
    }
});
</script> 