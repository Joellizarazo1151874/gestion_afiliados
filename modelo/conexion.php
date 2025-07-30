<?php
class Conexion {
    private $host;
    private $usuario;
    private $password;
    private $base_datos;
    private $conexion;

    public function __construct() {
        // Usar constantes de configuración si están definidas, sino usar valores por defecto
        $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->usuario = defined('DB_USER') ? DB_USER : 'root';
        $this->password = defined('DB_PASS') ? DB_PASS : '';
        $this->base_datos = defined('DB_NAME') ? DB_NAME : 'gestion_usuarios';
    }

    public function conectar() {
        try {
            $this->conexion = new PDO("mysql:host=$this->host;dbname=$this->base_datos;charset=utf8", 
                                     $this->usuario, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            return null;
        }
    }

    public function cerrar() {
        $this->conexion = null;
    }
}
?>
