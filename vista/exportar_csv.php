<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/UsuarioController.php';

$usuarioController = new UsuarioController();

// Obtener filtros de la URL
$filtros = [
    'busqueda' => $_GET['busqueda'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'tipo_discapacidad' => $_GET['tipo_discapacidad'] ?? '',
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
];

// Obtener usuarios
$usuarios = $usuarioController->obtenerUsuarios($filtros);

// Configurar headers para descarga CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="usuarios_' . date('Y-m-d_H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

// Crear el archivo CSV
$output = fopen('php://output', 'w');

// Agregar BOM para UTF-8 (para que Excel reconozca caracteres especiales)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Encabezados
$headers = [
    'Número Asociado',
    'Nombre',
    'Apellidos',
    'Fecha Nacimiento',
    'Género',
    'Tipo Discapacidad',
    '% Discapacidad',
    'Teléfono',
    'Email',
    'Dirección',
    'Ciudad',
    'Código Postal',
    'Fecha Afiliación',
    'Estado',
    'Observaciones'
];

fputcsv($output, $headers);

// Datos
foreach ($usuarios as $usuario) {
    $row = [
        $usuario['numero_asociado'],
        $usuario['nombre'],
        $usuario['apellidos'],
        $usuario['fecha_nacimiento'],
        $usuario['genero'],
        $usuario['tipo_discapacidad'],
        $usuario['porcentaje_discapacidad'],
        $usuario['telefono'],
        $usuario['email'],
        $usuario['direccion'],
        $usuario['ciudad'],
        $usuario['codigo_postal'],
        $usuario['fecha_afiliacion'],
        $usuario['estado'],
        $usuario['observaciones']
    ];
    
    fputcsv($output, $row);
}

fclose($output);
exit();
?> 