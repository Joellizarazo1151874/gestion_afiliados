# Configuración de Email - Sistema de Gestión de Usuarios

## Descripción
Este sistema incluye funcionalidad para enviar notificaciones por email cuando se aprueba una solicitud de afiliación. El sistema utiliza PHPMailer para el envío de correos electrónicos.

## Requisitos Previos

### 1. Instalar Dependencias
Ejecuta el siguiente comando en la raíz del proyecto para instalar PHPMailer:

```bash
composer install
```

### 2. Configurar Gmail (Recomendado)

#### Paso 1: Habilitar Verificación en Dos Pasos
1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Ve a la sección "Seguridad"
3. Habilita "Verificación en dos pasos" si no está habilitada

#### Paso 2: Generar Contraseña de Aplicación
1. En la misma sección "Seguridad"
2. Busca "Contraseñas de aplicación"
3. Selecciona "Otra" y dale un nombre como "Sistema de Gestión"
4. Copia la contraseña generada (16 caracteres)

#### Paso 3: Configurar el Sistema
1. Abre el archivo `config_email.php`
2. Reemplaza `'tu_contraseña_de_aplicacion'` con la contraseña generada en el paso anterior
3. Guarda el archivo

## Configuración del Archivo config_email.php

```php
// Credenciales del email
define('SMTP_USERNAME', 'familiaunidaporladiscapacidad@gmail.com');
define('SMTP_PASSWORD', 'tu_contraseña_de_aplicacion'); // CAMBIAR ESTO
```

## Funcionalidad Implementada

### Notificación de Aprobación
Cuando un administrador aprueba una solicitud de afiliación, el sistema automáticamente:

1. ✅ Aprueba la solicitud en la base de datos
2. ✅ Crea el usuario en el sistema
3. ✅ Envía un email de notificación al solicitante
4. ✅ Registra el envío en los logs

### Contenido del Email
El email incluye:
- Felicitaciones por la aprobación
- Detalles de la solicitud (número, fecha, tipo de discapacidad)
- Información sobre los beneficios de ser miembro
- Próximos pasos
- Información de contacto

## Archivos Modificados

### Nuevos Archivos Creados:
- `modelo/EmailService.php` - Servicio de envío de emails
- `config_email.php` - Configuración específica de email
- `README_EMAIL.md` - Este archivo de documentación

### Archivos Modificados:
- `composer.json` - Agregada dependencia de PHPMailer
- `controlador/SolicitudController.php` - Integrado envío de email en aprobación

## Logs de Email

Los envíos de email se registran en:
```
logs/emails_sent.log
```

Formato del log:
```
[2024-01-15 10:30:45] [EXITOSO] [Aprobación] Enviado a: usuario@ejemplo.com
[2024-01-15 10:35:12] [ERROR] [Aprobación] Enviado a: usuario2@ejemplo.com - Error: SMTP connect() failed
```

## Solución de Problemas

### Error: "SMTP connect() failed"
- Verifica que la verificación en dos pasos esté habilitada
- Asegúrate de usar la contraseña de aplicación correcta
- Verifica que el puerto 587 esté abierto en tu servidor

### Error: "Authentication failed"
- La contraseña de aplicación es incorrecta
- Regenera una nueva contraseña de aplicación

### Error: "Could not instantiate mail function"
- Verifica que PHPMailer esté instalado correctamente
- Ejecuta `composer install` nuevamente

## Configuración de Debug

Para habilitar el debug de SMTP, cambia en `config_email.php`:

```php
define('EMAIL_DEBUG', true); // Cambiar a true para debugging
```

## Notas Importantes

1. **Seguridad**: Nunca uses tu contraseña normal de Gmail, siempre usa contraseñas de aplicación
2. **Límites**: Gmail tiene límites de envío (500 emails por día para cuentas gratuitas)
3. **Logs**: Los logs se guardan automáticamente para auditoría
4. **Fallback**: Si el email falla, el proceso de aprobación continúa normalmente

## Soporte

Si tienes problemas con la configuración del email:
1. Revisa los logs en `logs/emails_sent.log`
2. Verifica la configuración en `config_email.php`
3. Asegúrate de que PHPMailer esté instalado correctamente 