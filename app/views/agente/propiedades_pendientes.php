<?php
// Layout principal
include APP_PATH . '/views/layouts/main.php';
?>
<div class="container mt-4">
    <h2 class="mb-4">Propiedades Pendientes de Validación</h2>
    <?php if (!empty($stats)) : ?>
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">En revisión</h5>
                    <p class="card-text fs-4"><?php echo $stats['en_revision'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Activas</h5>
                    <p class="card-text fs-4"><?php echo $stats['activas'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Rechazadas</h5>
                    <p class="card-text fs-4"><?php echo $stats['rechazadas'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Vendidas</h5>
                    <p class="card-text fs-4"><?php echo $stats['vendidas'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($propiedades)) : ?>
                <?php foreach ($propiedades as $prop) : ?>
                <tr id="row-<?php echo $prop['id']; ?>">
                    <td><?php echo $prop['id']; ?></td>
                    <td><?php echo htmlspecialchars($prop['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($prop['cliente_nombre'] . ' ' . $prop['cliente_apellido']); ?></td>
                    <td>$<?php echo number_format($prop['precio'], 2); ?></td>
                    <td><span class="badge bg-warning text-dark">En revisión</span></td>
                    <td>
                        <form class="d-inline-block aprobar-form" method="post" action="/agente/propiedades/aprobar">
                            <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                        </form>
                        <form class="d-inline-block rechazar-form" method="post" action="/agente/propiedades/rechazar">
                            <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
                            <input type="text" name="motivo" placeholder="Motivo" class="form-control form-control-sm d-inline-block w-auto" required>
                            <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                        </form>
                        <form class="d-inline-block eliminar-form" method="post" action="/agente/propiedades/eliminar" onsubmit="return confirm('¿Seguro que deseas eliminar esta propiedad?');">
                            <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6" class="text-center">No hay propiedades pendientes.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div id="alerta-crud" class="mt-3"></div>
    <h3 class="mt-5">Historial de Propiedades Aprobadas</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($aprobadas)) : ?>
                <?php foreach ($aprobadas as $prop) : ?>
                <tr id="row-aprobada-<?php echo $prop['id']; ?>">
                    <td><?php echo $prop['id']; ?></td>
                    <td><?php echo htmlspecialchars($prop['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($prop['cliente_nombre'] ?? ''); ?></td>
                    <td>$<?php echo number_format($prop['precio'], 2); ?></td>
                    <td><span class="badge bg-success">Aprobada</span></td>
                    <td>
                        <form class="d-inline-block eliminar-form" method="post" action="/agente/propiedades/eliminar" onsubmit="return confirm('¿Seguro que deseas eliminar esta propiedad?');">
                            <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6" class="text-center">No hay propiedades aprobadas.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <h3 class="mt-5">Historial de Propiedades Rechazadas</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($rechazadas)) : ?>
                <?php foreach ($rechazadas as $prop) : ?>
                <tr id="row-rechazada-<?php echo $prop['id']; ?>">
                    <td><?php echo $prop['id']; ?></td>
                    <td><?php echo htmlspecialchars($prop['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($prop['cliente_nombre'] ?? ''); ?></td>
                    <td>$<?php echo number_format($prop['precio'], 2); ?></td>
                    <td><span class="badge bg-danger">Rechazada</span></td>
                    <td>
                        <form class="d-inline-block eliminar-form" method="post" action="/agente/propiedades/eliminar" onsubmit="return confirm('¿Seguro que deseas eliminar esta propiedad?');">
                            <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6" class="text-center">No hay propiedades rechazadas.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
// Manejo AJAX mejorado para aprobar, rechazar y eliminar
function handleCrudForm(formClass, successMsg) {
    document.querySelectorAll(formClass).forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar formulario
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Obtener el botón y cambiar su estado
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            submitBtn.disabled = true;
            
            // Preparar datos
            const fd = new FormData(form);
            
            // Crear timeout para evitar que se quede colgado
            const timeout = setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                document.getElementById('alerta-crud').innerHTML = 
                    '<div class="alert alert-warning">La operación está tardando más de lo esperado. Inténtalo de nuevo.</div>';
            }, 10000); // 10 segundos
            
            // Enviar petición
            fetch(form.action, {
                method: 'POST',
                body: fd
            })
            .then(response => {
                clearTimeout(timeout);
                
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Respuesta no válida del servidor');
                }
                
                return response.json();
            })
            .then(data => {
                const alerta = document.getElementById('alerta-crud');
                
                if (data.success) {
                    alerta.innerHTML = `<div class="alert alert-success alert-dismissible fade show">
                        <strong>¡Éxito!</strong> ${successMsg}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    
                    // Ocultar fila si es aprobar/rechazar/eliminar
                    const row = form.closest('tr');
                    if (row) {
                        row.style.transition = 'opacity 0.5s';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 500);
                    }
                    
                    // Actualizar estadísticas si es necesario
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                    
                } else {
                    alerta.innerHTML = `<div class="alert alert-danger alert-dismissible fade show">
                        <strong>Error:</strong> ${data.message || 'Error desconocido'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                }
            })
            .catch(error => {
                clearTimeout(timeout);
                console.error('Error:', error);
                
                document.getElementById('alerta-crud').innerHTML = 
                    `<div class="alert alert-danger alert-dismissible fade show">
                        <strong>Error de conexión:</strong> ${error.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
            })
            .finally(() => {
                // Restaurar botón
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    handleCrudForm('.aprobar-form', 'Propiedad aprobada correctamente.');
    handleCrudForm('.rechazar-form', 'Propiedad rechazada correctamente.');
    handleCrudForm('.eliminar-form', 'Propiedad eliminada correctamente.');
    
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(() => {
        const alertas = document.querySelectorAll('.alert');
        alertas.forEach(alerta => {
            if (alerta.classList.contains('alert-success')) {
                alerta.style.transition = 'opacity 0.5s';
                alerta.style.opacity = '0';
                setTimeout(() => alerta.remove(), 500);
            }
        });
    }, 5000);
});
</script> 