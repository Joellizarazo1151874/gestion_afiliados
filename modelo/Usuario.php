<?php
require_once 'conexion.php';

class Usuario {
    private $conexion;

    public function __construct() {
        $conexion = new Conexion();
        $this->conexion = $conexion->conectar();
    }

    // Obtener todos los usuarios con filtros
    public function obtenerUsuarios($filtros = []) {
        $sql = "SELECT * FROM usuarios WHERE 1=1";
        $params = [];

        // Aplicar filtros
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (nombre LIKE ? OR apellidos LIKE ? OR numero_asociado LIKE ?)";
            $busqueda = "%" . $filtros['busqueda'] . "%";
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['tipo_discapacidad'])) {
            $sql .= " AND tipo_discapacidad = ?";
            $params[] = $filtros['tipo_discapacidad'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND fecha_afiliacion >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND fecha_afiliacion <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $sql .= " ORDER BY fecha_registro DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un usuario por ID
    public function obtenerUsuarioPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo usuario
    public function crearUsuario($datos) {
        $sql = "INSERT INTO usuarios (numero_asociado, nombre, apellidos, fecha_nacimiento, 
                genero, tipo_discapacidad, porcentaje_discapacidad, telefono, email, 
                direccion, ciudad, codigo_postal, fecha_afiliacion, estado, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $datos['numero_asociado'],
            $datos['nombre'],
            $datos['apellidos'],
            $datos['fecha_nacimiento'],
            $datos['genero'],
            $datos['tipo_discapacidad'],
            $datos['porcentaje_discapacidad'],
            $datos['telefono'],
            $datos['email'],
            $datos['direccion'],
            $datos['ciudad'],
            $datos['codigo_postal'],
            $datos['fecha_afiliacion'],
            $datos['estado'],
            $datos['observaciones']
        ]);
    }

    // Actualizar usuario
    public function actualizarUsuario($id, $datos) {
        $sql = "UPDATE usuarios SET numero_asociado = ?, nombre = ?, apellidos = ?, 
                fecha_nacimiento = ?, genero = ?, tipo_discapacidad = ?, porcentaje_discapacidad = ?, 
                telefono = ?, email = ?, direccion = ?, ciudad = ?, codigo_postal = ?, 
                fecha_afiliacion = ?, estado = ?, observaciones = ? WHERE id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $datos['numero_asociado'],
            $datos['nombre'],
            $datos['apellidos'],
            $datos['fecha_nacimiento'],
            $datos['genero'],
            $datos['tipo_discapacidad'],
            $datos['porcentaje_discapacidad'],
            $datos['telefono'],
            $datos['email'],
            $datos['direccion'],
            $datos['ciudad'],
            $datos['codigo_postal'],
            $datos['fecha_afiliacion'],
            $datos['estado'],
            $datos['observaciones'],
            $id
        ]);
    }

    // Eliminar usuario
    public function eliminarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Verificar si existe un número de asociado
    public function existeNumeroAsociado($numero_asociado, $id_excluir = null) {
        $sql = "SELECT id FROM usuarios WHERE numero_asociado = ?";
        $params = [$numero_asociado];
        
        if ($id_excluir) {
            $sql .= " AND id != ?";
            $params[] = $id_excluir;
        }
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ? true : false;
    }

    // Obtener estadísticas
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                COUNT(*) as total_usuarios,
                COUNT(CASE WHEN estado = 'Activo' THEN 1 END) as usuarios_activos,
                COUNT(CASE WHEN estado = 'Inactivo' THEN 1 END) as usuarios_inactivos,
                COUNT(CASE WHEN estado = 'Suspendido' THEN 1 END) as usuarios_suspendidos
                FROM usuarios";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener tipos de discapacidad únicos
    public function obtenerTiposDiscapacidad() {
        $sql = "SELECT DISTINCT tipo_discapacidad FROM usuarios ORDER BY tipo_discapacidad";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $tipos_existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Tipos predefinidos
        $tipos_predefinidos = [
            'Discapacidad física',
            'Discapacidad visual',
            'Discapacidad auditiva',
            'Discapacidad intelectual',
            'Discapacidad psicosocial',
            'Discapacidad múltiple',
            'Discapacidad del habla',
            'Discapacidad motora',
            'Discapacidad neurológica',
            'Otra'
        ];
        
        // Combinar tipos predefinidos con los existentes en la base de datos
        $todos_tipos = array_merge($tipos_predefinidos, $tipos_existentes);
        $todos_tipos = array_unique($todos_tipos);
        sort($todos_tipos);
        
        return $todos_tipos;
    }
}
?> 