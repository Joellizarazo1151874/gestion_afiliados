<?php
// Deshabilitar la salida de errores para evitar que se mezclen con el archivo
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_id'])) {
    // Log para debugging
    error_log("Descarga de documento: Usuario no autorizado. Session: " . print_r($_SESSION, true));
    http_response_code(401);
    echo 'No autorizado - Sesión no válida';
    exit;
}

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/modelo/Documento.php';

$documento_id = intval($_GET['id'] ?? 0);

if (!$documento_id) {
    http_response_code(400);
    echo 'ID de documento no válido';
    exit;
}

try {
    $documento = new Documento();
    
    // Obtener información del documento usando la conexión directamente
    require_once dirname(__DIR__) . '/modelo/conexion.php';
    $conexion = new Conexion();
    $pdo = $conexion->conectar();
    
    $sql = "SELECT * FROM documentos_solicitudes WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documento_id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doc) {
        http_response_code(404);
        echo 'Documento no encontrado';
        exit;
    }
    
    $ruta_archivo = $doc['ruta_archivo'];
    
    // Convertir ruta relativa a absoluta
    if (!file_exists($ruta_archivo)) {
        $ruta_archivo = dirname(__DIR__) . '/' . $ruta_archivo;
    }
    
    if (!file_exists($ruta_archivo)) {
        error_log("Descarga de documento: Archivo no encontrado: " . $ruta_archivo);
        http_response_code(404);
        echo 'Archivo no encontrado en el servidor: ' . $ruta_archivo;
        exit;
    }
    
    // Obtener información del archivo
    $nombre_archivo = $doc['nombre_archivo'];
    $tipo_mime = mime_content_type($ruta_archivo);
    $tamano = filesize($ruta_archivo);
    
    // Limpiar cualquier salida previa y buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Configurar headers para descarga
    header('Content-Type: ' . $tipo_mime);
    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    header('Content-Length: ' . $tamano);
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Accept-Ranges: bytes');
    
    // Asegurar que no hay salida antes del archivo
    if (ob_get_level()) {
        ob_clean();
    }
    flush();
    
    // Leer y enviar el archivo en chunks para archivos grandes
    $handle = fopen($ruta_archivo, 'rb');
    if ($handle) {
        while (!feof($handle)) {
            echo fread($handle, 8192); // Leer en chunks de 8KB
            flush();
        }
        fclose($handle);
    } else {
        // Fallback a readfile si fopen falla
        readfile($ruta_archivo);
    }
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error al descargar el documento: ' . $e->getMessage();
    exit;
}
?> 