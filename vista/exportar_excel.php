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

// Exportar a Excel
$usuarioController->exportarExcel($filtros);
?> 