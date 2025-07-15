<?php
/**
 * Helper de Email - PHPMailer
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este helper maneja el envío de emails usando PHPMailer
 * para una mejor compatibilidad y manejo de errores.
 */

// Incluir PHPMailer (asumiendo que está instalado vía Composer)
// Si no usas Composer, incluye los archivos manualmente:
// require_once 'PHPMailer/src/Exception.php';
// require_once 'PHPMailer/src/PHPMailer.php';
// require_once 'PHPMailer/src/SMTP.php';

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
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 8px 8px; }
                .button { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
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
                    
                    <div style='text-align: center;'>
                        <a href='{$verificationUrl}' class='button'>Verificar mi cuenta</a>
                    </div>
                    
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <p style='word-break: break-all; color: #2563eb;'>{$verificationUrl}</p>
                    
                    <p><strong>Importante:</strong> Este enlace expirará en 1 hora por motivos de seguridad.</p>
                    
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
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc2626; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 8px 8px; }
                .button { display: inline-block; background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
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
                    
                    <div style='text-align: center;'>
                        <a href='{$resetUrl}' class='button'>Restablecer mi contraseña</a>
                    </div>
                    
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <p style='word-break: break-all; color: #dc2626;'>{$resetUrl}</p>
                    
                    <p><strong>Importante:</strong> Este enlace expirará en 30 minutos por motivos de seguridad.</p>
                    
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
} 