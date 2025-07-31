<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/controlador/SolicitudController.php';

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $controller = new SolicitudController();
    $accion = $_POST['accion'] ?? '';
    $solicitud_id = intval($_POST['solicitud_id'] ?? 0);
    
    if (!$solicitud_id) {
        throw new Exception('ID de solicitud no válido');
    }
    
    switch ($accion) {
        case 'aprobar':
            $resultado = $controller->aprobarSolicitud($solicitud_id, $_SESSION['admin_id']);
            break;
            
        case 'rechazar':
            $motivo = trim($_POST['motivo'] ?? '');
            if (empty($motivo)) {
                throw new Exception('El motivo del rechazo es obligatorio');
            }
            $resultado = $controller->rechazarSolicitud($solicitud_id, $_SESSION['admin_id'], $motivo);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    error_log("Error procesando solicitud admin: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 