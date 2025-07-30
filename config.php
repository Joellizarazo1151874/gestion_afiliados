<?php
/**
 * Archivo de configuración del sistema
 * Modificar estos valores según las necesidades de la organización
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_usuarios');

// Configuración de la organización
define('ORG_NOMBRE', 'Familia unida por la discapacidad');
define('ORG_DESCRIPCION', 'Sistema de Gestión de Asociados');
define('ORG_EMAIL', 'admin@familiaunida.com');

// Configuración del sistema
define('SYS_VERSION', '1.0.0');
define('SYS_TITLE', 'Gestión de Usuarios - ' . ORG_NOMBRE);

// Configuración de paginación
define('ITEMS_PER_PAGE', 25);

// Configuración de exportación
define('EXPORT_FILENAME_PREFIX', 'usuarios_');

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos
define('MAX_LOGIN_ATTEMPTS', 5);

// Configuración de validación
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes

// Configuración de tipos de discapacidad predefinidos
$TIPOS_DISCAPACIDAD_PREDEFINIDOS = [
    'Discapacidad física',
    'Discapacidad visual',
    'Discapacidad auditiva',
    'Discapacidad intelectual',
    'Discapacidad psicosocial',
    'Discapacidad múltiple',
    'Discapacidad del habla',
    'Discapacidad motora',
    'Discapacidad neurológica',
    'Otra'
];

// Configuración de estados de usuario
$ESTADOS_USUARIO = [
    'Activo' => 'Usuario activo en la organización',
    'Inactivo' => 'Usuario temporalmente inactivo',
    'Suspendido' => 'Usuario suspendido temporalmente'
];

// Configuración de géneros
$GENEROS = [
    'Masculino',
    'Femenino',
    'Otro'
];

// Configuración de colores del tema
$TEMA_COLORES = [
    'primary' => '#FFD700',    // Amarillo dorado
    'secondary' => '#DC143C',  // Rojo mexicano
    'success' => '#228B22',    // Verde bosque
    'warning' => '#FF8C00',    // Naranja oscuro
    'danger' => '#B22222',     // Rojo fuego
    'info' => '#32CD32'        // Verde lima
];

// Configuración de notificaciones
define('ENABLE_EMAIL_NOTIFICATIONS', false);
define('ENABLE_SMS_NOTIFICATIONS', false);

// Configuración de logs
define('ENABLE_LOGGING', true);
define('LOG_FILE', 'logs/system.log');

// Configuración de backup
define('AUTO_BACKUP', false);
define('BACKUP_FREQUENCY', 'daily'); // daily, weekly, monthly

// Configuración de reportes
define('ENABLE_REPORTS', true);
define('REPORTS_PATH', 'reports/');

// Configuración de idioma
define('DEFAULT_LANGUAGE', 'es');
define('AVAILABLE_LANGUAGES', ['es', 'en']);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (solo para desarrollo)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Configuración de seguridad adicional
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Función para obtener configuración
function getConfig($key, $default = null) {
    global $TIPOS_DISCAPACIDAD_PREDEFINIDOS, $ESTADOS_USUARIO, $GENEROS, $TEMA_COLORES;
    
    $configs = [
        'tipos_discapacidad' => $TIPOS_DISCAPACIDAD_PREDEFINIDOS,
        'estados_usuario' => $ESTADOS_USUARIO,
        'generos' => $GENEROS,
        'colores_tema' => $TEMA_COLORES
    ];
    
    return $configs[$key] ?? $default;
}

// Función para validar configuración
function validateConfig() {
    $errors = [];
    
    // Validar conexión a base de datos
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $errors[] = "Error de conexión a la base de datos: " . $e->getMessage();
    }
    
    // Validar directorios necesarios
    $directories = ['logs', 'reports', 'uploads'];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $errors[] = "No se pudo crear el directorio: $dir";
            }
        }
    }
    
    return $errors;
}

// Función para obtener información del sistema
function getSystemInfo() {
    return [
        'version' => SYS_VERSION,
        'php_version' => PHP_VERSION,
        'mysql_version' => defined('DB_HOST') ? 'Configurado' : 'No configurado',
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time')
    ];
}
?> 