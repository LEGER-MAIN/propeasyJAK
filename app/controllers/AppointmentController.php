<?php
/**
 * Controlador AppointmentController
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con las citas
 * entre agentes y clientes para visitas de propiedades.
 */

// Incluir modelos necesarios
require_once APP_PATH . '/models/Appointment.php';
require_once APP_PATH . '/models/SolicitudCompra.php';
require_once APP_PATH . '/models/Property.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/helpers/EmailHelper.php';

class AppointmentController {
    private $appointmentModel;
    private $solicitudModel;
    private $propertyModel;
    private $userModel;
    private $emailHelper;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->solicitudModel = new SolicitudCompra();
        $this->propertyModel = new Property();
        $this->userModel = new User();
        $this->emailHelper = new EmailHelper();
    }
    
    /**
     * Renderizar vista con layout
     */
    private function render($view, $data = []) {
        // Verificar que el archivo de vista existe
        $viewFile = APP_PATH . '/views/appointments/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("Archivo de vista no encontrado: " . $view);
        }
        
        // Verificar que el archivo de layout existe
        $layoutFile = APP_PATH . '/views/layouts/main.php';
        if (!file_exists($layoutFile)) {
            throw new Exception("Archivo de layout no encontrado");
        }
        
        // Extraer variables del array de datos
        extract($data);
        
        // Capturar el contenido de la vista
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        
        // Verificar que el contenido se capturó
        if (empty($content)) {
            $content = "<div style='background: yellow; color: black; padding: 20px; margin: 20px;'><h1>ERROR: Contenido vacío</h1><p>Vista: {$view}</p></div>";
        }
        
        // Incluir el layout
        include $layoutFile;
    }
    
    /**
     * Mostrar lista de citas del agente
     */
    public function index() {
        // Verificar autenticación
        if (!isAuthenticated()) {
            redirect('/login');
            return;
        }

        $user = $_SESSION['user'] ?? [];
        $appointments = [];

        // Obtener filtros
        $estado = $_GET['estado'] ?? '';
        $fecha = $_GET['fecha'] ?? '';
        
        try {
            if (hasRole(ROLE_AGENTE)) {
                $citas = $this->appointmentModel->getByAgent($_SESSION['user_id'], $estado);
                $stats = $this->appointmentModel->getAgentStats($_SESSION['user_id']);
            } else {
                $citas = $this->appointmentModel->getByClient($_SESSION['user_id'], $estado);
                $stats = [
                    'total_citas' => count($citas),
                    'propuestas' => 0,
                    'aceptadas' => 0,
                    'rechazadas' => 0,
                    'realizadas' => 0,
                    'proximas' => 0
                ];
            }
        } catch (Exception $e) {
            // En caso de error de base de datos, usar arrays vacíos
            error_log("Error en index(): " . $e->getMessage());
            $citas = [];
            $stats = [
                'total_citas' => 0,
                'propuestas' => 0,
                'aceptadas' => 0,
                'rechazadas' => 0,
                'realizadas' => 0,
                'proximas' => 0
            ];
        }

        $pageTitle = 'Agenda de Citas - ' . APP_NAME;
        
        // Renderizar con layout
        $this->render('index', [
            'pageTitle' => $pageTitle,
            'citas' => $citas,
            'stats' => $stats,
            'user' => $user
        ]);
    }



    /**
     * Mostrar formulario para crear nueva cita
     */
    public function create() {
        // Verificar que el usuario esté autenticado y sea agente
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Obtener solicitud ID si se proporciona
        $solicitudId = (int)($_GET['solicitud_id'] ?? 0);
        
        // Si se proporciona solicitud ID, obtener datos de la solicitud
        $solicitud = null;
        $propiedad = null;
        $cliente = null;
        
        try {
            if ($solicitudId) {
                $solicitud = $this->solicitudModel->getById($solicitudId);
                if ($solicitud && $solicitud['agente_id'] == $_SESSION['user_id']) {
                    $propiedad = $this->propertyModel->getById($solicitud['propiedad_id']);
                    $cliente = $this->userModel->getById($solicitud['cliente_id']);
                }
            }
            
            // Obtener solicitudes pendientes del agente
            $solicitudes = $this->solicitudModel->obtenerPorEstado(REQUEST_STATUS_NEW, 50, 0);
            
            // Filtrar solo las del agente actual
            $solicitudes = array_filter($solicitudes, function($solicitud) {
                return $solicitud['agente_id'] == $_SESSION['user_id'];
            });
        } catch (Exception $e) {
            // En caso de error de base de datos, usar arrays vacíos
            error_log("Error en create(): " . $e->getMessage());
            $solicitudes = [];
            $solicitud = null;
            $propiedad = null;
            $cliente = null;
        }
        
        $pageTitle = 'Crear Nueva Cita - ' . APP_NAME;
        
        // Debug: verificar que las variables estén disponibles
        if (empty($solicitudes)) {
            $solicitudes = [];
        }
        
        // Renderizar con layout
        $this->render('create', [
            'pageTitle' => $pageTitle,
            'solicitudes' => $solicitudes,
            'solicitud' => $solicitud,
            'propiedad' => $propiedad,
            'cliente' => $cliente
        ]);
    }
    
    /**
     * Crear una nueva cita
     */
    public function store() {
        // Verificar que el usuario esté autenticado y sea agente
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/appointments');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/appointments');
        }
        
        // Validar datos de entrada
        $solicitudId = (int)($_POST['solicitud_id'] ?? 0);
        $fechaCita = $_POST['fecha_cita'] ?? '';
        $horaCita = $_POST['hora_cita'] ?? '';
        $lugar = sanitizeInput($_POST['lugar'] ?? '');
        $tipoCita = $_POST['tipo_cita'] ?? '';
        $observaciones = sanitizeInput($_POST['observaciones'] ?? '');
        
        // Validar solicitud
        if (!$solicitudId) {
            setFlashMessage('error', 'Solicitud es requerida.');
            redirect('/appointments/create');
        }
        
        $solicitud = $this->solicitudModel->getById($solicitudId);
        if (!$solicitud || $solicitud['agente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Solicitud no válida.');
            redirect('/appointments/create');
        }
        
        // Validar fecha y hora
        if (empty($fechaCita) || empty($horaCita)) {
            setFlashMessage('error', 'Fecha y hora son requeridas.');
            redirect('/appointments/create');
        }
        
        $fechaHora = $fechaCita . ' ' . $horaCita . ':00';
        $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fechaHora);
        if (!$fecha || $fecha < new DateTime()) {
            setFlashMessage('error', 'La fecha y hora deben ser futuras.');
            redirect('/appointments/create');
        }
        
        // Validar otros campos
        if (empty($lugar)) {
            setFlashMessage('error', 'Lugar es requerido.');
            redirect('/appointments/create');
        }
        
        if (empty($tipoCita)) {
            setFlashMessage('error', 'Tipo de cita es requerido.');
            redirect('/appointments/create');
        }
        
        // Preparar datos de la cita
        $appointmentData = [
            'solicitud_id' => $solicitudId,
            'agente_id' => $_SESSION['user_id'],
            'cliente_id' => $solicitud['cliente_id'],
            'propiedad_id' => $solicitud['propiedad_id'],
            'fecha_cita' => $fechaHora,
            'lugar' => $lugar,
            'tipo_cita' => $tipoCita,
            'observaciones' => $observaciones
        ];
        
                    // Crear la cita
            $appointmentId = $this->appointmentModel->create($appointmentData);
        
        if ($appointmentId) {
            // Actualizar estado de la solicitud
            $this->solicitudModel->updateStatus($solicitudId, REQUEST_STATUS_MEETING);
            
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($appointmentId, 'created');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            setFlashMessage('success', 'Cita creada exitosamente. Se ha enviado una notificación al cliente.');
            redirect('/appointments/' . $appointmentId);
        } else {
            setFlashMessage('error', 'Error al crear la cita. Verifica que el horario esté disponible.');
            redirect('/appointments/create');
        }
    }
    
    /**
     * Mostrar detalles de una cita específica
     * 
     * @param int $id ID de la cita
     */
    public function show($id) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar permisos (agente o cliente de la cita)
        if ($cita['agente_id'] != $_SESSION['user_id'] && $cita['cliente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'No tienes permisos para ver esta cita.');
            redirect('/appointments');
        }
        
        $pageTitle = 'Detalles de Cita - ' . APP_NAME;
        
        // Renderizar con layout
        $this->render('show', [
            'pageTitle' => $pageTitle,
            'cita' => $cita
        ]);
    }
    
    /**
     * Mostrar formulario para editar cita
     * 
     * @param int $id ID de la cita
     */
    public function edit($id) {
        // Verificar que el usuario esté autenticado y sea agente
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita || $cita['agente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Cita no encontrada o no tienes permisos.');
            redirect('/appointments');
        }
        
        // Verificar que la cita no esté completada o cancelada
        if (in_array($cita['estado'], [Appointment::STATUS_COMPLETED, Appointment::STATUS_CANCELLED])) {
            setFlashMessage('error', 'No se puede editar una cita completada o cancelada.');
            redirect('/appointments/' . $id);
        }
        
        $pageTitle = 'Editar Cita - ' . APP_NAME;
        
        // Renderizar con layout
        $this->render('edit', [
            'pageTitle' => $pageTitle,
            'cita' => $cita
        ]);
    }
    
    /**
     * Actualizar una cita
     * 
     * @param int $id ID de la cita
     */
    public function update($id) {
        // Verificar que el usuario esté autenticado y sea agente
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/appointments');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/appointments');
        }
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita || $cita['agente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Cita no encontrada o no tienes permisos.');
            redirect('/appointments');
        }
        
        // Verificar que la cita no esté completada o cancelada
        if (in_array($cita['estado'], [Appointment::STATUS_COMPLETED, Appointment::STATUS_CANCELLED])) {
            setFlashMessage('error', 'No se puede editar una cita completada o cancelada.');
            redirect('/appointments/' . $id);
        }
        
        // Validar datos de entrada
        $fechaCita = $_POST['fecha_cita'] ?? '';
        $horaCita = $_POST['hora_cita'] ?? '';
        $lugar = sanitizeInput($_POST['lugar'] ?? '');
        $tipoCita = $_POST['tipo_cita'] ?? '';
        $observaciones = sanitizeInput($_POST['observaciones'] ?? '');
        
        // Validar fecha y hora
        if (empty($fechaCita) || empty($horaCita)) {
            setFlashMessage('error', 'Fecha y hora son requeridas.');
            redirect('/appointments/edit/' . $id);
        }
        
        $fechaHora = $fechaCita . ' ' . $horaCita . ':00';
        $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fechaHora);
        if (!$fecha || $fecha < new DateTime()) {
            setFlashMessage('error', 'La fecha y hora deben ser futuras.');
            redirect('/appointments/edit/' . $id);
        }
        
        // Validar otros campos
        if (empty($lugar)) {
            setFlashMessage('error', 'Lugar es requerido.');
            redirect('/appointments/edit/' . $id);
        }
        
        if (empty($tipoCita)) {
            setFlashMessage('error', 'Tipo de cita es requerido.');
            redirect('/appointments/edit/' . $id);
        }
        
        // Preparar datos para actualización
        $updateData = [
            'fecha_cita' => $fechaHora,
            'lugar' => $lugar,
            'tipo_cita' => $tipoCita,
            'observaciones' => $observaciones
        ];
        
        // Actualizar la cita
        if ($this->appointmentModel->update($id, $updateData)) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'updated');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            setFlashMessage('success', 'Cita actualizada exitosamente. Se ha enviado una notificación al cliente.');
            redirect('/appointments/' . $id);
        } else {
            setFlashMessage('error', 'Error al actualizar la cita. Verifica que el horario esté disponible.');
            redirect('/appointments/edit/' . $id);
        }
    }
    
    /**
     * Aceptar una cita (solo clientes)
     * 
     * @param int $id ID de la cita
     */
    public function accept($id) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar que el usuario sea el cliente de la cita
        if ($cita['cliente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'No tienes permisos para aceptar esta cita.');
            redirect('/appointments');
        }
        
        // Verificar que la cita esté en estado propuesta
        if ($cita['estado'] !== Appointment::STATUS_PROPOSED) {
            setFlashMessage('error', 'Solo se pueden aceptar citas propuestas.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita
        if ($this->appointmentModel->updateStatus($id, Appointment::STATUS_ACCEPTED)) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'accepted');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            setFlashMessage('success', 'Cita aceptada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            setFlashMessage('error', 'Error al aceptar la cita.');
            redirect('/appointments/' . $id);
        }
    }
    
    /**
     * Rechazar una cita (solo clientes)
     * 
     * @param int $id ID de la cita
     */
    public function reject($id) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar que el usuario sea el cliente de la cita
        if ($cita['cliente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'No tienes permisos para rechazar esta cita.');
            redirect('/appointments');
        }
        
        // Verificar que la cita esté en estado propuesta
        if ($cita['estado'] !== Appointment::STATUS_PROPOSED) {
            setFlashMessage('error', 'Solo se pueden rechazar citas propuestas.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita
        if ($this->appointmentModel->updateStatus($id, Appointment::STATUS_REJECTED)) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'rejected');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            setFlashMessage('success', 'Cita rechazada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            setFlashMessage('error', 'Error al rechazar la cita.');
            redirect('/appointments/' . $id);
        }
    }
    
    /**
     * Cancelar una cita
     * 
     * @param int $id ID de la cita
     */
    public function cancel($id) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar que el usuario sea el agente o cliente de la cita
        if ($cita['agente_id'] != $_SESSION['user_id'] && $cita['cliente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'No tienes permisos para cancelar esta cita.');
            redirect('/appointments');
        }
        
        // Verificar que la cita no esté completada o cancelada
        if (in_array($cita['estado'], [Appointment::STATUS_COMPLETED, Appointment::STATUS_CANCELLED])) {
            setFlashMessage('error', 'No se puede cancelar una cita completada o cancelada.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita
        if ($this->appointmentModel->updateStatus($id, Appointment::STATUS_CANCELLED)) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'cancelled');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            setFlashMessage('success', 'Cita cancelada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            setFlashMessage('error', 'Error al cancelar la cita.');
            redirect('/appointments/' . $id);
        }
    }
    
    /**
     * Marcar cita como completada (solo agentes)
     * 
     * @param int $id ID de la cita
     */
    public function complete($id) {
        // Verificar que el usuario esté autenticado y sea agente
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita || $cita['agente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Cita no encontrada o no tienes permisos.');
            redirect('/appointments');
        }
        
        // Verificar que la cita esté aceptada
        if ($cita['estado'] !== Appointment::STATUS_ACCEPTED) {
            setFlashMessage('error', 'Solo se pueden completar citas aceptadas.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita
        if ($this->appointmentModel->updateStatus($id, Appointment::STATUS_COMPLETED)) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'completed');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            setFlashMessage('success', 'Cita marcada como completada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            setFlashMessage('error', 'Error al marcar la cita como completada.');
            redirect('/appointments/' . $id);
        }
    }
    
    /**
     * Mostrar vista de calendario
     */
    public function calendar() {
        // Verificar que el usuario esté autenticado y sea agente
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Obtener mes y año
        $mes = (int)($_GET['mes'] ?? date('n'));
        $anio = (int)($_GET['anio'] ?? date('Y'));
        
        // Validar mes y año
        if ($mes < 1 || $mes > 12) $mes = date('n');
        if ($anio < 2020 || $anio > 2030) $anio = date('Y');
        
        // Obtener citas del mes
        $fechaInicio = sprintf('%04d-%02d-01', $anio, $mes);
        $fechaFin = date('Y-m-t', strtotime($fechaInicio));
        
        $citas = $this->appointmentModel->getByDateRange($_SESSION['user_id'], $fechaInicio, $fechaFin);
        
        // Organizar citas por día
        $citasPorDia = [];
        foreach ($citas as $cita) {
            $dia = date('j', strtotime($cita['fecha_cita']));
            if (!isset($citasPorDia[$dia])) {
                $citasPorDia[$dia] = [];
            }
            $citasPorDia[$dia][] = $cita;
        }
        
        $pageTitle = 'Calendario de Citas - ' . APP_NAME;
        
        // Renderizar con layout
        $this->render('calendar', [
            'pageTitle' => $pageTitle,
            'citas' => $citas,
            'citasPorDia' => $citasPorDia,
            'mes' => $mes,
            'anio' => $anio
        ]);
    }
    
    /**
     * Obtener citas para API (AJAX)
     */
    public function getAppointments() {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Verificar método GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        // Obtener parámetros
        $fechaInicio = $_GET['start'] ?? date('Y-m-01');
        $fechaFin = $_GET['end'] ?? date('Y-m-t');
        $estado = $_GET['status'] ?? null;
        
        // Obtener citas según el rol del usuario
        if (hasRole(ROLE_AGENTE)) {
            $citas = $this->appointmentModel->getByDateRange($_SESSION['user_id'], $fechaInicio, $fechaFin);
        } else {
            $citas = $this->appointmentModel->getByClient($_SESSION['user_id'], $estado);
        }
        
        // Formatear citas para el calendario
        $citasFormateadas = [];
        foreach ($citas as $cita) {
            $citasFormateadas[] = [
                'id' => $cita['id'],
                'title' => ($cita['cliente_nombre'] ?? 'Cliente') . ' ' . ($cita['cliente_apellido'] ?? ''),
                'start' => $cita['fecha_cita'],
                'end' => date('Y-m-d H:i:s', strtotime($cita['fecha_cita'] . ' +1 hour')),
                'estado' => $cita['estado'],
                'tipo_cita' => $cita['tipo_cita'],
                'lugar' => $cita['lugar'],
                'observaciones' => $cita['observaciones'] ?? '',
                'cliente_email' => $cita['cliente_email'] ?? '',
                'cliente_telefono' => $cita['cliente_telefono'] ?? '',
                'propiedad_titulo' => $cita['propiedad_titulo'] ?? 'Propiedad',
                'url' => '/appointments/' . $cita['id']
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($citasFormateadas);
    }
    
    /**
     * Enviar notificación por email
     * 
     * @param int $appointmentId ID de la cita
     * @param string $action Acción realizada
     */
    private function sendAppointmentNotification($appointmentId, $action) {
        $cita = $this->appointmentModel->getById($appointmentId);
        if (!$cita) {
            return;
        }
        
        $subject = '';
        $template = '';
        $data = [
            'cita' => $cita,
            'app_name' => APP_NAME,
            'app_url' => APP_URL
        ];
        
        switch ($action) {
            case 'created':
                $subject = 'Nueva cita propuesta - ' . APP_NAME;
                $template = 'appointment_created';
                break;
            case 'updated':
                $subject = 'Cita actualizada - ' . APP_NAME;
                $template = 'appointment_updated';
                break;
            case 'accepted':
                $subject = 'Cita aceptada - ' . APP_NAME;
                $template = 'appointment_accepted';
                break;
            case 'rejected':
                $subject = 'Cita rechazada - ' . APP_NAME;
                $template = 'appointment_rejected';
                break;
            case 'cancelled':
                $subject = 'Cita cancelada - ' . APP_NAME;
                $template = 'appointment_cancelled';
                break;
            case 'completed':
                $subject = 'Cita completada - ' . APP_NAME;
                $template = 'appointment_completed';
                break;
        }
        
        if ($template) {
            // TODO: Implementar envío de emails cuando esté configurado
            // Por ahora, solo log para desarrollo
            error_log("EMAIL - {$action} - Cliente: {$cita['cliente_email']} - Agente: {$cita['agente_email']}");
            
            // Comentado temporalmente hasta implementar templates de email
            /*
            // Enviar email al cliente
            $this->emailHelper->sendCustomEmail(
                $cita['cliente_email'],
                $subject,
                $this->getEmailTemplate($template, $data),
                '',
                $cita['cliente_nombre'] . ' ' . $cita['cliente_apellido']
            );
            
            // Enviar email al agente
            $this->emailHelper->sendCustomEmail(
                $cita['agente_email'],
                $subject,
                $this->getEmailTemplate($template, $data),
                '',
                $cita['agente_nombre'] . ' ' . $cita['agente_apellido']
            );
            */
        }
    }


    

} 