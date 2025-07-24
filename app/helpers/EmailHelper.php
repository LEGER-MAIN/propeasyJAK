<?php
/**
 * Helper de Email - PHPMailer
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este helper maneja el envío de emails usando PHPMailer
 * para una mejor compatibilidad y manejo de errores.
 */

// Incluir autoloader de Composer para PHPMailer
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    private $mailer;
    
    /**
     * Constructor del helper
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }
    
    /**
     * Configurar PHPMailer
     */
    private function configureMailer() {
        try {
            // Configuración del servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USER;
            $this->mailer->Password = SMTP_PASS;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = SMTP_PORT;
            
            // Configuración del remitente
            $this->mailer->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $this->mailer->addReplyTo(SMTP_FROM, SMTP_FROM_NAME);
            
            // Configuración de caracteres
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            
            // Configuración de debug (solo en desarrollo)
            if (defined('APP_ENV') && APP_ENV === 'development') {
                $this->mailer->SMTPDebug = SMTP::DEBUG_OFF; // Cambiar a DEBUG_SERVER si necesitas debug
            } else {
                $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;
            }
            
        } catch (Exception $e) {
            error_log("Error configurando PHPMailer: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar email de verificación
     * 
     * @param string $to Email destinatario
     * @param string $token Token de verificación
     * @param string $nombre Nombre del usuario
     * @return bool True si se envió correctamente
     */
    public function sendVerificationEmail($to, $token, $nombre) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $nombre);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Verifica tu cuenta - ' . APP_NAME;
            
            $verificationUrl = APP_URL . '/verify-email?token=' . $token;
            
            $this->mailer->Body = $this->getVerificationEmailTemplate($nombre, $verificationUrl);
            $this->mailer->AltBody = $this->getVerificationEmailText($nombre, $verificationUrl);
            
            $this->mailer->send();
            
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("EMAIL ENVIADO - Verificación: {$to}");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email de verificación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar email de recuperación de contraseña
     * 
     * @param string $to Email destinatario
     * @param string $token Token de reset
     * @param string $nombre Nombre del usuario
     * @return bool True si se envió correctamente
     */
    public function sendPasswordResetEmail($to, $token, $nombre) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $nombre);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Recuperación de contraseña - ' . APP_NAME;
            
            $resetUrl = APP_URL . '/reset-password?token=' . $token;
            
            $this->mailer->Body = $this->getPasswordResetEmailTemplate($nombre, $resetUrl);
            $this->mailer->AltBody = $this->getPasswordResetEmailText($nombre, $resetUrl);
            
            $this->mailer->send();
            
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("EMAIL ENVIADO - Reset Password: {$to}");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email de reset: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar email personalizado
     * 
     * @param string $to Email destinatario
     * @param string $subject Asunto del email
     * @param string $htmlBody Cuerpo HTML del email
     * @param string $textBody Cuerpo de texto plano (opcional)
     * @param string $toName Nombre del destinatario (opcional)
     * @return bool True si se envió correctamente
     */
    public function sendCustomEmail($to, $subject, $htmlBody, $textBody = '', $toName = '') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $toName);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $textBody ?: strip_tags($htmlBody);
            
            $this->mailer->send();
            
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("EMAIL ENVIADO - Personalizado: {$to} - {$subject}");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email personalizado: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener template HTML para email de verificación
     * 
     * @param string $nombre Nombre del usuario
     * @param string $verificationUrl URL de verificación
     * @return string Template HTML
     */
    private function getVerificationEmailTemplate($nombre, $verificationUrl) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Verifica tu cuenta - " . APP_NAME . "</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 12px; 
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    overflow: hidden; 
                }
                .header { 
                    background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600; 
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
                }
                .content { 
                    padding: 40px 30px; 
                    background: white; 
                }
                .content h2 { 
                    color: #2d3748; 
                    margin-top: 0; 
                    margin-bottom: 20px; 
                    font-size: 24px; 
                    font-weight: 600; 
                }
                .button-container { 
                    text-align: center; 
                    margin: 30px 0; 
                    padding: 20px 0; 
                }
                .button { 
                    display: inline-block; 
                    background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%); 
                    color: #ffffff !important; 
                    padding: 16px 32px; 
                    text-decoration: none; 
                    border-radius: 50px; 
                    font-weight: 700; 
                    font-size: 16px; 
                    text-transform: uppercase; 
                    letter-spacing: 0.5px; 
                    box-shadow: 0 4px 15px rgba(30, 64, 175, 0.5); 
                    transition: all 0.3s ease; 
                    border: none; 
                    cursor: pointer; 
                    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3); 
                }
                .button:hover { 
                    transform: translateY(-2px); 
                    box-shadow: 0 6px 20px rgba(30, 64, 175, 0.7); 
                    background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%); 
                }
                .link-text { 
                    background: #f7fafc; 
                    padding: 15px; 
                    border-radius: 8px; 
                    border-left: 4px solid #1e40af; 
                    margin: 20px 0; 
                    word-break: break-all; 
                    font-family: 'Courier New', monospace; 
                    font-size: 14px; 
                    color: #4a5568; 
                }
                .important-note { 
                    background: #fff5f5; 
                    border: 1px solid #fed7d7; 
                    border-radius: 8px; 
                    padding: 15px; 
                    margin: 20px 0; 
                    color: #c53030; 
                }
                .footer { 
                    text-align: center; 
                    margin-top: 30px; 
                    padding: 20px; 
                    background: #f7fafc; 
                    color: #718096; 
                    font-size: 14px; 
                    border-top: 1px solid #e2e8f0; 
                }
                @media only screen and (max-width: 600px) {
                    .container { margin: 10px; }
                    .content { padding: 30px 20px; }
                    .button { padding: 14px 28px; font-size: 15px; }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>" . APP_NAME . "</h1>
                </div>
                <div class='content'>
                    <h2>¡Bienvenido a " . APP_NAME . "!</h2>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Gracias por registrarte en nuestra plataforma. Para completar tu registro y acceder a todas las funcionalidades, por favor verifica tu dirección de email.</p>
                    
                    <div class='button-container'>
                        <a href='{$verificationUrl}' class='button' style='display: inline-block; background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%); color: #ffffff !important; padding: 16px 32px; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(30, 64, 175, 0.5); border: none; cursor: pointer; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);'>Verificar mi cuenta</a>
                    </div>
                    
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <div class='link-text'>{$verificationUrl}</div>
                    
                    <div class='important-note'>
                        <strong>⚠️ Importante:</strong> Este enlace expirará en 1 hora por motivos de seguridad.
                    </div>
                    
                    <p>Si no creaste esta cuenta, puedes ignorar este mensaje de forma segura.</p>
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
     * Obtener template de texto plano para email de verificación
     * 
     * @param string $nombre Nombre del usuario
     * @param string $verificationUrl URL de verificación
     * @return string Template de texto plano
     */
    private function getVerificationEmailText($nombre, $verificationUrl) {
        return "¡Bienvenido a " . APP_NAME . "!

Hola {$nombre},

Gracias por registrarte en nuestra plataforma. Para completar tu registro, por favor verifica tu dirección de email visitando el siguiente enlace:

{$verificationUrl}

Este enlace expirará en 1 hora por motivos de seguridad.

Si no creaste esta cuenta, puedes ignorar este mensaje.

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Obtener template HTML para email de reset de contraseña
     * 
     * @param string $nombre Nombre del usuario
     * @param string $resetUrl URL de reset
     * @return string Template HTML
     */
    private function getPasswordResetEmailTemplate($nombre, $resetUrl) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Recuperación de contraseña - " . APP_NAME . "</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 12px; 
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    overflow: hidden; 
                }
                .header { 
                    background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600; 
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
                }
                .content { 
                    padding: 40px 30px; 
                    background: white; 
                }
                .content h2 { 
                    color: #2d3748; 
                    margin-top: 0; 
                    margin-bottom: 20px; 
                    font-size: 24px; 
                    font-weight: 600; 
                }
                .button-container { 
                    text-align: center; 
                    margin: 30px 0; 
                    padding: 20px 0; 
                }
                .button { 
                    display: inline-block; 
                    background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); 
                    color: #ffffff !important; 
                    padding: 16px 32px; 
                    text-decoration: none; 
                    border-radius: 50px; 
                    font-weight: 700; 
                    font-size: 16px; 
                    text-transform: uppercase; 
                    letter-spacing: 0.5px; 
                    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.5); 
                    transition: all 0.3s ease; 
                    border: none; 
                    cursor: pointer; 
                    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3); 
                }
                .button:hover { 
                    transform: translateY(-2px); 
                    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.7); 
                    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); 
                }
                .link-text { 
                    background: #f7fafc; 
                    padding: 15px; 
                    border-radius: 8px; 
                    border-left: 4px solid #dc2626; 
                    margin: 20px 0; 
                    word-break: break-all; 
                    font-family: 'Courier New', monospace; 
                    font-size: 14px; 
                    color: #4a5568; 
                }
                .important-note { 
                    background: #fff5f5; 
                    border: 1px solid #fed7d7; 
                    border-radius: 8px; 
                    padding: 15px; 
                    margin: 20px 0; 
                    color: #c53030; 
                }
                .footer { 
                    text-align: center; 
                    margin-top: 30px; 
                    padding: 20px; 
                    background: #f7fafc; 
                    color: #718096; 
                    font-size: 14px; 
                    border-top: 1px solid #e2e8f0; 
                }
                @media only screen and (max-width: 600px) {
                    .container { margin: 10px; }
                    .content { padding: 30px 20px; }
                    .button { padding: 14px 28px; font-size: 15px; }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Recuperación de Contraseña</h1>
                </div>
                <div class='content'>
                    <h2>Hola <strong>{$nombre}</strong>,</h2>
                    <p>Has solicitado restablecer tu contraseña en " . APP_NAME . ".</p>
                    
                    <div class='button-container'>
                        <a href='{$resetUrl}' class='button' style='display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #ffffff !important; padding: 16px 32px; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.5); border: none; cursor: pointer; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);'>Restablecer mi contraseña</a>
                    </div>
                    
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <div class='link-text'>{$resetUrl}</div>
                    
                    <div class='important-note'>
                        <strong>⚠️ Importante:</strong> Este enlace expirará en 30 minutos por motivos de seguridad.
                    </div>
                    
                    <p>Si no solicitaste este cambio, puedes ignorar este mensaje de forma segura. Tu contraseña actual permanecerá sin cambios.</p>
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
     * Obtener template de texto plano para email de reset
     * 
     * @param string $nombre Nombre del usuario
     * @param string $resetUrl URL de reset
     * @return string Template de texto plano
     */
    private function getPasswordResetEmailText($nombre, $resetUrl) {
        return "Recuperación de contraseña - " . APP_NAME . "

Hola {$nombre},

Has solicitado restablecer tu contraseña. Para crear una nueva contraseña, visita el siguiente enlace:

{$resetUrl}

Este enlace expirará en 30 minutos por motivos de seguridad.

Si no solicitaste este cambio, puedes ignorar este mensaje. Tu contraseña actual permanecerá sin cambios.

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Probar conexión SMTP
     * 
     * @return array Resultado de la prueba
     */
    public function testSMTPConnection() {
        try {
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->mailer->smtpConnect();
            $this->mailer->smtpClose();
            
            return [
                'success' => true,
                'message' => 'Conexión SMTP exitosa'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error de conexión SMTP: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Enviar notificación de nueva cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @return bool True si se envió correctamente
     */
    public function sendAppointmentNotification($cita, $agente, $cliente) {
        try {
            // Email para el agente (confirmación de que creó la cita)
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($agente['email'], $agente['nombre'] . ' ' . $agente['apellido']);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Cita propuesta creada - ' . APP_NAME;
            
            $this->mailer->Body = $this->getAppointmentNotificationTemplate($cita, $agente, $cliente, 'agente');
            $this->mailer->AltBody = $this->getAppointmentNotificationText($cita, $agente, $cliente, 'agente');
            
            $this->mailer->send();
            
            // Email para el cliente (propuesta de cita que requiere aceptación)
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($cliente['email'], $cliente['nombre'] . ' ' . $cliente['apellido']);
            
            $this->mailer->Subject = 'Propuesta de cita - ' . APP_NAME;
            $this->mailer->Body = $this->getAppointmentProposalTemplate($cita, $agente, $cliente);
            $this->mailer->AltBody = $this->getAppointmentProposalText($cita, $agente, $cliente);
            
            $this->mailer->send();
            
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("EMAIL ENVIADO - Propuesta de cita: {$cita['id']}");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando propuesta de cita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar recordatorio de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @return bool True si se envió correctamente
     */
    public function sendAppointmentReminder($cita, $agente, $cliente) {
        try {
            // Email para el agente
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($agente['email'], $agente['nombre'] . ' ' . $agente['apellido']);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Recordatorio de cita - ' . APP_NAME;
            
            $this->mailer->Body = $this->getAppointmentReminderTemplate($cita, $agente, $cliente, 'agente');
            $this->mailer->AltBody = $this->getAppointmentReminderText($cita, $agente, $cliente, 'agente');
            
            $this->mailer->send();
            
            // Email para el cliente
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($cliente['email'], $cliente['nombre'] . ' ' . $cliente['apellido']);
            
            $this->mailer->Body = $this->getAppointmentReminderTemplate($cita, $agente, $cliente, 'cliente');
            $this->mailer->AltBody = $this->getAppointmentReminderText($cita, $agente, $cliente, 'cliente');
            
            $this->mailer->send();
            
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("EMAIL ENVIADO - Recordatorio de cita: {$cita['id']}");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando recordatorio de cita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar notificación de cancelación de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @param string $motivo Motivo de la cancelación
     * @return bool True si se envió correctamente
     */
    public function sendAppointmentCancellation($cita, $agente, $cliente, $motivo = '') {
        try {
            // Email para el agente
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($agente['email'], $agente['nombre'] . ' ' . $agente['apellido']);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Cita cancelada - ' . APP_NAME;
            
            $this->mailer->Body = $this->getAppointmentCancellationTemplate($cita, $agente, $cliente, $motivo, 'agente');
            $this->mailer->AltBody = $this->getAppointmentCancellationText($cita, $agente, $cliente, $motivo, 'agente');
            
            $this->mailer->send();
            
            // Email para el cliente
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($cliente['email'], $cliente['nombre'] . ' ' . $cliente['apellido']);
            
            $this->mailer->Body = $this->getAppointmentCancellationTemplate($cita, $agente, $cliente, $motivo, 'cliente');
            $this->mailer->AltBody = $this->getAppointmentCancellationText($cita, $agente, $cliente, $motivo, 'cliente');
            
            $this->mailer->send();
            
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("EMAIL ENVIADO - Cancelación de cita: {$cita['id']}");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando cancelación de cita: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener template HTML para notificación de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @param string $tipo Tipo de destinatario (agente/cliente)
     * @return string Template HTML
     */
    private function getAppointmentNotificationTemplate($cita, $agente, $cliente, $tipo) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        $titulo = $tipo === 'agente' ? 'Nueva cita programada' : 'Tu cita ha sido confirmada';
        $nombreDestinatario = $tipo === 'agente' ? $agente['nombre'] : $cliente['nombre'];
        $otroParticipante = $tipo === 'agente' ? $cliente['nombre'] . ' ' . $cliente['apellido'] : $agente['nombre'] . ' ' . $agente['apellido'];
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$titulo} - " . APP_NAME . "</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 12px; 
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    overflow: hidden; 
                }
                .header { 
                    background: linear-gradient(135deg, #059669 0%, #047857 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600; 
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
                }
                .content { 
                    padding: 40px 30px; 
                    background: white; 
                }
                .appointment-details {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    border-left: 4px solid #059669;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    padding: 8px 0;
                    border-bottom: 1px solid #e2e8f0;
                }
                .detail-row:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #374151;
                }
                .detail-value {
                    color: #1f2937;
                }
                .footer {
                    background: #f8fafc;
                    padding: 20px 30px;
                    text-align: center;
                    color: #6b7280;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📅 {$titulo}</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$nombreDestinatario},</h2>
                    
                    <p>Se ha programado una nueva cita con los siguientes detalles:</p>
                    
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
                            <span class='detail-label'>Participante:</span>
                            <span class='detail-value'>{$otroParticipante}</span>
                        </div>
                    </div>
                    
                    <p>Por favor, asegúrate de estar disponible en la fecha y hora programada. Si necesitas hacer algún cambio, contacta con el otro participante lo antes posible.</p>
                    
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
     * Obtener template de texto plano para notificación de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @param string $tipo Tipo de destinatario (agente/cliente)
     * @return string Template de texto plano
     */
    private function getAppointmentNotificationText($cita, $agente, $cliente, $tipo) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        $titulo = $tipo === 'agente' ? 'Nueva cita programada' : 'Tu cita ha sido confirmada';
        $nombreDestinatario = $tipo === 'agente' ? $agente['nombre'] : $cliente['nombre'];
        $otroParticipante = $tipo === 'agente' ? $cliente['nombre'] . ' ' . $cliente['apellido'] : $agente['nombre'] . ' ' . $agente['apellido'];
        
        return "{$titulo} - " . APP_NAME . "

Hola {$nombreDestinatario},

Se ha programado una nueva cita con los siguientes detalles:

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}
Participante: {$otroParticipante}

Por favor, asegúrate de estar disponible en la fecha y hora programada.

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Obtener template HTML para recordatorio de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @param string $tipo Tipo de destinatario (agente/cliente)
     * @return string Template HTML
     */
    private function getAppointmentReminderTemplate($cita, $agente, $cliente, $tipo) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        $nombreDestinatario = $tipo === 'agente' ? $agente['nombre'] : $cliente['nombre'];
        $otroParticipante = $tipo === 'agente' ? $cliente['nombre'] . ' ' . $cliente['apellido'] : $agente['nombre'] . ' ' . $agente['apellido'];
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Recordatorio de cita - " . APP_NAME . "</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 12px; 
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    overflow: hidden; 
                }
                .header { 
                    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600; 
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
                }
                .content { 
                    padding: 40px 30px; 
                    background: white; 
                }
                .reminder-box {
                    background: #fef3c7;
                    border: 2px solid #f59e0b;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    text-align: center;
                }
                .reminder-box h3 {
                    color: #92400e;
                    margin: 0 0 10px 0;
                    font-size: 20px;
                }
                .appointment-details {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    border-left: 4px solid #f59e0b;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    padding: 8px 0;
                    border-bottom: 1px solid #e2e8f0;
                }
                .detail-row:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #374151;
                }
                .detail-value {
                    color: #1f2937;
                }
                .footer {
                    background: #f8fafc;
                    padding: 20px 30px;
                    text-align: center;
                    color: #6b7280;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>⏰ Recordatorio de Cita</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$nombreDestinatario},</h2>
                    
                    <div class='reminder-box'>
                        <h3>¡Tu cita está próxima!</h3>
                        <p>Recuerda que tienes una cita programada para mañana.</p>
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
                            <span class='detail-label'>Participante:</span>
                            <span class='detail-value'>{$otroParticipante}</span>
                        </div>
                    </div>
                    
                    <p>Por favor, confirma tu asistencia y prepárate para la cita.</p>
                    
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
     * Obtener template de texto plano para recordatorio de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @param string $tipo Tipo de destinatario (agente/cliente)
     * @return string Template de texto plano
     */
    private function getAppointmentReminderText($cita, $agente, $cliente, $tipo) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        $nombreDestinatario = $tipo === 'agente' ? $agente['nombre'] : $cliente['nombre'];
        $otroParticipante = $tipo === 'agente' ? $cliente['nombre'] . ' ' . $cliente['apellido'] : $agente['nombre'] . ' ' . $agente['apellido'];
        
        return "Recordatorio de cita - " . APP_NAME . "

Hola {$nombreDestinatario},

¡Tu cita está próxima! Recuerda que tienes una cita programada para mañana.

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}
Participante: {$otroParticipante}

Por favor, confirma tu asistencia y prepárate para la cita.

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Obtener template HTML para cancelación de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @param string $motivo Motivo de la cancelación
     * @param string $tipo Tipo de destinatario (agente/cliente)
     * @return string Template HTML
     */
    private function getAppointmentCancellationTemplate($cita, $agente, $cliente, $motivo, $tipo) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        $nombreDestinatario = $tipo === 'agente' ? $agente['nombre'] : $cliente['nombre'];
        $otroParticipante = $tipo === 'agente' ? $cliente['nombre'] . ' ' . $cliente['apellido'] : $agente['nombre'] . ' ' . $agente['apellido'];
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Cita cancelada - " . APP_NAME . "</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 12px; 
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    overflow: hidden; 
                }
                .header { 
                    background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600; 
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
                }
                .content { 
                    padding: 40px 30px; 
                    background: white; 
                }
                .cancellation-box {
                    background: #fef2f2;
                    border: 2px solid #dc2626;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    text-align: center;
                }
                .cancellation-box h3 {
                    color: #991b1b;
                    margin: 0 0 10px 0;
                    font-size: 20px;
                }
                .appointment-details {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    border-left: 4px solid #dc2626;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    padding: 8px 0;
                    border-bottom: 1px solid #e2e8f0;
                }
                .detail-row:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #374151;
                }
                .detail-value {
                    color: #1f2937;
                }
                .footer {
                    background: #f8fafc;
                    padding: 20px 30px;
                    text-align: center;
                    color: #6b7280;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>❌ Cita Cancelada</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$nombreDestinatario},</h2>
                    
                    <div class='cancellation-box'>
                        <h3>La cita ha sido cancelada</h3>
                        <p>La cita programada con {$otroParticipante} ha sido cancelada.</p>
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
                            <span class='detail-label'>Participante:</span>
                            <span class='detail-value'>{$otroParticipante}</span>
                        </div>
                        " . ($motivo ? "<div class='detail-row'>
                            <span class='detail-label'>Motivo:</span>
                            <span class='detail-value'>{$motivo}</span>
                        </div>" : "") . "
                    </div>
                    
                    <p>Si necesitas reprogramar la cita, contacta con {$otroParticipante} para coordinar una nueva fecha.</p>
                    
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
     * Obtener template de texto plano para cancelación de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @param string $motivo Motivo de la cancelación
     * @param string $tipo Tipo de destinatario (agente/cliente)
     * @return string Template de texto plano
     */
    private function getAppointmentCancellationText($cita, $agente, $cliente, $motivo, $tipo) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        
        $nombreDestinatario = $tipo === 'agente' ? $agente['nombre'] : $cliente['nombre'];
        $otroParticipante = $tipo === 'agente' ? $cliente['nombre'] . ' ' . $cliente['apellido'] : $agente['nombre'] . ' ' . $agente['apellido'];
        
        $motivoText = $motivo ? "\nMotivo: {$motivo}" : "";
        
        return "Cita cancelada - " . APP_NAME . "

Hola {$nombreDestinatario},

La cita programada con {$otroParticipante} ha sido cancelada.

Fecha y Hora: {$fecha}
Tipo de Cita: {$tipoCita}
Ubicación: {$lugar}
Participante: {$otroParticipante}{$motivoText}

Si necesitas reprogramar la cita, contacta con {$otroParticipante} para coordinar una nueva fecha.

Saludos,
El equipo de " . APP_NAME;
    }
    
    /**
     * Obtener template HTML para propuesta de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @return string Template HTML
     */
    private function getAppointmentProposalTemplate($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        $acceptUrl = APP_URL . '/appointments/' . $cita['id'] . '/accept';
        $rejectUrl = APP_URL . '/appointments/' . $cita['id'] . '/reject';
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Propuesta de cita - " . APP_NAME . "</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 12px; 
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    overflow: hidden; 
                }
                .header { 
                    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600; 
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
                }
                .content { 
                    padding: 40px 30px; 
                    background: white; 
                }
                .proposal-box {
                    background: #eff6ff;
                    border: 2px solid #3b82f6;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    text-align: center;
                }
                .proposal-box h3 {
                    color: #1e40af;
                    margin: 0 0 15px 0;
                    font-size: 20px;
                }
                .appointment-details {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    border-left: 4px solid #3b82f6;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    padding: 8px 0;
                    border-bottom: 1px solid #e2e8f0;
                }
                .detail-row:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #374151;
                }
                .detail-value {
                    color: #1f2937;
                }
                .action-buttons {
                    text-align: center;
                    margin: 30px 0;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 30px;
                    margin: 0 10px;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: 16px;
                    transition: all 0.3s ease;
                }
                .btn-accept {
                    background: #059669;
                    color: white;
                }
                .btn-accept:hover {
                    background: #047857;
                    transform: translateY(-2px);
                }
                .btn-reject {
                    background: #dc2626;
                    color: white;
                }
                .btn-reject:hover {
                    background: #b91c1c;
                    transform: translateY(-2px);
                }
                .footer { 
                    background: #f8fafc; 
                    padding: 20px 30px; 
                    text-align: center; 
                    color: #6b7280; 
                    font-size: 14px; 
                }
                .platform-notice {
                    background: #fef3c7;
                    border: 1px solid #f59e0b;
                    border-radius: 8px;
                    padding: 15px;
                    margin: 20px 0;
                    text-align: center;
                }
                .platform-notice strong {
                    color: #92400e;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📅 Propuesta de Cita</h1>
                </div>
                <div class='content'>
                    <h2>Hola {$cliente['nombre']},</h2>
                    <p>El agente <strong>{$agente['nombre']} {$agente['apellido']}</strong> te ha propuesto una cita para revisar la propiedad que te interesa.</p>
                    
                    <div class='proposal-box'>
                        <h3>¿Te parece bien esta propuesta?</h3>
                        <p>Por favor, revisa los detalles y confirma si aceptas o rechazas esta cita.</p>
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
                        <div class='detail-row'>
                            <span class='detail-label'>Teléfono:</span>
                            <span class='detail-value'>{$agente['telefono']}</span>
                        </div>
                    </div>
                    
                    <div class='action-buttons'>
                        <a href='{$acceptUrl}' class='btn btn-accept'>✅ Aceptar Cita</a>
                        <a href='{$rejectUrl}' class='btn btn-reject'>❌ Rechazar Cita</a>
                    </div>
                    
                    <div class='platform-notice'>
                        <strong>💡 ¿Sabías que puedes gestionar tus citas desde la plataforma?</strong><br>
                        Si estás conectado a " . APP_NAME . ", también verás una notificación en tiempo real.
                    </div>
                    
                    <p>Si tienes alguna pregunta, no dudes en contactar al agente directamente.</p>
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
     * Obtener template de texto para propuesta de cita
     * 
     * @param array $cita Datos de la cita
     * @param array $agente Datos del agente
     * @param array $cliente Datos del cliente
     * @return string Template de texto
     */
    private function getAppointmentProposalText($cita, $agente, $cliente) {
        $fecha = date('d/m/Y H:i', strtotime($cita['fecha_cita']));
        $tipoCita = ucfirst(str_replace('_', ' ', $cita['tipo_cita']));
        $lugar = $cita['lugar'] ?: 'No especificado';
        $acceptUrl = APP_URL . '/appointments/' . $cita['id'] . '/accept';
        $rejectUrl = APP_URL . '/appointments/' . $cita['id'] . '/reject';
        
        return "Propuesta de cita - " . APP_NAME . "

Hola {$cliente['nombre']},

El agente {$agente['nombre']} {$agente['apellido']} te ha propuesto una cita para revisar la propiedad que te interesa.

DETALLES DE LA CITA:
- Fecha y Hora: {$fecha}
- Tipo de Cita: {$tipoCita}
- Ubicación: {$lugar}
- Agente: {$agente['nombre']} {$agente['apellido']}
- Teléfono: {$agente['telefono']}

¿Te parece bien esta propuesta?

Para aceptar la cita, visita: {$acceptUrl}
Para rechazar la cita, visita: {$rejectUrl}

Si tienes alguna pregunta, no dudes en contactar al agente directamente.

Saludos,
El equipo de " . APP_NAME;
    }
} 
