<?php
require_once 'conexion.php';

class Administrador {
    private $conexion;

    public function __construct() {
        $conexion = new Conexion();
        $this->conexion = $conexion->conectar();
    }

    // Verificar credenciales de login
    public function verificarLogin($usuario, $password) {
        $sql = "SELECT * FROM administradores WHERE usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    // Obtener administrador por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM administradores WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cambiar contraseña
    public function cambiarPassword($id, $nueva_password) {
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $sql = "UPDATE administradores SET password = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$password_hash, $id]);
    }
}
?> 