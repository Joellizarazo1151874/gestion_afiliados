<?php
require_once 'conexion.php';

class Documento {
    private $conexion;
    
    public function __construct() {
        $this->conexion = new Conexion();
    }
    
    /**
     * Obtener todos los tipos de documentos
     */
    public function obtenerTiposDocumentos() {
        $sql = "SELECT * FROM tipos_documentos WHERE activo = 1 ORDER BY orden ASC";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Guardar un documento
     */
    public function guardarDocumento($usuario_id, $tipo_documento, $nombre_archivo, $ruta_archivo) {
        $sql = "INSERT INTO documentos_usuarios (usuario_id, tipo_documento, nombre_archivo, ruta_archivo) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->conectar()->prepare($sql);
        return $stmt->execute([$usuario_id, $tipo_documento, $nombre_archivo, $ruta_archivo]);
    }
    
    /**
     * Obtener documentos de un usuario
     */
    public function obtenerDocumentosUsuario($usuario_id) {
        $sql = "SELECT d.*, td.descripcion, td.obligatorio 
                FROM documentos_usuarios d 
                LEFT JOIN tipos_documentos td ON d.tipo_documento = td.nombre 
                WHERE d.usuario_id = ? 
                ORDER BY td.orden ASC";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener documentos de un usuario (método simple sin JOIN)
     */
    public function obtenerDocumentosUsuarioSimple($usuario_id) {
        $sql = "SELECT * FROM documentos_usuarios WHERE usuario_id = ? ORDER BY fecha_carga DESC";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un documento por ID
     */
    public function obtenerDocumentoPorId($documento_id) {
        $sql = "SELECT * FROM documentos_usuarios WHERE id = ?";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute([$documento_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si un usuario tiene todos los documentos obligatorios
     */
    public function verificarDocumentosCompletos($usuario_id) {
        $sql = "SELECT COUNT(*) as total_obligatorios 
                FROM tipos_documentos 
                WHERE obligatorio = 1 AND activo = 1";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute();
        $total_obligatorios = $stmt->fetch(PDO::FETCH_ASSOC)['total_obligatorios'];
        
        $sql = "SELECT COUNT(*) as total_cargados 
                FROM documentos_usuarios d 
                LEFT JOIN tipos_documentos td ON d.tipo_documento = td.nombre 
                WHERE d.usuario_id = ? AND td.obligatorio = 1";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute([$usuario_id]);
        $total_cargados = $stmt->fetch(PDO::FETCH_ASSOC)['total_cargados'];
        
        return $total_cargados >= $total_obligatorios;
    }
    
    /**
     * Actualizar estado de un documento
     */
    public function actualizarEstadoDocumento($documento_id, $estado, $observaciones = '') {
        $sql = "UPDATE documentos_usuarios SET estado = ?, observaciones = ? WHERE id = ?";
        $stmt = $this->conexion->conectar()->prepare($sql);
        return $stmt->execute([$estado, $observaciones, $documento_id]);
    }
    
    /**
     * Eliminar un documento
     */
    public function eliminarDocumento($documento_id) {
        // Primero obtener la ruta del archivo para eliminarlo físicamente
        $sql = "SELECT ruta_archivo FROM documentos_usuarios WHERE id = ?";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute([$documento_id]);
        $documento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($documento && file_exists($documento['ruta_archivo'])) {
            unlink($documento['ruta_archivo']);
        }
        
        // Eliminar registro de la base de datos
        $sql = "DELETE FROM documentos_usuarios WHERE id = ?";
        $stmt = $this->conexion->conectar()->prepare($sql);
        return $stmt->execute([$documento_id]);
    }
    
    /**
     * Obtener estadísticas de documentos
     */
    public function obtenerEstadisticasDocumentos() {
        $sql = "SELECT 
                    COUNT(*) as total_documentos,
                    SUM(CASE WHEN estado = 'Aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'Rechazado' THEN 1 ELSE 0 END) as rechazados
                FROM documentos_usuarios";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Métodos para documentos de solicitudes
     */
    public function guardarDocumentoSolicitud($solicitud_id, $tipo_documento, $nombre_archivo, $ruta_archivo, $pdo = null) {
        $sql = "INSERT INTO documentos_solicitudes (
            solicitud_id, tipo_documento, nombre_archivo, ruta_archivo
        ) VALUES (?, ?, ?, ?)";
        
        // Usar la conexión proporcionada o crear una nueva
        $conexion = $pdo ?: $this->conexion->conectar();
        $stmt = $conexion->prepare($sql);
        return $stmt->execute([$solicitud_id, $tipo_documento, $nombre_archivo, $ruta_archivo]);
    }
    
    public function obtenerDocumentosSolicitud($solicitud_id) {
        $sql = "SELECT * FROM documentos_solicitudes WHERE solicitud_id = ? ORDER BY fecha_carga";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute([$solicitud_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerDocumentosSolicitudPorEmail($email) {
        $sql = "SELECT ds.* FROM documentos_solicitudes ds 
                INNER JOIN solicitudes_afiliacion sa ON ds.solicitud_id = sa.id 
                WHERE sa.email = ? AND sa.estado = 'Aprobada' 
                ORDER BY ds.fecha_carga";
        $stmt = $this->conexion->conectar()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 