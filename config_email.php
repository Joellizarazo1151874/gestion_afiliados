<?php
/**
 * Configuración de Email para el sistema
 * 
 * IMPORTANTE: Para usar Gmail, necesitas:
 * 1. Habilitar la verificación en dos pasos en tu cuenta de Gmail
 * 2. Generar una contraseña de aplicación específica para esta aplicación
 * 3. Usar esa contraseña de aplicación en lugar de tu contraseña normal
 */

// Configuración del servidor SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // tls o ssl

// Credenciales del email
define('SMTP_USERNAME', 'ginussmartpark@gmail.com');
define('SMTP_PASSWORD', 'zdhd jlrj breu iirg'); // REEMPLAZAR CON LA NUEVA CONTRASEÑA DE 16 CARACTERES

// Configuración del remitente
define('EMAIL_FROM_ADDRESS', 'ginussmartpark@gmail.com');
define('EMAIL_FROM_NAME', ORG_NOMBRE);

// Configuración adicional
define('EMAIL_CHARSET', 'UTF-8');
define('EMAIL_DEBUG', false); // Cambiar a true para debugging

// Configuración de logs
define('EMAIL_LOG_ENABLED', true);
define('EMAIL_LOG_FILE', 'logs/emails_sent.log');

/**
 * INSTRUCCIONES PARA CONFIGURAR GMAIL:
 * 
 * 1. Ve a tu cuenta de Google: https://myaccount.google.com/
 * 2. Ve a "Seguridad"
 * 3. Habilita "Verificación en dos pasos" si no está habilitada
 * 4. Ve a "Contraseñas de aplicación"
 * 5. Selecciona "Otra" y dale un nombre como "Sistema de Gestión"
 * 6. Copia la contraseña generada (16 caracteres)
 * 7. Reemplaza 'tu_contraseña_de_aplicacion' en esta línea:
 *    define('SMTP_PASSWORD', 'tu_contraseña_de_aplicacion');
 * 8. Guarda este archivo
 * 
 * NOTA: La contraseña de aplicación es diferente a tu contraseña normal de Gmail
 * y es específica para esta aplicación.
 */
?> 