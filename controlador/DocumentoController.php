<?php
require_once '../modelo/Documento.php';

class DocumentoController {
    private $documento;
    
    public function __construct() {
        $this->documento = new Documento();
    }
    
    /**
     * Procesar la carga de documentos
     */
    public function procesarCargaDocumentos($usuario_id, $archivos) {
        $resultado = ['exito' => true, 'mensaje' => '', 'documentos_cargados' => 0];
        
        // Crear directorio si no existe
        $directorio = "../uploads/documentos/";
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        foreach ($archivos as $tipo_documento => $archivo) {
            if ($archivo['error'] === UPLOAD_ERR_OK) {
                // Validar tipo de archivo
                $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                
                if (!in_array($extension, $extensiones_permitidas)) {
                    $resultado['exito'] = false;
                    $resultado['mensaje'] = "Tipo de archivo no permitido para $tipo_documento";
                    continue;
                }
                
                // Validar tamaño (máximo 5MB)
                if ($archivo['size'] > 5 * 1024 * 1024) {
                    $resultado['exito'] = false;
                    $resultado['mensaje'] = "El archivo $tipo_documento es demasiado grande (máximo 5MB)";
                    continue;
                }
                
                // Generar nombre único
                $nombre_archivo = $usuario_id . '_' . $tipo_documento . '_' . time() . '.' . $extension;
                $ruta_completa = $directorio . $nombre_archivo;
                
                // Mover archivo
                if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                    // Guardar en base de datos
                    if ($this->documento->guardarDocumento($usuario_id, $tipo_documento, $archivo['name'], $ruta_completa)) {
                        $resultado['documentos_cargados']++;
                    } else {
                        $resultado['exito'] = false;
                        $resultado['mensaje'] = "Error al guardar $tipo_documento en la base de datos";
                    }
                } else {
                    $resultado['exito'] = false;
                    $resultado['mensaje'] = "Error al subir el archivo $tipo_documento";
                }
            }
        }
        
        if ($resultado['documentos_cargados'] > 0) {
            $resultado['mensaje'] = "Se cargaron {$resultado['documentos_cargados']} documentos exitosamente";
        }
        
        return $resultado;
    }
    
    /**
     * Obtener documentos de un usuario
     */
    public function obtenerDocumentosUsuario($usuario_id) {
        return $this->documento->obtenerDocumentosUsuario($usuario_id);
    }
    
    /**
     * Obtener tipos de documentos
     */
    public function obtenerTiposDocumentos() {
        return $this->documento->obtenerTiposDocumentos();
    }
    
    /**
     * Verificar si un usuario tiene documentos completos
     */
    public function verificarDocumentosCompletos($usuario_id) {
        return $this->documento->verificarDocumentosCompletos($usuario_id);
    }
    
    /**
     * Actualizar estado de documento
     */
    public function actualizarEstadoDocumento($documento_id, $estado, $observaciones = '') {
        return $this->documento->actualizarEstadoDocumento($documento_id, $estado, $observaciones);
    }
    
    /**
     * Eliminar documento
     */
    public function eliminarDocumento($documento_id) {
        return $this->documento->eliminarDocumento($documento_id);
    }
    
    /**
     * Obtener estadísticas de documentos
     */
    public function obtenerEstadisticasDocumentos() {
        return $this->documento->obtenerEstadisticasDocumentos();
    }
    
    /**
     * Descargar documento
     */
    public function descargarDocumento($documento_id) {
        $documentos = $this->documento->obtenerDocumentosUsuario(null);
        foreach ($documentos as $doc) {
            if ($doc['id'] == $documento_id && file_exists($doc['ruta_archivo'])) {
                $extension = pathinfo($doc['ruta_archivo'], PATHINFO_EXTENSION);
                $tipo_mime = $this->obtenerTipoMime($extension);
                
                header('Content-Type: ' . $tipo_mime);
                header('Content-Disposition: attachment; filename="' . $doc['nombre_archivo'] . '"');
                header('Content-Length: ' . filesize($doc['ruta_archivo']));
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: no-cache');
                
                readfile($doc['ruta_archivo']);
                exit();
            }
        }
        return false;
    }
    
    /**
     * Obtener tipo MIME según extensión
     */
    private function obtenerTipoMime($extension) {
        $tipos_mime = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        return isset($tipos_mime[$extension]) ? $tipos_mime[$extension] : 'application/octet-stream';
    }
}
?> 