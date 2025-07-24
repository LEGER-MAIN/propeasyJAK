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
            
            // Obtener solicitudes pendientes del agente actual
            $solicitudes = $this->solicitudModel->getSolicitudesAgente($_SESSION['user_id'], REQUEST_STATUS_NEW, 50, 0);
        } catch (Exception $e) {
            // En caso de error de base de datos, usar arrays vacíos
            error_log("Error en create(): " . $e->getMessage());
            $solicitudes = [];
            $solicitud = null;
            $propiedad = null;
            $cliente = null;
        }
        
        $pageTitle = 'Crear Nueva Cita - ' . APP_NAME;
        

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
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
                return;
            }
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/appointments/' . $id);
        }
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cita no encontrada.']);
                return;
            }
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar que el usuario sea el cliente de la cita
        if ($cita['cliente_id'] != $_SESSION['user_id']) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para aceptar esta cita.']);
                return;
            }
            setFlashMessage('error', 'No tienes permisos para aceptar esta cita.');
            redirect('/appointments');
        }
        
        // Verificar que la cita esté en estado propuesta
        if ($cita['estado'] !== Appointment::STATUS_PROPOSED) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Solo se pueden aceptar citas propuestas.']);
                return;
            }
            setFlashMessage('error', 'Solo se pueden aceptar citas propuestas.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita
        $result = $this->appointmentModel->updateStatus($id, Appointment::STATUS_ACCEPTED);
        
        if ($result) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'accepted');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => true, 'message' => 'Cita aceptada exitosamente.']);
                return;
            }
            setFlashMessage('success', 'Cita aceptada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al aceptar la cita.']);
                return;
            }
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
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
                return;
            }
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/appointments/' . $id);
        }
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cita no encontrada.']);
                return;
            }
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar que el usuario sea el cliente de la cita
        if ($cita['cliente_id'] != $_SESSION['user_id']) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para rechazar esta cita.']);
                return;
            }
            setFlashMessage('error', 'No tienes permisos para rechazar esta cita.');
            redirect('/appointments');
        }
        
        // Verificar que la cita esté en estado propuesta
        if ($cita['estado'] !== Appointment::STATUS_PROPOSED) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Solo se pueden rechazar citas propuestas.']);
                return;
            }
            setFlashMessage('error', 'Solo se pueden rechazar citas propuestas.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita
        $result = $this->appointmentModel->updateStatus($id, Appointment::STATUS_REJECTED);
        
        if ($result) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'rejected');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => true, 'message' => 'Cita rechazada exitosamente.']);
                return;
            }
            setFlashMessage('success', 'Cita rechazada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al rechazar la cita.']);
                return;
            }
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
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
                return;
            }
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/appointments/' . $id);
        }
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cita no encontrada.']);
                return;
            }
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar que el usuario sea el agente o cliente de la cita
        if ($cita['agente_id'] != $_SESSION['user_id'] && $cita['cliente_id'] != $_SESSION['user_id']) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para cancelar esta cita.']);
                return;
            }
            setFlashMessage('error', 'No tienes permisos para cancelar esta cita.');
            redirect('/appointments');
        }
        
        // Verificar que la cita no esté completada o cancelada
        if (in_array($cita['estado'], [Appointment::STATUS_COMPLETED, Appointment::STATUS_CANCELLED])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No se puede cancelar una cita completada o cancelada.']);
                return;
            }
            setFlashMessage('error', 'No se puede cancelar una cita completada o cancelada.');
            redirect('/appointments/' . $id);
        }
        
        // Si es cliente, solo puede cancelar citas aceptadas
        if ($cita['cliente_id'] == $_SESSION['user_id'] && $cita['estado'] !== Appointment::STATUS_ACCEPTED) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Solo puedes cancelar citas que hayas aceptado previamente.']);
                return;
            }
            setFlashMessage('error', 'Solo puedes cancelar citas que hayas aceptado previamente.');
            redirect('/appointments/' . $id);
        }
        
        // Si es agente, puede cancelar citas propuestas, aceptadas o con cambio solicitado
        if ($cita['agente_id'] == $_SESSION['user_id'] && 
            !in_array($cita['estado'], [Appointment::STATUS_PROPOSED, Appointment::STATUS_ACCEPTED, Appointment::STATUS_CHANGE_REQUESTED])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No puedes cancelar esta cita en su estado actual.']);
                return;
            }
            setFlashMessage('error', 'No puedes cancelar esta cita en su estado actual.');
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
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => true, 'message' => 'Cita cancelada exitosamente.']);
                return;
            }
            setFlashMessage('success', 'Cita cancelada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al cancelar la cita.']);
                return;
            }
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
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
                return;
            }
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/appointments/' . $id);
        }
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita || $cita['agente_id'] != $_SESSION['user_id']) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cita no encontrada o no tienes permisos.']);
                return;
            }
            setFlashMessage('error', 'Cita no encontrada o no tienes permisos.');
            redirect('/appointments');
        }
        
        // Verificar que la cita esté aceptada
        if ($cita['estado'] !== Appointment::STATUS_ACCEPTED) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Solo se pueden completar citas aceptadas.']);
                return;
            }
            setFlashMessage('error', 'Solo se pueden completar citas aceptadas.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita
        $result = $this->appointmentModel->updateStatus($id, Appointment::STATUS_COMPLETED);
        
        if ($result) {
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'completed');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => true, 'message' => 'Cita marcada como completada exitosamente.']);
                return;
            }
            setFlashMessage('success', 'Cita marcada como completada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al marcar la cita como completada.']);
                return;
            }
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
        
        // Obtener datos del agente y cliente
        $agente = $this->userModel->getById($cita['agente_id']);
        $cliente = $this->userModel->getById($cita['cliente_id']);
        
        if (!$agente || !$cliente) {
            error_log("Error: No se encontraron datos del agente o cliente para la cita {$appointmentId}");
            return;
        }
        
        try {
        switch ($action) {
            case 'created':
                    // Enviar notificación de nueva cita
                    $this->emailHelper->sendAppointmentNotification($cita, $agente, $cliente);
                break;
                    
            case 'updated':
                    // Enviar notificación de cita actualizada
                    $this->emailHelper->sendCustomEmail(
                        $cliente['email'],
                        'Cita actualizada - ' . APP_NAME,
                        $this->getAppointmentUpdateEmailTemplate($cita, $agente, $cliente),
                        $this->getAppointmentUpdateEmailText($cita, $agente, $cliente),
                        $cliente['nombre'] . ' ' . $cliente['apellido']
                    );
                break;
                    
            case 'accepted':
                    // Enviar notificación de cita aceptada
                    $this->emailHelper->sendCustomEmail(
                        $agente['email'],
                        'Cita aceptada - ' . APP_NAME,
                        $this->getAppointmentAcceptedEmailTemplate($cita, $agente, $cliente),
                        $this->getAppointmentAcceptedEmailText($cita, $agente, $cliente),
                        $agente['nombre'] . ' ' . $agente['apellido']
                    );
                break;
                    
            case 'rejected':
                    // Enviar notificación de cita rechazada
                    $this->emailHelper->sendCustomEmail(
                        $agente['email'],
                        'Cita rechazada - ' . APP_NAME,
                        $this->getAppointmentRejectedEmailTemplate($cita, $agente, $cliente),
                        $this->getAppointmentRejectedEmailText($cita, $agente, $cliente),
                        $agente['nombre'] . ' ' . $agente['apellido']
                    );
                break;
                    
            case 'change_requested':
                    // Enviar notificación de solicitud de cambio
                    $comentariosCambio = $_POST['comentarios_cambio'] ?? '';
                    $this->emailHelper->sendCustomEmail(
                        $agente['email'],
                        'Solicitud de cambio de cita - ' . APP_NAME,
                        $this->getAppointmentChangeRequestedEmailTemplate($cita, $agente, $cliente, $comentariosCambio),
                        $this->getAppointmentChangeRequestedEmailText($cita, $agente, $cliente, $comentariosCambio),
                        $agente['nombre'] . ' ' . $agente['apellido']
                    );
                break;
                    
            case 'cancelled':
                    // Enviar notificación de cita cancelada
                    $motivo = $_POST['motivo_cancelacion'] ?? '';
                    $this->emailHelper->sendAppointmentCancellation($cita, $agente, $cliente, $motivo);
                break;
                    
            case 'completed':
                    // Enviar notificación de cita completada
                    $this->emailHelper->sendCustomEmail(
                        $cliente['email'],
                        'Cita completada - ' . APP_NAME,
                        $this->getAppointmentCompletedEmailTemplate($cita, $agente, $cliente),
                        $this->getAppointmentCompletedEmailText($cita, $agente, $cliente),
                        $cliente['nombre'] . ' ' . $cliente['apellido']
                    );
                break;
        }
        
            error_log("EMAIL ENVIADO - {$action} - Cita: {$appointmentId}");
            
        } catch (Exception $e) {
            error_log("Error enviando email de notificación: " . $e->getMessage());
        }
    }
    
    /**
     * Template HTML para cita actualizada
     */
    private function getAppointmentUpdateEmailTemplate($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Cita actualizada - " . APP_NAME . "</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; background: white; }
                .appointment-details { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #3b82f6; }
                .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
                .detail-row:last-child { border-bottom: none; }
                .detail-label { font-weight: 600; color: #374151; }
                .detail-value { color: #1f2937; }
                .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📅 Cita Actualizada</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$cliente['nombre']},</h2>
                    <p>Tu cita ha sido actualizada con los siguientes detalles:</p>
                    <div class='appointment-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Fecha y Hora:</span>
                            <span class='detail-value'>{$fecha}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Tipo de Cita:</span>
                            <span class='detail-value'>{$tipoCita}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Ubicación:</span>
                            <span class='detail-value'>{$lugar}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Agente:</span>
                            <span class='detail-value'>{$agente['nombre']} {$agente['apellido']}</span>
                        </div>
                    </div>
                    <p>Por favor, confirma que estos nuevos detalles te funcionan.</p>
                    <p>¡Gracias por usar " . APP_NAME . "!</p>
                </div>
                <div class='footer'>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                    <p>&copy; " . date('Y') . " " . APP_NAME . ". Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Template de texto para cita actualizada
     */
    private function getAppointmentUpdateEmailText($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "Cita actualizada - " . APP_NAME . "

Hola {$cliente['nombre']},

Tu cita ha sido actualizada con los siguientes detalles:

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}
Agente: {$agente['nombre']} {$agente['apellido']}

Por favor, confirma que estos nuevos detalles te funcionan.

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Template HTML para cita aceptada
     */
    private function getAppointmentAcceptedEmailTemplate($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Cita aceptada - " . APP_NAME . "</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; background: white; }
                .success-box { background: #d1fae5; border: 2px solid #059669; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
                .success-box h3 { color: #065f46; margin: 0 0 10px 0; font-size: 20px; }
                .appointment-details { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #059669; }
                .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
                .detail-row:last-child { border-bottom: none; }
                .detail-label { font-weight: 600; color: #374151; }
                .detail-value { color: #1f2937; }
                .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Cita Aceptada</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$agente['nombre']},</h2>
                    <div class='success-box'>
                        <h3>¡Excelente noticia!</h3>
                        <p>El cliente ha aceptado la cita programada.</p>
                    </div>
                    <div class='appointment-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Fecha y Hora:</span>
                            <span class='detail-value'>{$fecha}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Tipo de Cita:</span>
                            <span class='detail-value'>{$tipoCita}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Ubicación:</span>
                            <span class='detail-value'>{$lugar}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Cliente:</span>
                            <span class='detail-value'>{$cliente['nombre']} {$cliente['apellido']}</span>
                        </div>
                    </div>
                    <p>¡Prepárate para la cita y asegúrate de tener toda la información necesaria!</p>
                    <p>¡Gracias por usar " . APP_NAME . "!</p>
                </div>
                <div class='footer'>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                    <p>&copy; " . date('Y') . " " . APP_NAME . ". Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Template de texto para cita aceptada
     */
    private function getAppointmentAcceptedEmailText($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "Cita aceptada - " . APP_NAME . "

Hola {$agente['nombre']},

¡Excelente noticia! El cliente ha aceptado la cita programada.

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}
Cliente: {$cliente['nombre']} {$cliente['apellido']}

¡Prepárate para la cita y asegúrate de tener toda la información necesaria!

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Template HTML para cita rechazada
     */
    private function getAppointmentRejectedEmailTemplate($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Cita rechazada - " . APP_NAME . "</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; background: white; }
                .rejection-box { background: #fef2f2; border: 2px solid #dc2626; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
                .rejection-box h3 { color: #991b1b; margin: 0 0 10px 0; font-size: 20px; }
                .appointment-details { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #dc2626; }
                .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
                .detail-row:last-child { border-bottom: none; }
                .detail-label { font-weight: 600; color: #374151; }
                .detail-value { color: #1f2937; }
                .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>❌ Cita Rechazada</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$agente['nombre']},</h2>
                    <div class='rejection-box'>
                        <h3>La cita ha sido rechazada</h3>
                        <p>El cliente ha rechazado la cita programada.</p>
                    </div>
                    <div class='appointment-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Fecha y Hora:</span>
                            <span class='detail-value'>{$fecha}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Tipo de Cita:</span>
                            <span class='detail-value'>{$tipoCita}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Ubicación:</span>
                            <span class='detail-value'>{$lugar}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Cliente:</span>
                            <span class='detail-value'>{$cliente['nombre']} {$cliente['apellido']}</span>
                        </div>
                    </div>
                    <p>Considera contactar al cliente para proponer una nueva fecha o entender las razones del rechazo.</p>
                    <p>¡Gracias por usar " . APP_NAME . "!</p>
                </div>
                <div class='footer'>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                    <p>&copy; " . date('Y') . " " . APP_NAME . ". Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Template de texto para cita rechazada
     */
    private function getAppointmentRejectedEmailText($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "Cita rechazada - " . APP_NAME . "

Hola {$agente['nombre']},

El cliente {$cliente['nombre']} {$cliente['apellido']} ha rechazado la siguiente cita:

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}

Por favor, contacta al cliente para programar una nueva cita o resolver cualquier inconveniente.

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Template HTML para cita completada
     */
    private function getAppointmentCompletedEmailTemplate($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Cita completada - " . APP_NAME . "</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; background: white; }
                .completion-box { background: #d1fae5; border: 2px solid #059669; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
                .completion-box h3 { color: #065f46; margin: 0 0 10px 0; font-size: 20px; }
                .appointment-details { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #059669; }
                .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
                .detail-row:last-child { border-bottom: none; }
                .detail-label { font-weight: 600; color: #374151; }
                .detail-value { color: #1f2937; }
                .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Cita Completada</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$cliente['nombre']},</h2>
                    <div class='completion-box'>
                        <h3>¡Cita completada exitosamente!</h3>
                        <p>Tu cita ha sido marcada como completada.</p>
                    </div>
                    <div class='appointment-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Fecha y Hora:</span>
                            <span class='detail-value'>{$fecha}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Tipo de Cita:</span>
                            <span class='detail-value'>{$tipoCita}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Ubicación:</span>
                            <span class='detail-value'>{$lugar}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Agente:</span>
                            <span class='detail-value'>{$agente['nombre']} {$agente['apellido']}</span>
                        </div>
                    </div>
                    <p>Esperamos que la cita haya sido productiva. Si tienes alguna pregunta o necesitas más información, no dudes en contactar al agente.</p>
                    <p>¡Gracias por usar " . APP_NAME . "!</p>
                </div>
                <div class='footer'>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                    <p>&copy; " . date('Y') . " " . APP_NAME . ". Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Template de texto para cita completada
     */
    private function getAppointmentCompletedEmailText($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "Cita completada - " . APP_NAME . "

Hola {$cliente['nombre']},

¡Cita completada exitosamente!

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}
Agente: {$agente['nombre']} {$agente['apellido']}

Esperamos que la cita haya sido productiva. Si tienes alguna pregunta o necesitas más información, no dudes en contactar al agente.

Saludos,
El equipo de " . APP_NAME;
    }

    /**
     * Obtener citas pendientes de aceptación para el cliente actual
     * API endpoint para el modal de notificaciones
     */
    public function getPendingAppointments() {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Verificar que sea cliente
        if ($_SESSION['user_rol'] !== 'cliente') {
            http_response_code(403);
            echo json_encode(['error' => 'Solo clientes pueden ver citas pendientes']);
            return;
        }
        
        try {
            // Obtener citas en estado "propuesta" para el cliente actual
            $citas = $this->appointmentModel->getByClient($_SESSION['user_id'], Appointment::STATUS_PROPOSED);
            
            // Formatear datos para el frontend
            $formattedAppointments = [];
            foreach ($citas as $cita) {
                $formattedAppointments[] = [
                    'id' => $cita['id'],
                    'fecha' => date('d/m/Y H:i', strtotime($cita['fecha_cita'])),
                    'tipo' => ucfirst(str_replace('_', ' ', $cita['tipo_cita'])),
                    'lugar' => $cita['lugar'],
                    'agente_nombre' => $cita['agente_nombre'] . ' ' . $cita['agente_apellido'],
                    'agente_telefono' => $cita['agente_telefono'],
                    'propiedad_titulo' => $cita['propiedad_titulo'] ?? 'Propiedad',
                    'observaciones' => $cita['observaciones'] ?? ''
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'citas' => $formattedAppointments,
                'count' => count($formattedAppointments)
            ]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo citas pendientes: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Solicitar cambio de cita (solo clientes)
     * 
     * @param int $id ID de la cita
     */
    public function requestChange($id) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/appointments/' . $id);
        }
        
        // Obtener la cita
        $cita = $this->appointmentModel->getById($id);
        if (!$cita) {
            setFlashMessage('error', 'Cita no encontrada.');
            redirect('/appointments');
        }
        
        // Verificar que el usuario sea el cliente de la cita
        if ($cita['cliente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'No tienes permisos para solicitar cambios en esta cita.');
            redirect('/appointments');
        }
        
        // Verificar que la cita esté en estado propuesta
        if ($cita['estado'] !== Appointment::STATUS_PROPOSED) {
            setFlashMessage('error', 'Solo se pueden solicitar cambios en citas propuestas.');
            redirect('/appointments/' . $id);
        }
        
        // Obtener comentarios del cambio solicitado
        $comentariosCambio = sanitizeInput($_POST['comentarios_cambio'] ?? '');
        if (empty($comentariosCambio)) {
            setFlashMessage('error', 'Debes especificar qué cambios deseas en la cita.');
            redirect('/appointments/' . $id);
        }
        
        // Actualizar estado de la cita y agregar comentarios
        $updateData = [
            'estado' => Appointment::STATUS_CHANGE_REQUESTED,
            'comentarios_cambio' => $comentariosCambio
        ];
        
        $result = $this->appointmentModel->updateStatus($id, Appointment::STATUS_CHANGE_REQUESTED);
        
        if ($result) {
            // Actualizar comentarios de cambio si existe el campo
            $this->appointmentModel->update($id, ['comentarios_cambio' => $comentariosCambio]);
            
            // Enviar notificación por email
            try {
                $this->sendAppointmentNotification($id, 'change_requested');
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
            }
            
            setFlashMessage('success', 'Solicitud de cambio enviada exitosamente.');
            redirect('/appointments/' . $id);
        } else {
            setFlashMessage('error', 'Error al solicitar el cambio de cita.');
            redirect('/appointments/' . $id);
        }
    }

    /**
     * Template HTML para solicitud de cambio de cita
     */
    private function getAppointmentChangeRequestedEmailTemplate($cita, $agente, $cliente, $comentariosCambio) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Solicitud de cambio de cita - " . APP_NAME . "</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; background: white; }
                .appointment-details { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #f59e0b; }
                .change-request { background: #fef3c7; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #f59e0b; }
                .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
                .detail-row:last-child { border-bottom: none; }
                .detail-label { font-weight: 600; color: #374151; }
                .detail-value { color: #1f2937; }
                .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔄 Solicitud de Cambio</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$agente['nombre']},</h2>
                    <p>El cliente <strong>{$cliente['nombre']} {$cliente['apellido']}</strong> ha solicitado cambios en la siguiente cita:</p>
                    <div class='appointment-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Fecha y Hora:</span>
                            <span class='detail-value'>{$fecha}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Tipo de Cita:</span>
                            <span class='detail-value'>{$tipoCita}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Ubicación:</span>
                            <span class='detail-value'>{$lugar}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Cliente:</span>
                            <span class='detail-value'>{$cliente['nombre']} {$cliente['apellido']}</span>
                        </div>
                    </div>
                    <div class='change-request'>
                        <h3>Cambios Solicitados:</h3>
                        <p>" . nl2br(htmlspecialchars($comentariosCambio)) . "</p>
                    </div>
                    <p>Por favor, revisa la solicitud y actualiza la cita según sea necesario.</p>
                    <p>¡Gracias por tu atención!</p>
                </div>
                <div class='footer'>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                    <p>&copy; " . date('Y') . " " . APP_NAME . ". Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Template de texto para solicitud de cambio de cita
     */
    private function getAppointmentChangeRequestedEmailText($cita, $agente, $cliente, $comentariosCambio) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        return "Solicitud de cambio de cita - " . APP_NAME . "

Hola {$agente['nombre']},

El cliente {$cliente['nombre']} {$cliente['apellido']} ha solicitado cambios en la siguiente cita:

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}

Cambios solicitados:
{$comentariosCambio}

Por favor, revisa la solicitud y actualiza la cita según sea necesario.

Saludos,
El equipo de " . APP_NAME;
    }
} 
