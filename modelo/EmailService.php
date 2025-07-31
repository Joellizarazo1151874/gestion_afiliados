<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config_email.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configurarMailer();
    }
    
    private function configurarMailer() {
        try {
            // Configuración del servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = SMTP_SECURE === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port = SMTP_PORT;
            $this->mailer->CharSet = EMAIL_CHARSET;
            
            // Configuración de debug
            if (EMAIL_DEBUG) {
                $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            }
            
            // Configuración del remitente
            $this->mailer->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
            
        } catch (Exception $e) {
            error_log("Error configurando PHPMailer: " . $e->getMessage());
            throw new Exception("Error en la configuración del servicio de email");
        }
    }
    
    public function enviarNotificacionAprobacion($datosSolicitud) {
        try {
            // Configurar destinatario
            $this->mailer->addAddress($datosSolicitud['email'], $datosSolicitud['nombre'] . ' ' . $datosSolicitud['apellidos']);
            
            // Configurar asunto y contenido
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '¡Tu solicitud ha sido APROBADA! - ' . ORG_NOMBRE;
            
            // Crear contenido del email
            $contenido = $this->generarContenidoAprobacion($datosSolicitud);
            $this->mailer->Body = $contenido;
            $this->mailer->AltBody = $this->generarContenidoTextoPlano($datosSolicitud);
            
            // Enviar email
            $this->mailer->send();
            
            // Log del envío exitoso
            $this->logEmailEnviado($datosSolicitud['email'], 'Aprobación', true);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email de aprobación: " . $e->getMessage());
            $this->logEmailEnviado($datosSolicitud['email'], 'Aprobación', false, $e->getMessage());
            return false;
        }
    }
    
    public function enviarNotificacionRechazo($datosSolicitud, $motivo) {
        try {
            // Configurar destinatario
            $this->mailer->addAddress($datosSolicitud['email'], $datosSolicitud['nombre'] . ' ' . $datosSolicitud['apellidos']);
            
            // Configurar asunto y contenido
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Información sobre tu solicitud - ' . ORG_NOMBRE;
            
            // Crear contenido del email
            $contenido = $this->generarContenidoRechazo($datosSolicitud, $motivo);
            $this->mailer->Body = $contenido;
            $this->mailer->AltBody = $this->generarContenidoRechazoTextoPlano($datosSolicitud, $motivo);
            
            // Enviar email
            $this->mailer->send();
            
            // Log del envío exitoso
            $this->logEmailEnviado($datosSolicitud['email'], 'Rechazo', true);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email de rechazo: " . $e->getMessage());
            $this->logEmailEnviado($datosSolicitud['email'], 'Rechazo', false, $e->getMessage());
            return false;
        }
    }
    
    private function generarContenidoAprobacion($datosSolicitud) {
        $fechaActual = date('d/m/Y H:i:s');
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>¡Solicitud Aprobada! - " . ORG_NOMBRE . "</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 20px;
                }
                
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                }
                
                .header {
                    background: linear-gradient(135deg, #FFD700 0%, #DC143C 50%, #32CD32 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                    position: relative;
                }
                
                .header::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"20\" cy=\"20\" r=\"2\" fill=\"rgba(255,255,255,0.1)\"/><circle cx=\"80\" cy=\"40\" r=\"1.5\" fill=\"rgba(255,255,255,0.1)\"/><circle cx=\"40\" cy=\"80\" r=\"1\" fill=\"rgba(255,255,255,0.1)\"/></svg>');
                    opacity: 0.3;
                }
                
                .header h1 {
                    font-size: 2.5em;
                    margin-bottom: 10px;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                    position: relative;
                    z-index: 1;
                }
                
                .header h2 {
                    font-size: 1.3em;
                    font-weight: 300;
                    opacity: 0.9;
                    position: relative;
                    z-index: 1;
                }
                
                .success-icon {
                    font-size: 80px;
                    margin: 30px 0;
                    text-align: center;
                    animation: bounce 2s infinite;
                }
                
                @keyframes bounce {
                    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                    40% { transform: translateY(-10px); }
                    60% { transform: translateY(-5px); }
                }
                
                .content {
                    padding: 40px 30px;
                    background: white;
                }
                
                .welcome-message {
                    font-size: 1.2em;
                    color: #2c3e50;
                    margin-bottom: 30px;
                    text-align: center;
                }
                
                .info-card {
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    border-radius: 15px;
                    padding: 25px;
                    margin: 25px 0;
                    border-left: 5px solid #32CD32;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                }
                
                .info-card h3 {
                    color: #2c3e50;
                    margin-bottom: 15px;
                    font-size: 1.3em;
                }
                
                .info-list {
                    list-style: none;
                    padding: 0;
                }
                
                .info-list li {
                    padding: 8px 0;
                    border-bottom: 1px solid #e9ecef;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .info-list li:last-child {
                    border-bottom: none;
                }
                
                .info-list strong {
                    color: #2c3e50;
                }
                
                .benefits-section {
                    background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
                    border-radius: 15px;
                    padding: 25px;
                    margin: 25px 0;
                }
                
                .benefits-section h3 {
                    color: #155724;
                    margin-bottom: 15px;
                    text-align: center;
                }
                
                .benefits-list {
                    list-style: none;
                    padding: 0;
                }
                
                .benefits-list li {
                    padding: 10px 0;
                    display: flex;
                    align-items: center;
                    color: #155724;
                }
                
                .benefits-list li::before {
                    content: '✅';
                    margin-right: 10px;
                    font-size: 1.2em;
                }
                
                .next-steps {
                    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
                    border-radius: 15px;
                    padding: 25px;
                    margin: 25px 0;
                }
                
                .next-steps h3 {
                    color: #856404;
                    margin-bottom: 15px;
                    text-align: center;
                }
                
                .next-steps ol {
                    padding-left: 20px;
                    color: #856404;
                }
                
                .next-steps li {
                    padding: 5px 0;
                }
                
                .contact-btn {
                    display: inline-block;
                    background: linear-gradient(135deg, #32CD32 0%, #228B22 100%);
                    color: white;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 25px;
                    margin: 20px 0;
                    text-align: center;
                    font-weight: bold;
                    box-shadow: 0 5px 15px rgba(50, 205, 50, 0.3);
                    transition: all 0.3s ease;
                }
                
                .contact-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(50, 205, 50, 0.4);
                }
                
                .footer {
                    background: #2c3e50;
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                
                .footer p {
                    margin: 5px 0;
                    opacity: 0.8;
                }
                
                .logo {
                    font-size: 1.5em;
                    font-weight: bold;
                    margin-bottom: 10px;
                    color: #FFD700;
                }
                
                @media (max-width: 600px) {
                    .email-container {
                        margin: 10px;
                        border-radius: 15px;
                    }
                    
                    .header {
                        padding: 30px 20px;
                    }
                    
                    .header h1 {
                        font-size: 2em;
                    }
                    
                    .content {
                        padding: 30px 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>🎉 ¡FELICITACIONES!</h1>
                    <h2>Tu solicitud ha sido APROBADA</h2>
                </div>
                
                <div class='content'>
                    <div class='success-icon'>🎊</div>
                    
                    <div class='welcome-message'>
                        <h3>Estimado/a <strong>{$datosSolicitud['nombre']} {$datosSolicitud['apellidos']}</strong></h3>
                        <p>¡Nos complace informarte que tu solicitud de afiliación a <strong>" . ORG_NOMBRE . "</strong> ha sido <strong>APROBADA</strong> exitosamente!</p>
                    </div>
                    
                    <div class='info-card'>
                        <h3>📋 Detalles de tu solicitud</h3>
                        <ul class='info-list'>
                            <li>
                                <span>Número de solicitud:</span>
                                <strong>{$datosSolicitud['numero_solicitud']}</strong>
                            </li>
                            <li>
                                <span>Fecha de aprobación:</span>
                                <strong>{$fechaActual}</strong>
                            </li>
                            <li>
                                <span>Tipo de discapacidad:</span>
                                <strong>{$datosSolicitud['tipo_discapacidad']}</strong>
                            </li>
                            <li>
                                <span>Porcentaje de discapacidad:</span>
                                <strong>{$datosSolicitud['porcentaje_discapacidad']}%</strong>
                            </li>
                        </ul>
                    </div>
                    
                    <div class='benefits-section'>
                        <h3>🌟 Beneficios como miembro aprobado</h3>
                        <ul class='benefits-list'>
                            <li>Programas de apoyo y asistencia especializada</li>
                            <li>Actividades y eventos de la organización</li>
                            <li>Recursos y servicios especializados</li>
                            <li>Red de apoyo y comunidad inclusiva</li>
                            <li>Información actualizada sobre derechos y beneficios</li>
                            <li>Acceso a talleres y capacitaciones</li>
                            <li>Apoyo en trámites y gestiones</li>
                        </ul>
                    </div>
                    
                    <div class='next-steps'>
                        <h3>📝 Próximos pasos</h3>
                        <ol>
                            <li>Recibirás información adicional sobre tu membresía</li>
                            <li>Te contactaremos para coordinar tu participación en actividades</li>
                            <li>Podrás acceder a los recursos exclusivos para miembros</li>
                            <li>Participarás en nuestra comunidad de apoyo</li>
                        </ol>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='mailto:" . ORG_EMAIL . "' class='contact-btn'>
                            📧 Contactar a la organización
                        </a>
                    </div>
                    
                    <p style='text-align: center; margin-top: 30px; color: #2c3e50; font-size: 1.1em;'>
                        Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos.
                    </p>
                    
                    <p style='text-align: center; margin-top: 20px; color: #32CD32; font-size: 1.2em; font-weight: bold;'>
                        ¡Bienvenido/a a nuestra familia! 🏠
                    </p>
                    
                    <p style='text-align: center; margin-top: 30px; color: #666;'>
                        Saludos cordiales,<br>
                        <strong>Equipo de " . ORG_NOMBRE . "</strong>
                    </p>
                </div>
                
                <div class='footer'>
                    <div class='logo'>" . ORG_NOMBRE . "</div>
                    <p>Este es un mensaje automático, por favor no respondas directamente a este correo.</p>
                    <p>Para consultas: " . ORG_EMAIL . "</p>
                    <p>Fecha de envío: {$fechaActual}</p>
                    <p style='margin-top: 15px; font-size: 0.9em; opacity: 0.7;'>
                        © " . date('Y') . " " . ORG_NOMBRE . ". Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function generarContenidoTextoPlano($datosSolicitud) {
        $fechaActual = date('d/m/Y H:i:s');
        
        return "
        ¡FELICITACIONES! Tu solicitud ha sido APROBADA
        
        Estimado/a {$datosSolicitud['nombre']} {$datosSolicitud['apellidos']},
        
        Nos complace informarte que tu solicitud de afiliación a " . ORG_NOMBRE . " ha sido APROBADA exitosamente.
        
        Detalles de tu solicitud:
        - Número de solicitud: {$datosSolicitud['numero_solicitud']}
        - Fecha de aprobación: {$fechaActual}
        - Tipo de discapacidad: {$datosSolicitud['tipo_discapacidad']}
        - Porcentaje de discapacidad: {$datosSolicitud['porcentaje_discapacidad']}%
        
        Como miembro aprobado, ahora tienes acceso a programas de apoyo, actividades, recursos especializados y nuestra red de apoyo.
        
        Próximos pasos:
        1. Recibirás información adicional sobre tu membresía
        2. Te contactaremos para coordinar tu participación
        3. Podrás acceder a recursos exclusivos para miembros
        
        Si tienes preguntas, contacta a: " . ORG_EMAIL . "
        
        ¡Bienvenido/a a nuestra familia!
        
        Equipo de " . ORG_NOMBRE . "
        Fecha: {$fechaActual}";
    }
    
    private function generarContenidoRechazo($datosSolicitud, $motivo) {
        $fechaActual = date('d/m/Y H:i:s');
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Información sobre tu solicitud - " . ORG_NOMBRE . "</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 20px;
                }
                
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                }
                
                .header {
                    background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                    position: relative;
                }
                
                .header::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"20\" cy=\"20\" r=\"2\" fill=\"rgba(255,255,255,0.1)\"/><circle cx=\"80\" cy=\"40\" r=\"1.5\" fill=\"rgba(255,255,255,0.1)\"/><circle cx=\"40\" cy=\"80\" r=\"1\" fill=\"rgba(255,255,255,0.1)\"/></svg>');
                    opacity: 0.3;
                }
                
                .header h1 {
                    font-size: 2.5em;
                    margin-bottom: 10px;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                    position: relative;
                    z-index: 1;
                }
                
                .header h2 {
                    font-size: 1.3em;
                    font-weight: 300;
                    opacity: 0.9;
                    position: relative;
                    z-index: 1;
                }
                
                .content {
                    padding: 40px 30px;
                    background: white;
                }
                
                .welcome-message {
                    font-size: 1.2em;
                    color: #2c3e50;
                    margin-bottom: 30px;
                    text-align: center;
                }
                
                .info-card {
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    border-radius: 15px;
                    padding: 25px;
                    margin: 25px 0;
                    border-left: 5px solid #DC143C;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                }
                
                .info-card h3 {
                    color: #2c3e50;
                    margin-bottom: 15px;
                    font-size: 1.3em;
                }
                
                .info-list {
                    list-style: none;
                    padding: 0;
                }
                
                .info-list li {
                    padding: 8px 0;
                    border-bottom: 1px solid #e9ecef;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .info-list li:last-child {
                    border-bottom: none;
                }
                
                .info-list strong {
                    color: #2c3e50;
                }
                
                .rejection-section {
                    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
                    border-radius: 15px;
                    padding: 25px;
                    margin: 25px 0;
                    border-left: 5px solid #DC143C;
                }
                
                .rejection-section h3 {
                    color: #c53030;
                    margin-bottom: 15px;
                    text-align: center;
                }
                
                .rejection-reason {
                    background: white;
                    border-radius: 10px;
                    padding: 20px;
                    margin: 15px 0;
                    border: 2px solid #fed7d7;
                }
                
                .next-steps {
                    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
                    border-radius: 15px;
                    padding: 25px;
                    margin: 25px 0;
                }
                
                .next-steps h3 {
                    color: #856404;
                    margin-bottom: 15px;
                    text-align: center;
                }
                
                .next-steps ul {
                    list-style: none;
                    padding: 0;
                }
                
                .next-steps li {
                    padding: 10px 0;
                    display: flex;
                    align-items: center;
                    color: #856404;
                }
                
                .next-steps li::before {
                    content: '🔄';
                    margin-right: 10px;
                    font-size: 1.2em;
                }
                
                .contact-btn {
                    display: inline-block;
                    background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
                    color: white;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 25px;
                    margin: 20px 0;
                    text-align: center;
                    font-weight: bold;
                    box-shadow: 0 5px 15px rgba(220, 20, 60, 0.3);
                    transition: all 0.3s ease;
                }
                
                .contact-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(220, 20, 60, 0.4);
                }
                
                .footer {
                    background: #2c3e50;
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                
                .footer p {
                    margin: 5px 0;
                    opacity: 0.8;
                }
                
                .logo {
                    font-size: 1.5em;
                    font-weight: bold;
                    margin-bottom: 10px;
                    color: #FFD700;
                }
                
                @media (max-width: 600px) {
                    .email-container {
                        margin: 10px;
                        border-radius: 15px;
                    }
                    
                    .header {
                        padding: 30px 20px;
                    }
                    
                    .header h1 {
                        font-size: 2em;
                    }
                    
                    .content {
                        padding: 30px 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>📋 Información sobre tu solicitud</h1>
                </div>
                
                <div class='content'>
                    <div class='welcome-message'>
                        <h3>Estimado/a <strong>{$datosSolicitud['nombre']} {$datosSolicitud['apellidos']}</strong></h3>
                        <p>Hemos revisado tu solicitud de afiliación y lamentamos informarte que no ha sido aprobada en esta oportunidad.</p>
                    </div>
                    
                    <div class='info-card'>
                        <h3>📋 Detalles de tu solicitud</h3>
                        <ul class='info-list'>
                            <li>
                                <span>Número de solicitud:</span>
                                <strong>{$datosSolicitud['numero_solicitud']}</strong>
                            </li>
                            <li>
                                <span>Fecha de revisión:</span>
                                <strong>{$fechaActual}</strong>
                            </li>
                            <li>
                                <span>Estado:</span>
                                <strong style='color: #DC143C;'>Rechazada</strong>
                            </li>
                            <li>
                                <span>Tipo de discapacidad:</span>
                                <strong>{$datosSolicitud['tipo_discapacidad']}</strong>
                            </li>
                        </ul>
                    </div>
                    
                    <div class='rejection-section'>
                        <h3>📝 Motivo del rechazo</h3>
                        <div class='rejection-reason'>
                            <p style='color: #c53030; font-style: italic; margin: 0;'>{$motivo}</p>
                        </div>
                    </div>
                    
                    <div class='next-steps'>
                        <h3>🔄 ¿Qué puedes hacer?</h3>
                        <ul>
                            <li>Revisar y corregir la información según el motivo indicado</li>
                            <li>Completar la documentación faltante si es necesario</li>
                            <li>Presentar una nueva solicitud cuando estés listo/a</li>
                            <li>Contactarnos para aclarar cualquier duda</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='mailto:" . ORG_EMAIL . "' class='contact-btn'>
                            📧 Contactar a la organización
                        </a>
                    </div>
                    
                    <p style='text-align: center; margin-top: 30px; color: #2c3e50; font-size: 1.1em;'>
                        Si tienes alguna pregunta sobre esta decisión, no dudes en contactarnos.
                    </p>
                    
                    <p style='text-align: center; margin-top: 20px; color: #666;'>
                        Atentamente,<br>
                        <strong>Equipo de " . ORG_NOMBRE . "</strong>
                    </p>
                </div>
                
                <div class='footer'>
                    <div class='logo'>" . ORG_NOMBRE . "</div>
                    <p>Este es un mensaje automático, por favor no respondas directamente a este correo.</p>
                    <p>Para consultas: " . ORG_EMAIL . "</p>
                    <p>Fecha de envío: {$fechaActual}</p>
                    <p style='margin-top: 15px; font-size: 0.9em; opacity: 0.7;'>
                        © " . date('Y') . " " . ORG_NOMBRE . ". Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function generarContenidoRechazoTextoPlano($datosSolicitud, $motivo) {
        $fechaActual = date('d/m/Y H:i:s');
        
        return "
        Información sobre tu solicitud - " . ORG_NOMBRE . "
        
        Estimado/a {$datosSolicitud['nombre']} {$datosSolicitud['apellidos']},
        
        Hemos revisado tu solicitud de afiliación y lamentamos informarte que no ha sido aprobada en esta oportunidad.
        
        Detalles de tu solicitud:
        - Número de solicitud: {$datosSolicitud['numero_solicitud']}
        - Fecha de revisión: {$fechaActual}
        - Estado: Rechazada
        - Tipo de discapacidad: {$datosSolicitud['tipo_discapacidad']}
        
        Motivo del rechazo:
        {$motivo}
        
        ¿Qué puedes hacer?
        1. Revisar y corregir la información según el motivo indicado
        2. Completar la documentación faltante si es necesario
        3. Presentar una nueva solicitud cuando estés listo/a
        4. Contactarnos para aclarar cualquier duda
        
        Si tienes alguna pregunta sobre esta decisión, no dudes en contactarnos.
        
        Para consultas: " . ORG_EMAIL . "
        
        Atentamente,
        Equipo de " . ORG_NOMBRE . "
        Fecha: {$fechaActual}";
    }
    
    private function logEmailEnviado($email, $tipo, $exitoso, $error = null) {
        if (!EMAIL_LOG_ENABLED) {
            return;
        }
        
        $logFile = dirname(__DIR__) . '/' . EMAIL_LOG_FILE;
        $timestamp = date('Y-m-d H:i:s');
        $status = $exitoso ? 'EXITOSO' : 'ERROR';
        $errorMsg = $error ? " - Error: $error" : '';
        
        $logEntry = "[$timestamp] [$status] [$tipo] Enviado a: $email$errorMsg\n";
        
        // Crear directorio de logs si no existe
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
?> 