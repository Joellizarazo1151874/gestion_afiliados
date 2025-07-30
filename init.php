<?php
/**
 * Archivo de inicialización del sistema
 * Se debe incluir al inicio de todos los archivos que requieran funcionalidad del sistema
 */

// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuración
require_once __DIR__ . '/config.php';

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');

// Configurar headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para validar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Función para redirigir si no está logueado
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Función para obtener datos del usuario logueado
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'] ?? null,
            'usuario' => $_SESSION['admin_usuario'] ?? null,
            'nombre' => $_SESSION['admin_nombre'] ?? null
        ];
    }
    return null;
}

// Función para mostrar mensajes de error/éxito
function showMessage($type, $message) {
    $alertClass = $type === 'success' ? 'alert-success' : 'alert-danger';
    $icon = $type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    return "<div class='alert $alertClass alert-dismissible fade show' role='alert'>
                <i class='fas fa-$icon me-2'></i>
                " . htmlspecialchars($message) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

// Función para registrar logs
function logActivity($action, $details = '') {
    if (!ENABLE_LOGGING) return;
    
    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $user = getCurrentUser();
    $userInfo = $user ? $user['usuario'] : 'Sistema';
    
    $logEntry = date('Y-m-d H:i:s') . " | $userInfo | $action | $details" . PHP_EOL;
    
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}

// Función para validar token CSRF (opcional)
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Configurar manejo de errores personalizado
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $errorMessage = "Error [$errno] $errstr en $errfile en la línea $errline";
    
    if (ENABLE_LOGGING) {
        logActivity('ERROR', $errorMessage);
    }
    
    if (ini_get('display_errors')) {
        echo "<div class='alert alert-danger'>$errorMessage</div>";
    }
    
    return true;
}

set_error_handler('customErrorHandler');

// Configurar manejo de excepciones personalizado
function customExceptionHandler($exception) {
    $errorMessage = "Excepción: " . $exception->getMessage() . " en " . $exception->getFile() . " línea " . $exception->getLine();
    
    if (ENABLE_LOGGING) {
        logActivity('EXCEPTION', $errorMessage);
    }
    
    if (ini_get('display_errors')) {
        echo "<div class='alert alert-danger'>$errorMessage</div>";
    }
}

set_exception_handler('customExceptionHandler');

// Función para limpiar sesión al cerrar
function cleanSession() {
    session_unset();
    session_destroy();
    session_start();
}

// Función para verificar timeout de sesión
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        cleanSession();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Verificar timeout de sesión automáticamente
if (isLoggedIn()) {
    checkSessionTimeout();
}
?> 