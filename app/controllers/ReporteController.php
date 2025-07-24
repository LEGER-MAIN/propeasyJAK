<?php
/**
 * Controlador ReporteController
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja las operaciones relacionadas con los reportes de irregularidades
 */

require_once APP_PATH . '/models/ReporteIrregularidad.php';
require_once APP_PATH . '/helpers/EmailHelper.php';

class ReporteController {
    private $reporteModel;
    private $emailHelper;
    
    public function __construct() {
        $this->reporteModel = new ReporteIrregularidad();
        $this->emailHelper = new EmailHelper();
    }
    
    /**
     * Mostrar formulario para crear un nuevo reporte
     */
    public function crear() {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            redirect('/login');
        }
        
        $tiposReporte = $this->reporteModel->obtenerTiposReporte();
        $pageTitle = 'Reportar Irregularidad - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/reportes/crear.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Procesar la creación de un nuevo reporte
     */
    public function guardar() {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            redirect('/login');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido');
            redirect('/reportes/crear');
        }
        
        // Obtener y limpiar datos del formulario
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'tipo_reporte' => sanitizeInput($_POST['tipo_reporte'] ?? ''),
            'titulo' => sanitizeInput($_POST['titulo'] ?? ''),
            'descripcion' => sanitizeInput($_POST['descripcion'] ?? '')
        ];
        
        // Validar datos
        $errores = $this->reporteModel->validar($data);
        
        if (!empty($errores)) {
            setFlashMessage('error', implode('<br>', $errores));
            redirect('/reportes/crear');
        }
        
        // Procesar archivo adjunto si se subió
        if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
            $archivo = $this->procesarArchivoAdjunto($_FILES['archivo_adjunto']);
            if ($archivo === false) {
                setFlashMessage('error', 'Error al procesar el archivo adjunto');
                redirect('/reportes/crear');
            }
            $data['archivo_adjunto'] = $archivo;
        }
        
        // Crear el reporte
        $reporteId = $this->reporteModel->crear($data);
        
        if ($reporteId) {
            // Enviar notificación por email al administrador
            $this->enviarNotificacionReporte($reporteId);
            
            // Enviar correo de agradecimiento al usuario
            $this->enviarAgradecimientoUsuario($reporteId);
            
            setFlashMessage('success', 'Reporte enviado correctamente. Nos pondremos en contacto contigo pronto.');
            redirect('/reportes/mis-reportes');
        } else {
            setFlashMessage('error', 'Error al crear el reporte. Por favor, inténtalo de nuevo.');
            redirect('/reportes/crear');
        }
    }
    
    /**
     * Mostrar lista de reportes del usuario
     */
    public function misReportes() {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            redirect('/login');
        }
        
        $reportes = $this->reporteModel->obtenerPorUsuario($_SESSION['user_id']);
        $pageTitle = 'Mis Reportes - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/reportes/mis-reportes.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Mostrar detalles de un reporte específico
     */
    public function mostrar($id) {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            redirect('/login');
        }
        
        $reporte = $this->reporteModel->obtenerPorId($id);
        
        if (!$reporte) {
            setFlashMessage('error', 'Reporte no encontrado');
            redirect('/reportes/mis-reportes');
        }
        
        // Verificar que el usuario sea el propietario del reporte o un administrador
        if ($reporte['usuario_id'] != $_SESSION['user_id'] && !hasRole(ROLE_ADMIN)) {
            setFlashMessage('error', 'No tienes permisos para ver este reporte');
            redirect('/reportes/mis-reportes');
        }
        
        $pageTitle = 'Detalles del Reporte - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/reportes/mostrar.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Panel de administración de reportes (solo para administradores)
     */
    public function admin() {
        // Verificar que el usuario sea administrador
        if (!hasRole(ROLE_ADMIN)) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('/dashboard');
        }
        
        // Obtener filtros
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'tipo_reporte' => $_GET['tipo_reporte'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];
        
        // Obtener reportes con paginación
        $pagina = max(1, intval($_GET['pagina'] ?? 1));
        $porPagina = 20;
        $offset = ($pagina - 1) * $porPagina;
        
        $reportes = $this->reporteModel->obtenerTodos($filtros, $porPagina, $offset);
        $estadisticas = $this->reporteModel->obtenerEstadisticas();
        $tiposReporte = $this->reporteModel->obtenerTiposReporte();
        $estados = $this->reporteModel->obtenerEstados();
        
        include APP_PATH . '/views/reportes/admin.php';
    }
    
    /**
     * Actualizar estado de un reporte (solo para administradores)
     */
    public function actualizarEstado() {
        // Verificar que el usuario sea administrador
        if (!hasRole(ROLE_ADMIN)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
            return;
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            return;
        }
        
        $reporteId = intval($_POST['reporte_id'] ?? 0);
        $estado = sanitizeInput($_POST['estado'] ?? '');
        $respuesta = sanitizeInput($_POST['respuesta'] ?? '');
        
        if (!$reporteId || !in_array($estado, array_keys($this->reporteModel->obtenerEstados()))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }
        
        $resultado = $this->reporteModel->actualizarEstado(
            $reporteId, 
            $estado, 
            $respuesta, 
            $_SESSION['user_id']
        );
        
        if ($resultado) {
            // Enviar notificación al usuario si hay respuesta
            if (!empty($respuesta)) {
                $this->enviarNotificacionRespuesta($reporteId);
            }
            
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }
    
    /**
     * Eliminar un reporte (solo para administradores)
     */
    public function eliminar() {
        // Verificar que el usuario sea administrador
        if (!hasRole(ROLE_ADMIN)) {
            setFlashMessage('error', 'No tienes permisos para realizar esta acción');
            redirect('/admin/reports');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido');
            redirect('/admin/reports');
        }
        
        $reporteId = intval($_POST['reporte_id'] ?? 0);
        
        if (!$reporteId) {
            setFlashMessage('error', 'ID de reporte inválido');
            redirect('/admin/reports');
        }
        
        $resultado = $this->reporteModel->eliminar($reporteId);
        
        if ($resultado) {
            setFlashMessage('success', 'Reporte eliminado correctamente');
        } else {
            setFlashMessage('error', 'Error al eliminar el reporte');
        }
        
        redirect('/admin/reports');
    }
    
    /**
     * Reporte de citas (solo para administradores)
     */
    public function citas() {
        // Verificar que el usuario sea administrador
        if (!hasRole(ROLE_ADMIN)) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('/dashboard');
        }
        
        // Incluir la vista de reportes de citas
        include APP_PATH . '/views/reportes/citas.php';
    }
    
    /**
     * Procesar archivo adjunto
     * 
     * @param array $archivo Datos del archivo subido
     * @return string|false Nombre del archivo guardado o false si falla
     */
    private function procesarArchivoAdjunto($archivo) {
        // Verificar tipo de archivo
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            return false;
        }
        
        // Verificar tamaño
        if ($archivo['size'] > MAX_FILE_SIZE) {
            return false;
        }
        
        // Crear directorio si no existe
        $directorio = PUBLIC_PATH . '/uploads/reportes';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }
        
        // Generar nombre único
        $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
        $rutaCompleta = $directorio . '/' . $nombreArchivo;
        
        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return $nombreArchivo;
        }
        
        return false;
    }
    
    /**
     * Enviar notificación de nuevo reporte al administrador
     * 
     * @param int $reporteId ID del reporte
     */
    private function enviarNotificacionReporte($reporteId) {
        $reporte = $this->reporteModel->obtenerPorId($reporteId);
        if (!$reporte) {
            return;
        }
        
        $tiposReporte = $this->reporteModel->obtenerTiposReporte();
        $tipoReporteTexto = $tiposReporte[$reporte['tipo_reporte']] ?? 'Desconocido';
        
        $asunto = 'Nuevo Reporte de Irregularidad - ' . APP_NAME;
        $htmlBody = $this->getEmailTemplateNuevoReporte($reporte, $tipoReporteTexto);
        $textBody = $this->getEmailTextNuevoReporte($reporte, $tipoReporteTexto);
        
        // Enviar al correo de soporte
        $this->emailHelper->sendCustomEmail(
            'propeasy.soporte@gmail.com',
            $asunto,
            $htmlBody,
            $textBody,
            'Equipo de Soporte de PropEasy'
        );
    }
    
    /**
     * Enviar correo de agradecimiento al usuario
     * 
     * @param int $reporteId ID del reporte
     */
    private function enviarAgradecimientoUsuario($reporteId) {
        $reporte = $this->reporteModel->obtenerPorId($reporteId);
        if (!$reporte) {
            return;
        }
        
        $asunto = 'Gracias por tu Reporte - ' . APP_NAME;
        $htmlBody = $this->getEmailTemplateAgradecimiento($reporte);
        $textBody = $this->getEmailTextAgradecimiento($reporte);
        
        $this->emailHelper->sendCustomEmail(
            $reporte['email'],
            $asunto,
            $htmlBody,
            $textBody,
            $reporte['nombre'] . ' ' . $reporte['apellido']
        );
    }
    
    /**
     * Enviar notificación de respuesta al usuario
     * 
     * @param int $reporteId ID del reporte
     */
    private function enviarNotificacionRespuesta($reporteId) {
        $reporte = $this->reporteModel->obtenerPorId($reporteId);
        if (!$reporte) {
            return;
        }
        
        $asunto = 'Respuesta a tu Reporte - ' . APP_NAME;
        $htmlBody = $this->getEmailTemplateRespuesta($reporte);
        $textBody = $this->getEmailTextRespuesta($reporte);
        
        $this->emailHelper->sendCustomEmail(
            $reporte['email'],
            $asunto,
            $htmlBody,
            $textBody,
            $reporte['nombre'] . ' ' . $reporte['apellido']
        );
    }
    
    /**
     * Obtener template HTML para email de nuevo reporte
     */
    private function getEmailTemplateNuevoReporte($reporte, $tipoReporteTexto) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Nuevo Reporte de Irregularidad</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .reporte-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #1e40af; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nuevo Reporte de Irregularidad</h1>
                </div>
                <div class='content'>
                    <p>Se ha recibido un nuevo reporte de irregularidad en el sistema.</p>
                    
                    <div class='reporte-info'>
                        <h3>Detalles del Reporte:</h3>
                        <p><strong>ID:</strong> #{$reporte['id']}</p>
                        <p><strong>Tipo:</strong> {$tipoReporteTexto}</p>
                        <p><strong>Título:</strong> {$reporte['titulo']}</p>
                        <p><strong>Descripción:</strong> {$reporte['descripcion']}</p>
                        <p><strong>Fecha:</strong> " . date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])) . "</p>
                    </div>
                    
                    <div class='reporte-info'>
                        <h3>Información del Usuario:</h3>
                        <p><strong>Nombre:</strong> {$reporte['nombre']} {$reporte['apellido']}</p>
                        <p><strong>Email:</strong> {$reporte['email']}</p>
                        <p><strong>Teléfono:</strong> {$reporte['telefono']}</p>
                    </div>
                    
                    <p><a href='" . APP_URL . "/admin/reports' style='background: #1e40af; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ver en el Panel de Administración</a></p>
                </div>
                <div class='footer'>
                    <p>Este es un mensaje automático del sistema PropEasy</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Obtener texto plano para email de nuevo reporte
     */
    private function getEmailTextNuevoReporte($reporte, $tipoReporteTexto) {
        return "Nuevo Reporte de Irregularidad

Se ha recibido un nuevo reporte de irregularidad en el sistema.

Detalles del Reporte:
- ID: #{$reporte['id']}
- Tipo: {$tipoReporteTexto}
- Título: {$reporte['titulo']}
- Descripción: {$reporte['descripcion']}
- Fecha: " . date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])) . "

Información del Usuario:
- Nombre: {$reporte['nombre']} {$reporte['apellido']}
- Email: {$reporte['email']}
- Teléfono: {$reporte['telefono']}

Para revisar este reporte, accede al panel de administración: " . APP_URL . "/admin/reports

Este es un mensaje automático del sistema PropEasy";
    }
    
    /**
     * Obtener template HTML para email de respuesta
     */
    private function getEmailTemplateRespuesta($reporte) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Respuesta a tu Reporte</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .respuesta { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #1e40af; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Respuesta a tu Reporte</h1>
                </div>
                <div class='content'>
                    <p>Hola {$reporte['nombre']},</p>
                    
                    <p>Hemos revisado tu reporte y te enviamos la siguiente respuesta:</p>
                    
                    <div class='respuesta'>
                        <h3>Tu Reporte:</h3>
                        <p><strong>Título:</strong> {$reporte['titulo']}</p>
                        <p><strong>Estado:</strong> " . ucfirst($reporte['estado']) . "</p>
                        
                        <h3>Nuestra Respuesta:</h3>
                        <p>{$reporte['respuesta_admin']}</p>
                    </div>
                    
                    <p>Gracias por ayudarnos a mejorar nuestro servicio.</p>
                    
                    <p>Saludos,<br>El equipo de PropEasy</p>
                </div>
                <div class='footer'>
                    <p>Este es un mensaje automático del sistema PropEasy</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Obtener texto plano para email de respuesta
     */
    private function getEmailTextRespuesta($reporte) {
        return "Respuesta a tu Reporte

Hola {$reporte['nombre']},

Hemos revisado tu reporte y te enviamos la siguiente respuesta:

Tu Reporte:
- Título: {$reporte['titulo']}
- Estado: " . ucfirst($reporte['estado']) . "

Nuestra Respuesta:
{$reporte['respuesta_admin']}

Gracias por ayudarnos a mejorar nuestro servicio.

Saludos,
El equipo de PropEasy

Este es un mensaje automático del sistema PropEasy";
    }
    
    /**
     * Obtener template HTML para email de agradecimiento
     */
    private function getEmailTemplateAgradecimiento($reporte) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Gracias por tu Reporte</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .reporte-info { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #1e40af; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .footer { text-align: center; padding: 20px; color: #666; }
                .btn { display: inline-block; background: #1e40af; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
                .highlight { background: #e0f2fe; padding: 15px; border-radius: 6px; border-left: 4px solid #0284c7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>¡Gracias por tu Colaboración!</h1>
                    <p>Tu reporte ha sido recibido correctamente</p>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$reporte['nombre']}</strong>,</p>
                    
                    <p>Queremos agradecerte por tomar el tiempo de reportar esta situación. Tu colaboración es fundamental para ayudarnos a mantener la calidad y confiabilidad de nuestra plataforma.</p>
                    
                    <div class='reporte-info'>
                        <h3>Detalles de tu Reporte:</h3>
                        <p><strong>ID del Reporte:</strong> #{$reporte['id']}</p>
                        <p><strong>Título:</strong> {$reporte['titulo']}</p>
                        <p><strong>Fecha de Envío:</strong> " . date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])) . "</p>
                        <p><strong>Estado:</strong> <span style='color: #f59e0b; font-weight: bold;'>En Revisión</span></p>
                    </div>
                    
                    <div class='highlight'>
                        <h4>¿Qué sucede ahora?</h4>
                        <ul>
                            <li>Nuestro equipo de soporte revisará tu reporte detalladamente</li>
                            <li>Tomaremos las medidas necesarias para resolver la situación</li>
                            <li>Te notificaremos cuando tu reporte sea atendido</li>
                            <li>Mantendremos la confidencialidad de tu información</li>
                        </ul>
                    </div>
                    
                    <p>Si tienes información adicional que quieras agregar a tu reporte, puedes acceder a tu historial de reportes desde tu dashboard.</p>
                    
                    <p style='text-align: center;'>
                        <a href='" . APP_URL . "/reportes/mis-reportes' class='btn'>Ver Mis Reportes</a>
                    </p>
                    
                    <p>Gracias nuevamente por ayudarnos a mejorar PropEasy. Tu feedback es invaluable para nosotros.</p>
                    
                    <p>Saludos cordiales,<br>
                    <strong>El equipo de PropEasy</strong></p>
                </div>
                <div class='footer'>
                    <p>Este es un mensaje automático del sistema PropEasy</p>
                    <p>Si tienes alguna pregunta, no dudes en contactarnos</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Obtener texto plano para email de agradecimiento
     */
    private function getEmailTextAgradecimiento($reporte) {
        return "¡Gracias por tu Colaboración!

Hola {$reporte['nombre']},

Queremos agradecerte por tomar el tiempo de reportar esta situación. Tu colaboración es fundamental para ayudarnos a mantener la calidad y confiabilidad de nuestra plataforma.

Detalles de tu Reporte:
- ID del Reporte: #{$reporte['id']}
- Título: {$reporte['titulo']}
- Fecha de Envío: " . date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])) . "
- Estado: En Revisión

¿Qué sucede ahora?
- Nuestro equipo de soporte revisará tu reporte detalladamente
- Tomaremos las medidas necesarias para resolver la situación
- Te notificaremos cuando tu reporte sea atendido
- Mantendremos la confidencialidad de tu información

Si tienes información adicional que quieras agregar a tu reporte, puedes acceder a tu historial de reportes desde tu dashboard: " . APP_URL . "/reportes/mis-reportes

Gracias nuevamente por ayudarnos a mejorar PropEasy. Tu feedback es invaluable para nosotros.

Saludos cordiales,
El equipo de PropEasy

Este es un mensaje automático del sistema PropEasy";
    }
} 
