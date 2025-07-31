<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/UsuarioController.php';
require_once '../controlador/DocumentoController.php';

$usuarioController = new UsuarioController();
$documentoController = new DocumentoController();

// Verificar si es una petición POST o GET con acción
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear o actualizar usuario
    $datos = [
        'numero_asociado' => $_POST['numero_asociado'] ?? '',
        'nombre' => $_POST['nombre'] ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'genero' => $_POST['genero'] ?? '',
        'tipo_discapacidad' => $_POST['tipo_discapacidad'] ?? '',
        'porcentaje_discapacidad' => $_POST['porcentaje_discapacidad'] ?? null,
        'telefono' => $_POST['telefono'] ?? '',
        'email' => $_POST['email'] ?? '',
        'direccion' => $_POST['direccion'] ?? '',
        'ciudad' => $_POST['ciudad'] ?? '',
        'codigo_postal' => $_POST['codigo_postal'] ?? '',
        'fecha_afiliacion' => $_POST['fecha_afiliacion'] ?? '',
        'estado' => $_POST['estado'] ?? 'Activo',
        'observaciones' => $_POST['observaciones'] ?? ''
    ];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Actualizar usuario existente
        $resultado = $usuarioController->actualizarUsuario($_POST['id'], $datos);
    } else {
        // Crear nuevo usuario
        $resultado = $usuarioController->crearUsuario($datos);
        
        // Si el usuario se creó exitosamente y hay documentos, procesarlos
        if ($resultado['success'] && isset($_FILES['documentos']) && !empty($_FILES['documentos']['name'][0])) {
            $usuario_id = $resultado['usuario_id'] ?? null;
            if ($usuario_id) {
                $documentos_resultado = $documentoController->procesarCargaDocumentos($usuario_id, $_FILES['documentos']);
                if (!$documentos_resultado['exito']) {
                    $resultado['message'] .= ' - ' . $documentos_resultado['mensaje'];
                } else {
                    $resultado['message'] .= ' - ' . $documentos_resultado['mensaje'];
                }
            }
        }
    }

    // Redirigir con mensaje
    $mensaje = $resultado['success'] ? 'success' : 'error';
    $texto = urlencode($resultado['message']);
    header("Location: index.php?mensaje=$mensaje&texto=$texto");
    exit();

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
    // Acciones específicas
    $accion = $_GET['accion'];
    $id = $_GET['id'] ?? null;

    if ($accion === 'eliminar' && $id) {
        $resultado = $usuarioController->eliminarUsuario($id);
        $mensaje = $resultado['success'] ? 'success' : 'error';
        $texto = urlencode($resultado['message']);
        header("Location: index.php?mensaje=$mensaje&texto=$texto");
        exit();
    }
}

// Si no es una petición válida, redirigir al dashboard
header('Location: index.php');
exit();
?> 