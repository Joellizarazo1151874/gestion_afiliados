<?php
require_once 'conexion.php';

class Solicitud {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }
    
    // Método público para obtener la conexión
    public function getConexion() {
        return $this->conexion;
    }

    // Crear nueva solicitud
    public function crearSolicitud($datos, $pdo = null) {
        try {
            // Generar número de solicitud
            $numero_solicitud = $this->generarNumeroSolicitud();
            
            $sql = "INSERT INTO solicitudes_afiliacion (
                numero_solicitud, nombre, apellidos, fecha_nacimiento, genero,
                tipo_discapacidad, porcentaje_discapacidad, telefono, email,
                direccion, ciudad, codigo_postal, observaciones
            ) VALUES (
                :numero_solicitud, :nombre, :apellidos, :fecha_nacimiento, :genero,
                :tipo_discapacidad, :porcentaje_discapacidad, :telefono, :email,
                :direccion, :ciudad, :codigo_postal, :observaciones
            )";
            
            // Agregar el número de solicitud a los datos
            $datos['numero_solicitud'] = $numero_solicitud;
            
            // Usar la conexión proporcionada o crear una nueva
            $conexion = $pdo ?: $this->conexion->conectar();
            $stmt = $conexion->prepare($sql);
            $stmt->execute($datos);
            
            return $conexion->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear solicitud: " . $e->getMessage());
        }
    }

    // Obtener todas las solicitudes
    public function obtenerSolicitudes($estado = null, $limit = null) {
        try {
            $sql = "SELECT * FROM solicitudes_afiliacion";
            $params = [];
            
            if ($estado) {
                $sql .= " WHERE estado = :estado";
                $params['estado'] = $estado;
            }
            
            $sql .= " ORDER BY fecha_solicitud DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit";
                $params['limit'] = $limit;
            }
            
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener solicitudes: " . $e->getMessage());
        }
    }

    // Obtener solicitud por ID
    public function obtenerSolicitudPorId($id) {
        try {
            $sql = "SELECT * FROM solicitudes_afiliacion WHERE id = :id";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener solicitud: " . $e->getMessage());
        }
    }

    // Obtener solicitud por número
    public function obtenerSolicitudPorNumero($numero) {
        try {
            $sql = "SELECT * FROM solicitudes_afiliacion WHERE numero_solicitud = :numero";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute(['numero' => $numero]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener solicitud: " . $e->getMessage());
        }
    }

    // Aprobar solicitud
    public function aprobarSolicitud($id, $admin_id) {
        try {
            $sql = "UPDATE solicitudes_afiliacion SET 
                    estado = 'Aprobada', 
                    fecha_revision = NOW(), 
                    revisado_por = :admin_id 
                    WHERE id = :id";
            
            $stmt = $this->conexion->conectar()->prepare($sql);
            return $stmt->execute(['id' => $id, 'admin_id' => $admin_id]);
        } catch (PDOException $e) {
            throw new Exception("Error al aprobar solicitud: " . $e->getMessage());
        }
    }

    // Rechazar solicitud
    public function rechazarSolicitud($id, $admin_id, $motivo) {
        try {
            $sql = "UPDATE solicitudes_afiliacion SET 
                    estado = 'Rechazada', 
                    fecha_revision = NOW(), 
                    revisado_por = :admin_id,
                    motivo_rechazo = :motivo 
                    WHERE id = :id";
            
            $stmt = $this->conexion->conectar()->prepare($sql);
            return $stmt->execute([
                'id' => $id, 
                'admin_id' => $admin_id,
                'motivo' => $motivo
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al rechazar solicitud: " . $e->getMessage());
        }
    }

    // Convertir solicitud a usuario
    public function convertirAUsuario($solicitud_id) {
        try {
            $solicitud = $this->obtenerSolicitudPorId($solicitud_id);
            if (!$solicitud) {
                throw new Exception("Solicitud no encontrada");
            }

            // Verificar si ya existe un usuario con este email
            $sql = "SELECT id FROM usuarios WHERE email = :email ORDER BY id ASC LIMIT 1";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute(['email' => $solicitud['email']]);
            $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario_existente) {
                // Si ya existe un usuario, retornar su ID
                error_log("Usuario ya existe con email {$solicitud['email']}, retornando ID: {$usuario_existente['id']}");
                return $usuario_existente['id'];
            }

            // Si no existe, crear nuevo usuario
            $sql = "INSERT INTO usuarios (
                numero_asociado, nombre, apellidos, fecha_nacimiento, genero,
                tipo_discapacidad, porcentaje_discapacidad, telefono, email,
                direccion, ciudad, codigo_postal, fecha_afiliacion, estado, observaciones
            ) VALUES (
                :numero_asociado, :nombre, :apellidos, :fecha_nacimiento, :genero,
                :tipo_discapacidad, :porcentaje_discapacidad, :telefono, :email,
                :direccion, :ciudad, :codigo_postal, NOW(), 'Activo', :observaciones
            )";
            
            $datos = [
                'numero_asociado' => $this->generarNumeroAsociado(),
                'nombre' => $solicitud['nombre'],
                'apellidos' => $solicitud['apellidos'],
                'fecha_nacimiento' => $solicitud['fecha_nacimiento'],
                'genero' => $solicitud['genero'],
                'tipo_discapacidad' => $solicitud['tipo_discapacidad'],
                'porcentaje_discapacidad' => $solicitud['porcentaje_discapacidad'],
                'telefono' => $solicitud['telefono'],
                'email' => $solicitud['email'],
                'direccion' => $solicitud['direccion'],
                'ciudad' => $solicitud['ciudad'],
                'codigo_postal' => $solicitud['codigo_postal'],
                'observaciones' => $solicitud['observaciones']
            ];
            
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute($datos);
            
            $nuevo_id = $this->conexion->conectar()->lastInsertId();
            error_log("Nuevo usuario creado con ID: {$nuevo_id}");
            return $nuevo_id;
        } catch (PDOException $e) {
            throw new Exception("Error al convertir a usuario: " . $e->getMessage());
        }
    }

    // Generar número de asociado
    private function generarNumeroAsociado() {
        try {
            $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(numero_asociado, 4) AS UNSIGNED)), 0) + 1 as siguiente FROM usuarios";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return 'ASO' . str_pad($resultado['siguiente'], 6, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            throw new Exception("Error al generar número de asociado: " . $e->getMessage());
        }
    }

    // Obtener estadísticas de solicitudes
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        estado,
                        COUNT(*) as total,
                        DATE(fecha_solicitud) as fecha
                    FROM solicitudes_afiliacion 
                    GROUP BY estado, DATE(fecha_solicitud)
                    ORDER BY fecha DESC";
            
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }

    // Verificar si email ya existe
    public function emailExiste($email) {
        try {
            $sql = "SELECT COUNT(*) as total FROM solicitudes_afiliacion WHERE email = :email";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute(['email' => $email]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar email: " . $e->getMessage());
        }
    }

    // Generar número de solicitud
    private function generarNumeroSolicitud() {
        try {
            $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(numero_solicitud, 4) AS UNSIGNED)), 0) + 1 as siguiente FROM solicitudes_afiliacion";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return 'SOL' . str_pad($resultado['siguiente'], 6, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            throw new Exception("Error al generar número de solicitud: " . $e->getMessage());
        }
    }
}
?> 