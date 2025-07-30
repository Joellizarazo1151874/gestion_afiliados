<?php
/**
 * Archivo de entrada principal
 * Redirige automáticamente al sistema de login
 */

// Verificar si el usuario ya está logueado
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    header('Location: vista/index.php');
    exit();
}

// Si no está logueado, redirigir al login
header('Location: vista/login.php');
exit();
?>
