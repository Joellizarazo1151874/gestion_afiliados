<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/Administrador.php';

class AuthController {
    private $administrador;

    public function __construct() {
        $this->administrador = new Administrador();
    }

    // Procesar login
    public function login($usuario, $password) {
        if (empty($usuario) || empty($password)) {
            return ['success' => false, 'message' => 'Usuario y contraseña son requeridos'];
        }

        $admin = $this->administrador->verificarLogin($usuario, $password);
        
        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_usuario'] = $admin['usuario'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            $_SESSION['logged_in'] = true;
            
            return ['success' => true, 'message' => 'Login exitoso'];
        } else {
            return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
        }
    }

    // Verificar si está logueado
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada'];
    }

    // Obtener datos del administrador logueado
    public function getCurrentAdmin() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'usuario' => $_SESSION['admin_usuario'],
                'nombre' => $_SESSION['admin_nombre']
            ];
        }
        return null;
    }

    // Requerir autenticación
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
}
?> 