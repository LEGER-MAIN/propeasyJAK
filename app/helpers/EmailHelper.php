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
} 