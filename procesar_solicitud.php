<?php
require_once 'config.php';
require_once 'controlador/SolicitudController.php';

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: solicitud_afiliacion.php?error=Método no permitido');
    exit;
}

try {
    $controller = new SolicitudController();
    
    // Preparar datos de la solicitud
    $datos = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'apellidos' => trim($_POST['apellidos'] ?? ''),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'genero' => $_POST['genero'] ?? '',
        'tipo_discapacidad' => $_POST['tipo_discapacidad'] ?? '',
        'porcentaje_discapacidad' => intval($_POST['porcentaje_discapacidad'] ?? 0),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'ciudad' => trim($_POST['ciudad'] ?? ''),
        'codigo_postal' => trim($_POST['codigo_postal'] ?? ''),
        'observaciones' => trim($_POST['observaciones'] ?? '')
    ];
    
    // Procesar documentos si existen
    $archivos = [];
    if (isset($_FILES['documentos']) && is_array($_FILES['documentos']['name'])) {
        foreach ($_FILES['documentos']['name'] as $tipo => $nombre) {
            if ($_FILES['documentos']['error'][$tipo] === UPLOAD_ERR_OK) {
                $archivos[$tipo] = [
                    'name' => $_FILES['documentos']['name'][$tipo],
                    'type' => $_FILES['documentos']['type'][$tipo],
                    'tmp_name' => $_FILES['documentos']['tmp_name'][$tipo],
                    'error' => $_FILES['documentos']['error'][$tipo],
                    'size' => $_FILES['documentos']['size'][$tipo]
                ];
            }
        }
    }
    
    // Procesar solicitud
    $resultado = $controller->procesarSolicitud($datos, $archivos);
    
    if ($resultado['success']) {
        // Obtener número de solicitud para mostrar al usuario
        $solicitud = $controller->obtenerSolicitud($resultado['solicitud_id']);
        $numero_solicitud = $solicitud['numero_solicitud'] ?? 'N/A';
        
        // Redirigir con mensaje de éxito
        header("Location: solicitud_afiliacion.php?success=1&numero=" . urlencode($numero_solicitud));
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: solicitud_afiliacion.php?error=" . urlencode($resultado['message']));
        exit;
    }
    
} catch (Exception $e) {
    // Log del error para debugging
    error_log("Error procesando solicitud: " . $e->getMessage());
    
    // Redirigir con mensaje de error genérico
    header("Location: solicitud_afiliacion.php?error=Error interno del servidor. Por favor intenta nuevamente.");
    exit;
}
?> 